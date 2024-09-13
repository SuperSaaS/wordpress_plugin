<?php
/**
 * SuperSaaS Login.
 *
 * @package SuperSaaS
 * @version 2.1.11
 */

/*
Plugin Name: SuperSaaS Login
Plugin URI:  http://www.supersaas.com/tutorials/wordpress_appointment_scheduling
Description: This module displays a 'Book now' button that automatically logs the user into a SuperSaaS schedule using his WordPress user name. It passes the user's information along, creating or updating the user's information on SuperSaaS as needed. This saves users from having to log in twice. Works with both the free and paid versions of SuperSaaS.
Version:     2.1.11
Author:      SuperSaaS
Author URI:  http://www.supersaas.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: supersaas

SuperSaaS Login is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

SuperSaaS Login is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SuperSaaS Login. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


/**
 * Block direct access.
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Plugin specific constants.
 */
define( 'SS_INCLUDES_DIR', plugin_dir_path( __FILE__ ) . 'includes' );
define( 'SS_LANG_DIR', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Require the needed PHP files.
 */
require_once SS_INCLUDES_DIR . '/admin.php';
require_once SS_INCLUDES_DIR . '/shortcode.php';

/**
 * Load the plugin translations.
 */
load_plugin_textdomain( 'supersaas', false, SS_LANG_DIR );

/**
 * Add a hook for the supersaas shortcode tag.
 */
add_shortcode( 'supersaas', 'supersaas_button_hook' );

/**
 * Add the SuperSaaS admin menu and register the SuperSaaS options.
 */
add_action( 'admin_menu', 'supersaas_add_admin_menu' );
add_action( 'admin_enqueue_scripts', 'supersaas_register_assets' );
add_action( 'admin_init', 'supersaas_register_settings' );
