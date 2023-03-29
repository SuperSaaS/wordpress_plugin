<?php
/**
 * Shortcode API specific hooks.
 *
 * @package SuperSaaS
 */

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Displays the SuperSaaS button.
 *
 * @param array $atts SuperSaaS shortcode attributes.
 */
function supersaas_button_hook($atts)
{
  global $current_user;
  wp_get_current_user();

  extract(shortcode_atts(
    array(
      'label' => get_option('ss_button_label', ''),
      'image' => get_option('ss_button_image', ''),
	    'options' => '',
      'after' => '',
      'schedule' => '',
    ), $atts, 'supersaas'
  ));

  $account = get_option('ss_account_name');
  $api_key = get_option('ss_password');
  $display_choice = get_option('ss_display_choice');
  $widget_script = get_option('ss_widget_script');
  $default_schedule = get_option('ss_schedule');
  $autologin_enabled = get_option('ss_autologin_enabled');
  // Sanitize options provided via shortcode
  $options = str_replace('\'', '"', $options);
  // Pattern that represents a sequence that specifies both account and schedule to display

  if ($display_choice === 'popup_btn') {
	  $out = '';

		// Match and update schedule
	  preg_match_all("/(?<=\")[0-9]+:\w+(?=\")/i", $widget_script, $id_matches);
    foreach ($id_matches as &$match_value) {
      foreach ($match_value as &$submatch_value) {
        list($id, $name) = explode(':', $submatch_value);
        if ($name !== $account) {
          if (!empty($after)) {
            $widget_script = str_replace($submatch_value, $after, $widget_script);
            $widget_script = str_replace($id, $after, $widget_script);
          }
          if (!empty($schedule)) {
            $widget_script = str_replace($submatch_value, $schedule, $widget_script);
            $widget_script = str_replace($id, $schedule, $widget_script);
          }
          if(empty($schedule) && empty($after) && !empty($default_schedule)) {
            $widget_script = str_replace($submatch_value, $default_schedule, $widget_script);
            $widget_script = str_replace($id, $default_schedule, $widget_script);
          }
        } else {
          $widget_script = str_replace($submatch_value, $name, $widget_script);
        }
      }
    }
		// Match and override options
	  preg_match_all("/SuperSaaS\([\s\S]+\K{[\s\S]*}(?=\))/i", $widget_script, $widget_options_matches);
	  foreach ($widget_options_matches as &$match_value) {
		  foreach ($match_value as &$submatch_value) {
			  if (!empty($options)) {
				  $widget_script = str_replace($submatch_value, $options, $widget_script);
			  }
		  }
	  }
		// If autologin option enabled and current WP user is logged in:
    if($autologin_enabled && $current_user->ID) {
	    // Populate required variables before initializing widget
      $user_login = $current_user->user_login;

      $out .= '<script type="text/javascript">';
      $out .= ' var supersaas_api_user_id = "' . $current_user->ID . 'fk";';
      $out .= ' var supersaas_api_user = {name: "' .
        htmlspecialchars($user_login) . '", full_name: "' .
        htmlspecialchars($current_user->user_firstname . ' ' . $current_user->user_lastname) . '", email: "' .
        htmlspecialchars($current_user->user_email) . '"} ;';
      $out .= ' var supersaas_api_checksum = "' . md5("$account$api_key$user_login") . '";';
      $out .= '</script>';
    }
    $out .= $widget_script;
  }

  if (empty($after) && empty($schedule)) {
    // Can't replace $after in case of $display_choice === 'popup_btn' since
    //  whether its provided is a part of the display logic for the popup
    $after = $default_schedule;
  } else {
    if (!empty($schedule)) {
      $after = $schedule;
    }
  }

  if ($display_choice === 'regular_btn') {
    if ($account && $api_key && $after) {
      if (!$label) {
        $label = __('Book Now!', 'supersaas');
      }

      $domain = get_option('ss_domain');
      $user_login = $current_user->user_login;
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'https://' : 'http://';

      if (!$domain) {
        $api_domain = "http://localhost:3000";
      } elseif (filter_var($domain, FILTER_VALIDATE_URL)) {
        $api_domain = rtrim($domain, '/');
      } else {
        $api_domain = $protocol . rtrim($domain, '/');
      }
      $api_endpoint = $api_domain . '/api/users';

	    // If autologin option enabled and current WP user is logged in:
      if ($current_user->ID && $autologin_enabled) {
	      //  Generate a hidden form with user data
        $account = str_replace(' ', '_', $account);
        $out = '<form method="post" action=' . $api_endpoint . '>';
        $out .= '<input type="hidden" name="account" value="' . $account . '"/>';
        $out .= '<input type="hidden" name="id" value="' . $current_user->ID . 'fk"/>';
        $out .= '<input type="hidden" name="user[name]" value="' . htmlspecialchars($user_login) . '"/>';
        $out .= '<input type="hidden" name="user[full_name]" value="' . htmlspecialchars($current_user->user_firstname . ' ' . $current_user->user_lastname) . '"/>';
        $out .= '<input type="hidden" name="user[email]" value="' . htmlspecialchars($current_user->user_email) . '"/>';
        $out .= '<input type="hidden" name="checksum" value="' . md5("$account$api_key$user_login") . '"/>';
        $out .= '<input type="hidden" name="after" value="' . htmlspecialchars(str_replace(' ', '_', $after)) . '"/>';

        if ($image) {
          $out .= '<input type="image" src="' . $image . '" alt="' . htmlspecialchars($label) . '" name="submit" onclick="return confirmBooking()"/>';
        } else {
          $out .= '<input type="submit" value="' . htmlspecialchars($label) . '" onclick="return confirmBooking()"/>';
        }

        $out .= '</form><script type="text/javascript">function confirmBooking() {';
        $out .= "var reservedWords = ['administrator','supervise','supervisor','superuser','user','admin','supersaas'];";
        $out .= "for (i = 0; i < reservedWords.length; i++) {if (reservedWords[i] === '{$user_login}') {return confirm('";
        $out .= __('Your username is a supersaas reserved word. You might not be able to login. Do you want to continue?', 'supersaas') . "');}}}</script>";
      } else {
        // Show a schedule button as simple link
	      $href = "$api_domain/schedule/$account/$after";
	      if ($image) {
		      $out = '<a href="' . $href . '"><img src="' . $image . '" alt="' . htmlspecialchars($label) . '"/></a>';
	      } else {
		      $out = '<a href="' . $href . '"><button>' . htmlspecialchars($label) . '</button></a>';
	      }
      }
    } else {
      $out = __('(Setup incomplete)', 'supersaas');
    }
  }

  return $out;
}
