<?php
/**
 * Settings defaults and accessor.
 *
 * @package PavelSilinskii\BackToTop
 */

namespace PavelSilinskii\BackToTop;

defined( 'ABSPATH' ) || exit;

/**
 * Holds option name, defaults and the merged settings getter.
 */
class Settings {

	const OPTION = 'psbtt_settings';

	const DEFAULTS = array(
		'enabled'          => true,
		'scroll_offset'    => 300,
		'position'         => 'right',
		'bottom_offset'    => 30,
		'side_offset'      => 30,
		'shape'            => 'circle',
		'size'             => 48,
		'bg_color'         => '#1a1a2e',
		'text_color'       => '#ffffff',
		'bg_color_hover'   => '#6c63ff',
		'text_color_hover' => '#ffffff',
		'opacity'          => 0.85,
		'icon_type'        => 'arrow',
		'icon_text'        => '↑',
		'custom_svg'       => '',
		'animation_show'   => 'fade',
		'smooth_scroll'    => true,
		'scroll_duration'  => 400,
		'show_on'          => 'all',
		'post_types'       => array(),
		'z_index'          => 9999,
		'custom_css'       => '',
	);

	/**
	 * Saved settings merged over defaults.
	 *
	 * @return array<string,mixed>
	 */
	public static function get(): array {
		$saved = get_option( self::OPTION, array() );
		return array_merge( self::DEFAULTS, is_array( $saved ) ? $saved : array() );
	}
}
