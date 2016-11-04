<?php
/**
 * Admin specific hooks.
 *
 * @package SuperSaaS
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Adds the SuperSaaS settings page.
 */
function supersaas_add_admin_menu() {
	add_options_page( __( 'SuperSaaS Settings', 'supersaas' ), 'SuperSaaS', 'manage_options', 'supersaas-settings', 'supersaas_options' );
}

/**
 * Registers the SuperSaaS settings.
 */
function supersaas_register_settings() {
	register_setting( 'supersaas-settings', 'ss_account_name' );
	register_setting( 'supersaas-settings', 'ss_password' );
	register_setting( 'supersaas-settings', 'ss_schedule' );
	register_setting( 'supersaas-settings', 'ss_button_label' );
	register_setting( 'supersaas-settings', 'ss_button_image' );
	register_setting( 'supersaas-settings', 'ss_domain', 'domain_from_url' );
}

/**
 * Sanitizes the custom domain settings field.
 *
 * @param string $ss_domain The value of the custom domain.
 *
 * @return string The domain (and port) name part of the URL.
 */
function domain_from_url($ss_domain) {
	$url_parts = parse_url( $ss_domain );
	if ( isset( $url_parts['host'] ) ) {
		$domain = $url_parts['host'];
		if ( isset( $url_parts['port'] ) ) {
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
function supersaas_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) ); // WPCS: XSS.EscapeOutput OK.
	}

?>
<div class="wrap">
	<h2><?php _e( 'SuperSaaS Settings', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?></h2>

	<form method="post" action="options.php">
	<?php settings_fields( 'supersaas-settings' ); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<?php _e( 'SuperSaaS account name', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?>
				<em>*</em>
			</th>
			<td>
				<input type="text" name="ss_account_name" value="<?php echo get_option( 'ss_account_name' ); // WPCS: XSS.EscapeOutput OK. ?>" required /><br />
				<span class='description'><?php _e( "The account name of your SuperSaaS account. If you don't have an account name yet then please create one at supersaas.com.", 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?></span>
			</td>
	 	</tr>

		<tr valign="top">
			<th scope="row">
				<?php _e( 'SuperSaaS password', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?>
				<em>*</em>
			</th>
			<td>
				<input type="password" name="ss_password" value="<?php echo get_option( 'ss_password' ); // WPCS: XSS.EscapeOutput OK. ?>" required /><br />
		 		<span class='description'><?php _e( 'The password for the administrator of your SuperSaaS account.', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<?php _e( 'Schedule name', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?>
			</th>
			<td>
				<input type="text" name="ss_schedule" value="<?php echo get_option( 'ss_schedule' ); // WPCS: XSS.EscapeOutput OK. ?>" /><br />
	  			<span class='description'><?php _e( 'The name of the schedule or URL to redirect to after login.', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?></span>
			</td>
	  	</tr>

		<tr valign="top">
			<th scope="row">
				<?php _e( 'Button Label', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?>
				<em>(<?php _e( 'optional', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?>)</em>
			</th>
			<td>
				<input type="text" name="ss_button_label" value="<?php echo get_option( 'ss_button_label' ) ? get_option( 'ss_button_label' ) : __( 'Book Now!', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?>" /><br />
	  			<span class='description'><?php _e( "The text to be put on the button that is displayed, for example 'Create Appointment'.", 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?></span>
			</td>
	  	</tr>

		<tr valign="top">
			<th scope="row">
				<?php _e( 'Button Image', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?>
				<em>(<?php _e( 'optional', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?>)</em>
			</th>
			<td>
				<input type="text" name="ss_button_image" value="<?php echo get_option( 'ss_button_image' ); // WPCS: XSS.EscapeOutput OK. ?>" /><br />
				<span class='description'><?php _e( 'Location of an image file to use as the button. Can be left blank.', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?></span>
			</td>
	 	 </tr>

		<tr valign="top">
			<th scope="row">
				<?php _e( 'Custom domain name', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?>
				<em>(<?php _e( 'optional', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?>)</em>
			</th>
			<td>
				<input type="text" name="ss_domain" value="<?php echo get_option( 'ss_domain' ); // WPCS: XSS.EscapeOutput OK. ?>" /><br />
				<span class='description'><?php _e( 'If you created a custom domain name that points to SuperSaaS enter it here. Can be left blank.', 'supersaas' ); // WPCS: XSS.EscapeOutput OK. ?></span>
			</td>
  		</tr>
	</table>

	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ); // WPCS: XSS.EscapeOutput OK. ?>" />
	</p>
  </form>
</div>

<?php
}
