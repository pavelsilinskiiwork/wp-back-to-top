<?php
/**
 * [back_to_top] shortcode.
 *
 * @package PavelSilinskii\BackToTop
 */

namespace PavelSilinskii\BackToTop;

defined( 'ABSPATH' ) || exit;

/**
 * Inline (non-fixed) Back to Top button.
 */
class Shortcode {

	/**
	 * Register the shortcode.
	 *
	 * @return void
	 */
	public function register(): void {
		add_shortcode( 'back_to_top', array( $this, 'render' ) );
	}

	/**
	 * Render the inline button.
	 *
	 * Example: [back_to_top text="↑ Back to Top" class="my-btn"]
	 *
	 * @param array<string,string>|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render( $atts ): string {
		$atts = shortcode_atts(
			array(
				'text'  => '',
				'label' => __( 'Back to top', 'pavel-silinskii-back-to-top' ),
				'class' => '',
			),
			is_array( $atts ) ? $atts : array(),
			'back_to_top'
		);

		// Registered on wp_enqueue_scripts with the CSS variables attached.
		wp_enqueue_style( 'psbtt-frontend' );

		$opts    = Settings::get();
		$shape   = in_array( $opts['shape'], array( 'circle', 'square', 'rounded' ), true ) ? $opts['shape'] : 'circle';
		$classes = array( 'psbtt-btn', 'psbtt-inline', 'psbtt-shape-' . $shape );
		foreach ( preg_split( '/\s+/', (string) $atts['class'], -1, PREG_SPLIT_NO_EMPTY ) as $extra ) {
			$classes[] = sanitize_html_class( $extra );
		}

		return sprintf(
			'<button type="button" class="%1$s" onclick="window.scrollTo({top:0,behavior:\'smooth\'})" aria-label="%2$s">%3$s</button>',
			esc_attr( implode( ' ', array_filter( $classes ) ) ),
			esc_attr( $atts['label'] ),
			Icons::render( $opts, (string) $atts['text'] )
		);
	}
}
