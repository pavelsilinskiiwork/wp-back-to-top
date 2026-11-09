<?php
/**
 * Icon markup helpers.
 *
 * @package PavelSilinskii\BackToTop
 */

namespace PavelSilinskii\BackToTop;

defined( 'ABSPATH' ) || exit;

/**
 * Builds the button icon and holds the allowed SVG tag list.
 */
class Icons {

	/**
	 * Default arrow-up SVG.
	 *
	 * @return string
	 */
	public static function arrow(): string {
		return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><polyline points="18 15 12 9 6 15"></polyline></svg>';
	}

	/**
	 * Allowed tags/attributes for user-supplied SVG.
	 *
	 * @return array<string,array<string,bool>>
	 */
	public static function allowed_svg(): array {
		$common = array(
			'class'           => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'transform'       => true,
			'opacity'         => true,
		);

		return array(
			'svg'      => $common + array(
				'xmlns'       => true,
				'viewbox'     => true,
				'width'       => true,
				'height'      => true,
				'aria-hidden' => true,
				'focusable'   => true,
				'role'        => true,
			),
			'g'        => $common,
			'path'     => $common + array( 'd' => true ),
			'polyline' => $common + array( 'points' => true ),
			'polygon'  => $common + array( 'points' => true ),
			'line'     => $common + array(
				'x1' => true,
				'y1' => true,
				'x2' => true,
				'y2' => true,
			),
			'circle'   => $common + array(
				'cx' => true,
				'cy' => true,
				'r'  => true,
			),
			'rect'     => $common + array(
				'x'      => true,
				'y'      => true,
				'width'  => true,
				'height' => true,
				'rx'     => true,
				'ry'     => true,
			),
		);
	}

	/**
	 * Icon HTML for the given settings, already escaped.
	 *
	 * @param array<string,mixed> $opts Settings.
	 * @param string              $text Optional text override.
	 * @return string
	 */
	public static function render( array $opts, string $text = '' ): string {
		if ( '' !== $text ) {
			return '<span>' . esc_html( $text ) . '</span>';
		}

		switch ( $opts['icon_type'] ) {
			case 'text':
				return '<span aria-hidden="true">' . esc_html( (string) $opts['icon_text'] ) . '</span>';
			case 'custom_svg':
				$svg = wp_kses( (string) $opts['custom_svg'], self::allowed_svg() );
				return '' !== trim( $svg ) ? $svg : self::arrow();
			default:
				return self::arrow();
		}
	}
}
