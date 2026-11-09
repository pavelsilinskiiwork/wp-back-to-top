<?php
/**
 * Frontend: assets and the floating button.
 *
 * @package PavelSilinskii\BackToTop
 */

namespace PavelSilinskii\BackToTop;

defined( 'ABSPATH' ) || exit;

/**
 * Outputs the fixed Back to Top button on the site.
 */
class Frontend {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_footer', array( $this, 'render_button' ) );
	}

	/**
	 * Whether the floating button should appear on the current request.
	 *
	 * @return bool
	 */
	public function should_display(): bool {
		$opts = Settings::get();

		if ( empty( $opts['enabled'] ) ) {
			return false;
		}

		if ( 'mobile_only' === $opts['show_on'] && ! wp_is_mobile() ) {
			return false;
		}

		if ( 'desktop_only' === $opts['show_on'] && wp_is_mobile() ) {
			return false;
		}

		$post_types = (array) $opts['post_types'];
		if ( ! empty( $post_types ) && ! is_singular( $post_types ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Register the stylesheet (with CSS variables) always, so the shortcode can
	 * use it; enqueue CSS + JS only when the floating button is shown.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		$opts = Settings::get();

		wp_register_style( 'psbtt-frontend', PSBTT_PLUGIN_URL . 'assets/css/psbtt-frontend.css', array(), PSBTT_VERSION );
		wp_add_inline_style( 'psbtt-frontend', self::inline_css( $opts ) );

		if ( ! $this->should_display() ) {
			return;
		}

		wp_enqueue_style( 'psbtt-frontend' );
		wp_enqueue_script( 'psbtt-frontend', PSBTT_PLUGIN_URL . 'assets/js/psbtt-frontend.js', array(), PSBTT_VERSION, true );
		wp_localize_script(
			'psbtt-frontend',
			'psbttConfig',
			array(
				'scrollOffset'   => (int) $opts['scroll_offset'],
				'animationShow'  => sanitize_key( $opts['animation_show'] ),
				'smoothScroll'   => (bool) $opts['smooth_scroll'],
				'scrollDuration' => (int) $opts['scroll_duration'],
			)
		);
	}

	/**
	 * Build the :root CSS variables block plus user custom CSS.
	 *
	 * @param array<string,mixed> $opts Settings.
	 * @return string
	 */
	public static function inline_css( array $opts ): string {
		$d    = Settings::DEFAULTS;
		$vars = array(
			'--psbtt-bg'         => sanitize_hex_color( $opts['bg_color'] ) ?: $d['bg_color'],
			'--psbtt-text'       => sanitize_hex_color( $opts['text_color'] ) ?: $d['text_color'],
			'--psbtt-bg-hover'   => sanitize_hex_color( $opts['bg_color_hover'] ) ?: $d['bg_color_hover'],
			'--psbtt-text-hover' => sanitize_hex_color( $opts['text_color_hover'] ) ?: $d['text_color_hover'],
			'--psbtt-size'       => absint( $opts['size'] ) . 'px',
			'--psbtt-opacity'    => (string) min( 1, max( 0.1, (float) $opts['opacity'] ) ),
			'--psbtt-bottom'     => absint( $opts['bottom_offset'] ) . 'px',
			'--psbtt-side'       => absint( $opts['side_offset'] ) . 'px',
			'--psbtt-z'          => (string) absint( $opts['z_index'] ),
		);

		$css = ':root{';
		foreach ( $vars as $name => $value ) {
			$css .= $name . ':' . $value . ';';
		}
		$css .= '}';

		$custom = trim( wp_strip_all_tags( (string) $opts['custom_css'] ) );
		if ( '' !== $custom ) {
			$css .= "\n" . $custom;
		}

		return $css;
	}

	/**
	 * Print the button in the footer.
	 *
	 * @return void
	 */
	public function render_button(): void {
		if ( ! $this->should_display() ) {
			return;
		}

		$opts    = Settings::get();
		$shape   = in_array( $opts['shape'], array( 'circle', 'square', 'rounded' ), true ) ? $opts['shape'] : 'circle';
		$anim    = in_array( $opts['animation_show'], array( 'fade', 'slide', 'none' ), true ) ? $opts['animation_show'] : 'fade';
		$classes = array( 'psbtt-btn', 'psbtt-shape-' . $shape, 'psbtt-anim-' . $anim );
		if ( 'left' === $opts['position'] ) {
			$classes[] = 'psbtt-pos-left';
		}
		$label = __( 'Back to top', 'pavel-silinskii-back-to-top' );

		ob_start();
		?>
		<button type="button" id="psbtt-btn"
			class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
			title="<?php echo esc_attr( $label ); ?>"
			style="display:none">
			<?php echo Icons::render( $opts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in Icons::render(). ?>
		</button>
		<?php
		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts above.
	}
}
