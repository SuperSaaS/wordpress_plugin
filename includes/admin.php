<?php
/**
 * Admin specific hooks.
 *
 * @package SuperSaaS
 */

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Adds the SuperSaaS settings page.
 */
function supersaas_add_admin_menu()
{
  add_options_page(__('SuperSaaS Settings', 'supersaas'), 'SuperSaaS', 'manage_options', 'supersaas-settings', 'supersaas_options');
}

/**
 * Registers the SuperSaaS settings.
 */
function supersaas_register_settings()
{
  register_setting('supersaas-settings', 'ss_account_name');
  register_setting('supersaas-settings', 'ss_display_choice', array('sanitize_callback' => 'sanitize_display_choice', 'default' => 'regular_btn'));
  register_setting('supersaas-settings', 'ss_autologin_enabled', array('sanitize_callback' => 'sanitize_autologin', 'default' => '1'));
  register_setting('supersaas-settings', 'ss_password'); // NOTE: this is an API KEY, not a user password; the "ss_password" key is used for backwards compatibility
  register_setting('supersaas-settings', 'ss_widget_script');

  register_setting('supersaas-settings', 'ss_schedule');
  register_setting('supersaas-settings', 'ss_button_label');
  register_setting('supersaas-settings', 'ss_button_image');
  register_setting('supersaas-settings', 'ss_domain', 'domain_from_url');
}

function sanitize_autologin($value) { return $value !== "1" ? "0" : "1"; }

function sanitize_display_choice($value)
{
  return in_array($value, array("regular_btn", "popup_btn")) ? $value : "regular_btn";
}

/**
 * Register JS for page
 *
 * @uses "admin_enqueue_scripts" action
 */
function supersaas_register_assets()
{
  wp_register_script("supersaas_custom_js_script", plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), null);
  wp_enqueue_script('supersaas_custom_js_script');
  wp_script_add_data( 'supersaas_custom_js_script', 'crossorigin', 'anonymous' );
}

/**
 * Sanitizes the custom domain settings field.
 *
 * @param string $ss_domain The value of the custom domain.
 *
 * @return string The domain (and port) name part of the URL.
 */
function domain_from_url($ss_domain)
{
  $url_parts = parse_url($ss_domain);
  if (isset($url_parts['host'])) {
    $domain = $url_parts['host'];
    if (isset($url_parts['port'])) {
      $domain .= ':' . $url_parts['port'];
    }

    return $domain;
  } else {
    return $ss_domain;
  }
}

/**
 * Outputs the content of the SuperSaaS options page.
 */
