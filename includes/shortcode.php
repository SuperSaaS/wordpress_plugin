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
  if (!$current_user->ID) {
    return '';
  }

  extract(shortcode_atts(
    array(
      'label' => get_option('ss_button_label', ''),
      'image' => get_option('ss_button_image', ''),
      'after' => '',
      'schedule' => '',
    ), $atts, 'supersaas'
  ));

  $account = get_option('ss_account_name');
  $api_key = get_option('ss_password');
  $display_choice = get_option('ss_display_choice');
  $widget_script = get_option('ss_widget_script');
  $default_schedule = get_option('ss_schedule');

  $pattern = "/(?<=\")[0-9]+:\w+(?=\")/i";
  preg_match_all($pattern, $widget_script, $matches);

  if ($display_choice === 'popup_btn') {
    $out = '<div>';
    foreach ($matches as &$match_value) {
      foreach ($match_value as &$submatch_value) {
        $out .= '<p>';
        list($id, $name) = explode(':', $submatch_value);
        if ($name !== $account) {
          if (!empty($after)) {
            // $out .= '<p> ' . '$after: ' . $after . '</p>';
            $widget_script = str_replace($submatch_value, $after, $widget_script);
            $widget_script = str_replace($id, $after, $widget_script);
          }
          if (!empty($schedule)) {
            // $out .= '<p> ' . '$schedule: ' . $schedule . '</p>';
            $widget_script = str_replace($submatch_value, $schedule, $widget_script);
            $widget_script = str_replace($id, $after, $widget_script);
          }
        }
        // $out .= 'extracted id: ' . $id . ' extracted name: ' . $name;
        $out .= '</p>';
      }
    }
    $out .= '</div>';
    $out .= $widget_script;
  }

  if (empty($after)) {
    // Can't replace $after in case of $display_choice === 'popup_btn' since
    //  whether its provided is a part of the display logic for the popup
    $after = $default_schedule;
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
        $api_endpoint = 'https://' . __('www.supersaas.com', 'supersaas') . '/api/users';
      } elseif (filter_var($domain, FILTER_VALIDATE_URL)) {
        $api_endpoint = rtrim($domain, '/') . '/api/users';
      } else {
        $api_endpoint = $protocol . rtrim($domain, '/') . '/api/users';
      }

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
        $out .= '<input type="image" src="' . $image . '" alt="' . htmlspecialchars($label) . '" name="submit" onclick="return  confirmBooking()"/>';
      } else {
        $out .= '<input type="submit" value="' . htmlspecialchars($label) . '" onclick="return confirmBooking()"/>';
      }

      $out .= '</form><script type="text/javascript">function confirmBooking() {';
      $out .= "var reservedWords = ['administrator','supervise','supervisor','superuser','user','admin','supersaas'];";
      $out .= "for (i = 0; i < reservedWords.length; i++) {if (reservedWords[i] === '{$user_login}') {return confirm('";
      $out .= __('Your username is a supersaas reserved word. You might not be able to login. Do you want to continue?', 'supersaas') . "');}}}</script>";
    } else {
      $out = __('(Setup incomplete)', 'supersaas');
    }
  }

  return $out;
}
