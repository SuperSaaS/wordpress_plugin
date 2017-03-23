<?php
/**
 * Shortcode API specific hooks.
 *
 * @package SuperSaaS
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Displays the SuperSaaS button.
 *
 * @param array $atts SuperSaaS shortcode attributes.
 */
function supersaas_button_hook( $atts ) {
	global $current_user;
	get_currentuserinfo();
	if ( ! $current_user->ID ) {
		return '';
	}

	extract(shortcode_atts(
		array(
			'label' => get_option( 'ss_button_label', '' ),
			'image' => get_option( 'ss_button_image', '' ),
			'after' => get_option( 'ss_schedule', '' ),
		), $atts, 'supersaas'
	));

	$account = get_option( 'ss_account_name' );
	$password = get_option( 'ss_password' );

	if ( $account && $password && $after ) {
		if ( ! $label ) {
			$label = __( 'Book Now!', 'supersaas' );
		}

		$domain = get_option( 'ss_domain' );
		$user_login = $current_user->user_login;
		$protocol = $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'https://' : 'http://';

		if ( ! $domain ) {
			$api_endpoint = $protocol . __( 'www.supersaas.com', 'supersaas' ) . '/api/users';
		} elseif ( filter_var( $domain, FILTER_VALIDATE_URL ) ) {
			$api_endpoint = rtrim( $domain, '/' ) . '/api/users';
		} else {
			$api_endpoint = $protocol . rtrim( $domain, '/' ) . '/api/users';
		}

		$account = str_replace( ' ', '_', $account );
		$out = '<form method="post" action=' . $api_endpoint . '>';
		$out .= '<input type="hidden" name="account" value="' . $account . '"/>';
		$out .= '<input type="hidden" name="id" value="' . $current_user->ID . 'fk"/>';
		$out .= '<input type="hidden" name="user[name]" value="' . htmlspecialchars( $user_login ) . '"/>';
		$out .= '<input type="hidden" name="user[full_name]" value="' . htmlspecialchars( $current_user->user_firstname . ' ' . $current_user->user_lastname ) . '"/>';
		$out .= '<input type="hidden" name="user[email]" value="' . htmlspecialchars( $current_user->user_email ) . '"/>';
		$out .= '<input type="hidden" name="checksum" value="' . md5( "$account$password$user_login" ) . '"/>';
		$out .= '<input type="hidden" name="after" value="' . htmlspecialchars( str_replace( ' ', '_', $after ) ) . '"/>';

		if ( $image ) {
			$out .= '<input type="image" src="' . $image . '" alt="' . htmlspecialchars( $label ) . '" name="submit" onclick="return  confirmBooking()"/>';
		} else {
			$out .= '<input type="submit" value="' . htmlspecialchars( $label ) . '" onclick="return confirmBooking()"/>';
		}

		$out .= '</form><script type="text/javascript">function confirmBooking() {';
		$out .= "var reservedWords = ['administrator','supervise','supervisor','superuser','user','admin','supersaas'];";
		$out .= "for (i = 0; i < reservedWords.length; i++) {if (reservedWords[i] === '{$user_login}') {return confirm('";
		$out .= __( 'Your username is a supersaas reserved word. You might not be able to login. Do you want to continue?', 'supersaas' ) . "');}}}</script>";
	} else {
		$out = __( '(Setup incomplete)', 'supersaas' );
	}

	return $out;
}
