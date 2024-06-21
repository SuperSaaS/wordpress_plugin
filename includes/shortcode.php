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
function generate_schedule_link($api_domain, $account, $final_schedule_name, $label, $image)
{
  $href = "$api_domain/schedule/$account/$final_schedule_name";
  if(strpos($final_schedule_name, "http") === 0) {
    $href = $final_schedule_name;
  }
  if(strpos($final_schedule_name, "/") === 0) {
    $href = "$api_domain$final_schedule_name";
  }
  if ($image) {
    return '<a href="' . $href . '"><img class="supersaas-confirm" src="' . $image . '" alt="' . htmlspecialchars($label) . '"/></a>';
  } else {
    return '<a href="' . $href . '"><button class="supersaas-confirm">' . htmlspecialchars($label) . '</button></a>';
  }
}

function supersaas_button_hook($atts)
{
  global $current_user;
  wp_get_current_user();

  $defaults_array = array(
    'label' => get_option('ss_button_label', ''),
    'image' => get_option('ss_button_image', ''),
    'options' => '',
    'after' => '',
    'schedule' => '',
  );

  extract(shortcode_atts( $defaults_array, $atts, 'supersaas'));

  $account = get_option('ss_account_name');
  $api_key = get_option('ss_password');
  // Backward compatibility for users who will update the plugin without updating settings
  $display_choice = get_option('ss_display_choice', 'regular_btn');
  $widget_script = get_option('ss_widget_script');
  $default_schedule = trim(get_option('ss_schedule')); // remove trailing spaces
  $default_schedule = str_replace(' ', '_', $default_schedule);
  $autologin_enabled = get_option('ss_autologin_enabled'); // one of the following: ("0", "1", "")
  $out = '';

  // Sanitize image
  if ($image) {
    $image = esc_url_raw($image);
  }

  // Sanitize options provided via shortcode
  $options = str_replace('\'', '"', $options);
  $options_obj = json_decode($options);
  if($options && !$options_obj) {
    // Validate options provided via shortcode
    $out .= "<p>" . __('Error occurred while parsing options. Did you provide options json properly?', 'supersaas') . "<br/> ";
    $out .= __('Example', 'supersaas') . ": <code> [supersaas options=\"{'menu':'show','view':'card'}\"] </code> </p>";
    return $out;
  }

  // Determine a final name of the schedule
  $final_schedule_name = '';
  if (!empty($after)) {
    $final_schedule_name = $after;
  }
  if (!empty($schedule)) {
    $final_schedule_name = $schedule;
  }
  if(empty($schedule) && empty($after) && !empty($default_schedule)) {
    $final_schedule_name = $default_schedule;
  }

  if ($display_choice === 'popup_btn') {
    if(!empty($final_schedule_name)) {
      // Match and replace {account_id:account_name} with {account_name} to trigger behaviour where
      //  schedule name can be passed without id
      preg_match_all("/(?<=SuperSaaS\(\")[0-9]+:\w+(?=\")/i", $widget_script, $id_matches);
      foreach ($id_matches as &$match_value) {
        foreach ($match_value as &$submatch_value) {
          list($account_id, $name) = explode(':', $submatch_value);
          $widget_script = str_replace($submatch_value, $name, $widget_script);
        }
      }

      // Match and update schedule (unless null is provided)
      preg_match_all("/(?<=,\")[0-9]+:\w+(?=\")/i", $widget_script, $id_matches);
      foreach ($id_matches as &$match_value) {
        foreach ($match_value as &$submatch_value) {
          list($schedule_id, $name) = explode(':', $submatch_value);
          $widget_script = str_replace($submatch_value, $final_schedule_name, $widget_script);
          $widget_script = str_replace($schedule_id, $final_schedule_name, $widget_script);
        }
      }

      // Match and update schedule (when null is provided)
      $widget_script = preg_replace("/SuperSaaS\([\s\S]*\Knull/i", "\"$final_schedule_name\"", $widget_script);
    } else {
      // When final schedule name is empty, trigger a default behaviour for `/schedule/{account_name}`
      // which here basically mean - clean the schedule name from $widget_script if provided
      preg_match_all("/(?<=,)\"[0-9]+:\w+\"/i", $widget_script, $id_matches);
      foreach ($id_matches as &$match_value) {
        foreach ($match_value as &$submatch_value) {
          list($schedule_id, $name) = explode(':', $submatch_value);
          $widget_script = str_replace($submatch_value, "null", $widget_script);
          $widget_script = str_replace($schedule_id, "null", $widget_script);
        }
      }
    }

    // Match and override widget options
    preg_match_all("/SuperSaaS\([\s\S]+\K{[\s\S]*}(?=\))/i", $widget_script, $widget_options_matches);
    foreach ($widget_options_matches as &$match_value) {
      foreach ($match_value as &$submatch_value) {
        $default_options_obj = json_decode($submatch_value, true);
        $options_final = $default_options_obj;
        if (!empty($options)) {
          // Merge options provided in widget_script with options provided via shortcode
          $options_final = array_merge((array) $default_options_obj, (array) $options_obj);
        }

        if(gettype($atts) == "array") {
          foreach ($atts as $key => $value) {
            // Consider any non-recognized shortcode attribute key as an override to widget options
            if(!in_array($key, array_keys($defaults_array))) {
              $options_final[$key] = $value;
            }
          }
        }
        $options = json_encode($options_final);
        $widget_script = str_replace($submatch_value, $options, $widget_script);
      }
    }

    // Set button text if provided:
    $widget_script = preg_replace("/(?<=\>)[\w\d\s]*(?=\<\/button>)/i", $label, $widget_script);

    // If autologin option enabled and current WP user is logged-in:
    if(!empty($api_key) && $current_user->ID) {
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

  if ($display_choice === 'regular_btn' || empty($display_choice)) {
    if ($account) {
      if (!$label) {
        $label = __('Book Now!', 'supersaas');
      }

      $domain = get_option('ss_domain');
      $user_login = $current_user->user_login;
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'https://' : 'http://';

      if (!$domain) {
        $api_domain = 'https://' . __('www.supersaas.com', 'supersaas');
      } elseif (filter_var($domain, FILTER_VALIDATE_URL)) {
        $api_domain = rtrim($domain, '/');
      } else {
        $api_domain = $protocol . rtrim($domain, '/');
      }
      $api_endpoint = $api_domain . '/api/users';

      if ($current_user->ID) {
        // User is logged-in
        if($autologin_enabled !== "0") {
          // Autologin isn't explicitly disabled (manually enabled or upgrading user)
          if($api_key) {
            // If API key is present and 'autologin' isn't explicitly disabled:
            //  Generate a hidden form with user data
            $account = str_replace(' ', '_', $account);
            $out .= '<form method="post" action=' . $api_endpoint . '>';
            $out .= '<input type="hidden" name="account" value="' . $account . '"/>';
            $out .= '<input type="hidden" name="id" value="' . $current_user->ID . 'fk"/>';
            $out .= '<input type="hidden" name="user[name]" value="' . htmlspecialchars($user_login) . '"/>';
            $out .= '<input type="hidden" name="user[full_name]" value="' . htmlspecialchars($current_user->user_firstname . ' ' . $current_user->user_lastname) . '"/>';
            $out .= '<input type="hidden" name="user[email]" value="' . htmlspecialchars($current_user->user_email) . '"/>';
            $out .= '<input type="hidden" name="checksum" value="' . md5("$account$api_key$user_login") . '"/>';
            $out .= '<input type="hidden" name="after" value="' . htmlspecialchars(str_replace(' ', '_', $final_schedule_name)) . '"/>';

            if ($image) {
              $out .= '<input class="supersaas-confirm" type="image" src="' . $image . '" alt="' . htmlspecialchars($label) . '" name="submit" onclick="return confirmBooking()"/>';
            } else {
              $out .= '<input class="supersaas-confirm" type="submit" value="' . htmlspecialchars($label) . '" onclick="return confirmBooking()"/>';
            }

            $out .= '</form><script type="text/javascript">function confirmBooking() {';
            $out .= "var reservedWords = ['administrator','supervise','supervisor','superuser','user','admin','supersaas'];";
            $out .= "for (i = 0; i < reservedWords.length; i++) {if (reservedWords[i] === '{$user_login}') {return confirm('";
            $out .= __('Your username is a SuperSaaS reserved word. You might not be able to log in. Do you want to continue?', 'supersaas') . "');}}}</script>";
          } else {
            $out .= __('(Setup incomplete)', 'supersaas');
          }
        } else {
          // Show a link to a schedule for logged-in users with 'autologin' explicitly disabled
          $out .= generate_schedule_link($api_domain, $account, $final_schedule_name, $label, $image);
        }
      } else {
        // User is non-logged-in
        if($autologin_enabled === "0") {
          // Show a link to a schedule for non-logged-in users with 'autologin' explicitly disabled
          $out .= generate_schedule_link($api_domain, $account, $final_schedule_name, $label, $image);
        }
        // Show nothing to non-logged-in users when 'autologin' isn't explicitly disabled
      }
    } else {
      $out .= __('(Setup incomplete)', 'supersaas');
    }
  }

  return $out;
}