function supersaas_options()
{
  if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.')); // WPCS: XSS.EscapeOutput OK.
  }


  ?>
  <div class="wrap">
    <h2><?php _e('SuperSaaS Settings', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?></h2>

    <form method="post" action="options.php" id="supersaas-options-form">
      <?php settings_fields('supersaas-settings'); ?>
      <p>
        <span style="font-weight: 600; font-size: 14px;">
          <?php _e('SuperSaaS account name', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
        </span>
        <input type="text" name="ss_account_name"
               value="<?php echo get_option('ss_account_name'); // WPCS: XSS.EscapeOutput OK.?>"
               required
        />
        <span class="error-msg error-msg-1 hidden" style="color: red"> <?php _e("Account name can't be blank", 'supersaas'); // WPCS: XSS.EscapeOutput OK.?> </span>
        <span class="error-msg error-msg-2 hidden" style="color: red"> <?php _e("Please provide an account name (not an email)", 'supersaas'); // WPCS: XSS.EscapeOutput OK.?> </span>
        <br/>
      </p>

      <div style="font-weight: 600; font-size: 14px;">
	      <?php _e('How would you like to show your SuperSaaS schedule?', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
      </div>

      <fieldset>
        <legend class="screen-reader-text"><span> <?php _e('SuperSaaS schedule display', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?></span></legend>
        <div>
          <label>
            <input name="ss_display_choice" type="radio" value="regular_btn"
                   class="tog" <?php echo get_option('ss_display_choice') === 'regular_btn' ? 'checked' : ''; // WPCS: XSS.EscapeOutput OK.?>
            />
	          <?php _e('Show a button that forwards the user to my SuperSaaS calendar', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
          </label>
        </div>
        <div>
          <label>
            <input name="ss_display_choice" type="radio" value="popup_btn"
                   class="tog" <?php echo get_option('ss_display_choice') === 'popup_btn' ? 'checked' : ''; // WPCS: XSS.EscapeOutput OK.?>
            />
	          <?php _e('Show a SuperSaaS calendar integrated into my Wordpress site inside a frame or via a pop-up button', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
          </label>
        </div>
      </fieldset>

      <p>
        <label>
          <input type="checkbox" name="ss_autologin_enabled"
                 value="1"
                 <?php echo get_option('ss_autologin_enabled') === '1' ? 'checked' : ''; // WPCS: XSS.EscapeOutput OK.?>
          />
	        <?php _e('If the user is logged in to WordPress, log them in your SuperSaaS account with their WordPress user name', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
        </label>

        <br/>
        <span id="ss_password" class="<?php echo get_option('ss_autologin_enabled') === '1' ? '' : 'hidden' ?>">
          <?php _e("Automatically logging in the user requires your <a href='https://www.supersaas.com/accounts/edit#api_key' target='_blank'>API key</a>", 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>&nbsp;
          <input type="text" name="ss_password" value="<?php echo get_option('ss_password'); // WPCS: XSS.EscapeOutput OK.?>"/>
          <span class="error-msg hidden" style="color: red"> <?php _e("API key can't be blank", 'supersaas'); // WPCS: XSS.EscapeOutput OK.?> </span>
        </span>
      </p>

      <p id="ss_widget_script" class="<?php echo get_option('ss_display_choice') === 'regular_btn' ? 'hidden' : '' ?>">
	      <?php _e("Paste the JavaScript <a href='https://www.supersaas.com/info/doc/integration/integration_with_widget' target='_blank'>widget code</a> generated on the SuperSaaS site.", 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>&nbsp;
        <br/>
        <textarea name="ss_widget_script" rows="9" cols="80" placeholder="<?php _e('Paste the script here', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>">
          <?php echo get_option('ss_widget_script'); // WPCS: XSS.EscapeOutput OK.?>
        </textarea>
        <br/>
        <span class="error-msg hidden" style="color: red">
          <?php _e("Widget script is invalid. Are you sure that you’ve pasted script generated <a href='https://www.supersaas.com/info/doc/integration/integration_with_widget' target='_blank'>here</a>?", 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>&nbsp;
        </span>
      </p>

      <table class="form-table">
        <tr>
          <th scope="row">
            <?php _e('Schedule name', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
          </th>
          <td>
            <input type="text" name="ss_schedule"
                   value="<?php echo get_option('ss_schedule'); // WPCS: XSS.EscapeOutput OK.?>"
            />
            <br/>
            <span class='description'>
              <?php _e("Leave blank for <a href='https://www.supersaas.com/accounts/access#account_list_schedules_1' target='_blank'>default calendar</a> (can be overwritten in shortcode)", 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>&nbsp;
            </span>
          </td>
        </tr>
        <tr id="ss_button_settings" class="<?php echo get_option('ss_display_choice') === 'popup_btn' ? 'hidden' : '' ?>">
          <th scope="row">
	          <?php _e('Button settings', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
            <em>(<?php _e('optional', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>)</em>
          </th>
          <td>
            <input type="text" name="ss_button_label"
                   value="<?php echo get_option('ss_button_label') ? get_option('ss_button_label') : __('Book Now!', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>"
            />
            <br/>
            <span class='description'>
              <?php _e("The text to be displayed on the button, for example 'Create Appointment'.", 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
            </span>
            <br/>
            <input type="text" name="ss_button_image"
                   value="<?php echo get_option('ss_button_image'); // WPCS: XSS.EscapeOutput OK.?>"
            />
            <span class="error-msg hidden" style="color: red"> <?php _e("Link is invalid", 'supersaas'); // WPCS: XSS.EscapeOutput OK.?> </span>
            <br/>
            <span class='description'>
              <?php _e('Location of an image file to use as the button. Can be left blank.', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
            </span>
          </td>
        </tr>

        <tr id="ss_domain" class="<?php echo get_option('ss_display_choice') === 'popup_btn' ? 'hidden' : '' ?>">
          <th scope="row">
            <?php _e('Custom domain name', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
            <em>(<?php _e('optional', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>)</em>
          </th>
          <td>
            <input type="text" name="ss_domain"
                   value="<?php echo get_option('ss_domain'); // WPCS: XSS.EscapeOutput OK.?>"
            />
            <br/>
            <span class='description'>
              <?php _e('If you created a custom domain name that points to SuperSaaS enter it here. Can be left blank.', 'supersaas'); // WPCS: XSS.EscapeOutput OK.?>
            </span>
          </td>
        </tr>
      </table>

      <p class="submit">
        <input type="submit" class="button-primary"
               value="<?php _e('Save Changes'); // WPCS: XSS.EscapeOutput OK.?>"
        />
      </p>
    </form>
  </div>

  <?php
}