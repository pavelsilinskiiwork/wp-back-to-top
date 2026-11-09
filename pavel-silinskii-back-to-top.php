<?php
/**
 * Plugin Name:       Pavel Silinskii Back to Top
 * Plugin URI:        https://wordpress.org/plugins/pavel-silinskii-back-to-top/
 * Description:       Lightweight, fully customizable Back to Top button with smooth scroll and accessibility support.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Pavel Silinskii
 * Author URI:        https://profiles.wordpress.org/pavelsilinskii/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pavel-silinskii-back-to-top
 * Domain Path:       /languages
 *
 * @package PavelSilinskii\BackToTop
 */

namespace PavelSilinskii\BackToTop;

defined( 'ABSPATH' ) || exit;

define( 'PSBTT_VERSION', '1.0.0' );
define( 'PSBTT_PLUGIN_FILE', __FILE__ );
define( 'PSBTT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PSBTT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once PSBTT_PLUGIN_DIR . 'includes/class-psbtt-settings.php';
require_once PSBTT_PLUGIN_DIR . 'includes/class-psbtt-icons.php';
require_once PSBTT_PLUGIN_DIR . 'includes/class-psbtt-frontend.php';
require_once PSBTT_PLUGIN_DIR . 'includes/class-psbtt-shortcode.php';
require_once PSBTT_PLUGIN_DIR . 'admin/class-psbtt-admin.php';

/**
 * Save default settings on activation, without overwriting existing ones.
 *
 * @return void
 */
function activate(): void {
	if ( false === get_option( Settings::OPTION ) ) {
		add_option( Settings::OPTION, Settings::DEFAULTS );
	}
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );

add_action(
	'plugins_loaded',
	function (): void {
		load_plugin_textdomain(
			'pavel-silinskii-back-to-top',
			false,
			dirname( plugin_basename( PSBTT_PLUGIN_FILE ) ) . '/languages'
		);

		( new Frontend() )->init();
		add_action( 'init', array( new Shortcode(), 'register' ) );

		if ( is_admin() ) {
			( new Admin() )->init();
		}
	}
);
