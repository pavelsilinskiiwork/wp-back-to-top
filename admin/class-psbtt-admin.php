<?php
/**
 * Admin settings page (Settings API).
 *
 * @package PavelSilinskii\BackToTop
 */

namespace PavelSilinskii\BackToTop;

defined( 'ABSPATH' ) || exit;

/**
 * Registers Settings → Back to Top.
 */
class Admin {

	const PAGE  = 'psbtt-settings';
	const GROUP = 'psbtt_settings_group';

	/**
	 * Hook suffix returned by add_options_page().
	 *
	 * @var string
	 */
	private string $hook = '';

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( PSBTT_PLUGIN_FILE ), array( $this, 'action_links' ) );
	}

	/**
	 * Add Settings link on the Plugins screen.
	 *
	 * @param array<int|string,string> $links Existing links.
	 * @return array<int|string,string>
	 */
	public function action_links( array $links ): array {
		$url = admin_url( 'options-general.php?page=' . self::PAGE );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'pavel-silinskii-back-to-top' ) . '</a>' );
		return $links;
	}

	/**
	 * Register the options page.
	 *
	 * @return void
	 */
	public function add_menu_page(): void {
		$this->hook = (string) add_options_page(
			__( 'Back to Top', 'pavel-silinskii-back-to-top' ),
			__( 'Back to Top', 'pavel-silinskii-back-to-top' ),
			'manage_options',
			self::PAGE,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the settings page template.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		include PSBTT_PLUGIN_DIR . 'admin/partials/psbtt-settings-page.php';
	}

	/**
	 * Register setting, sections and fields.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting(
			self::GROUP,
			Settings::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => Settings::DEFAULTS,
			)
		);

		$sections = array(
			'psbtt_general'  => __( 'General', 'pavel-silinskii-back-to-top' ),
			'psbtt_button'   => __( 'Button Style', 'pavel-silinskii-back-to-top' ),
			'psbtt_behavior' => __( 'Behavior', 'pavel-silinskii-back-to-top' ),
			'psbtt_advanced' => __( 'Advanced', 'pavel-silinskii-back-to-top' ),
		);
		foreach ( $sections as $id => $title ) {
			add_settings_section( $id, $title, '__return_false', self::PAGE );
		}

		foreach ( $this->fields() as $key => $field ) {
			add_settings_field(
				'psbtt_' . $key,
				$field['label'],
				array( $this, 'render_field' ),
				self::PAGE,
				$field['section'],
				array(
					'key'       => $key,
					'field'     => $field,
					'label_for' => in_array( $field['type'], array( 'radio', 'post_types' ), true ) ? '' : 'psbtt_' . $key,
					'class'     => 'psbtt-row psbtt-row-' . $key,
				)
			);
		}
	}

	/**
	 * Field definitions.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private function fields(): array {
		return array(
			// General.
			'enabled'          => array(
				'section' => 'psbtt_general',
				'label'   => __( 'Enable button', 'pavel-silinskii-back-to-top' ),
				'type'    => 'checkbox',
				'text'    => __( 'Show the Back to Top button on the site', 'pavel-silinskii-back-to-top' ),
			),
			'show_on'          => array(
				'section' => 'psbtt_general',
				'label'   => __( 'Show on', 'pavel-silinskii-back-to-top' ),
				'type'    => 'radio',
				'choices' => array(
					'all'          => __( 'All devices', 'pavel-silinskii-back-to-top' ),
					'mobile_only'  => __( 'Mobile only', 'pavel-silinskii-back-to-top' ),
					'desktop_only' => __( 'Desktop only', 'pavel-silinskii-back-to-top' ),
				),
			),
			'post_types'       => array(
				'section' => 'psbtt_general',
				'label'   => __( 'Limit to post types', 'pavel-silinskii-back-to-top' ),
				'type'    => 'post_types',
				'desc'    => __( 'Leave all unchecked to show the button on every page.', 'pavel-silinskii-back-to-top' ),
			),
			// Button style.
			'position'         => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Position', 'pavel-silinskii-back-to-top' ),
				'type'    => 'radio',
				'choices' => array(
					'right' => __( 'Right', 'pavel-silinskii-back-to-top' ),
					'left'  => __( 'Left', 'pavel-silinskii-back-to-top' ),
				),
			),
			'shape'            => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Shape', 'pavel-silinskii-back-to-top' ),
				'type'    => 'radio',
				'shapes'  => true,
				'choices' => array(
					'circle'  => __( 'Circle', 'pavel-silinskii-back-to-top' ),
					'rounded' => __( 'Rounded', 'pavel-silinskii-back-to-top' ),
					'square'  => __( 'Square', 'pavel-silinskii-back-to-top' ),
				),
			),
			'size'             => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Size', 'pavel-silinskii-back-to-top' ),
				'type'    => 'range',
				'min'     => 32,
				'max'     => 80,
				'step'    => 4,
				'unit'    => 'px',
			),
			'bottom_offset'    => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Bottom offset', 'pavel-silinskii-back-to-top' ),
				'type'    => 'number',
				'min'     => 0,
				'max'     => 500,
				'unit'    => 'px',
			),
			'side_offset'      => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Side offset', 'pavel-silinskii-back-to-top' ),
				'type'    => 'number',
				'min'     => 0,
				'max'     => 500,
				'unit'    => 'px',
			),
			'bg_color'         => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Background', 'pavel-silinskii-back-to-top' ),
				'type'    => 'color',
			),
			'text_color'       => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Icon color', 'pavel-silinskii-back-to-top' ),
				'type'    => 'color',
			),
			'bg_color_hover'   => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Background (hover)', 'pavel-silinskii-back-to-top' ),
				'type'    => 'color',
			),
			'text_color_hover' => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Icon color (hover)', 'pavel-silinskii-back-to-top' ),
				'type'    => 'color',
			),
			'opacity'          => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Opacity', 'pavel-silinskii-back-to-top' ),
				'type'    => 'range',
				'min'     => 0.1,
				'max'     => 1,
				'step'    => 0.05,
				'unit'    => '',
			),
			'icon_type'        => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Icon', 'pavel-silinskii-back-to-top' ),
				'type'    => 'radio',
				'choices' => array(
					'arrow'      => __( 'Arrow SVG', 'pavel-silinskii-back-to-top' ),
					'text'       => __( 'Text', 'pavel-silinskii-back-to-top' ),
					'custom_svg' => __( 'Custom SVG', 'pavel-silinskii-back-to-top' ),
				),
			),
			'icon_text'        => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Icon text', 'pavel-silinskii-back-to-top' ),
				'type'    => 'text',
			),
			'custom_svg'       => array(
				'section' => 'psbtt_button',
				'label'   => __( 'Custom SVG', 'pavel-silinskii-back-to-top' ),
				'type'    => 'textarea',
				'rows'    => 5,
				'desc'    => __( 'Paste inline SVG markup. Use stroke/fill "currentColor" so the icon follows the color settings.', 'pavel-silinskii-back-to-top' ),
			),
			// Behavior.
			'scroll_offset'    => array(
				'section' => 'psbtt_behavior',
				'label'   => __( 'Show after scrolling', 'pavel-silinskii-back-to-top' ),
				'type'    => 'number',
				'min'     => 0,
				'max'     => 10000,
				'unit'    => 'px',
			),
			'animation_show'   => array(
				'section' => 'psbtt_behavior',
				'label'   => __( 'Show animation', 'pavel-silinskii-back-to-top' ),
				'type'    => 'select',
				'choices' => array(
					'fade'  => __( 'Fade', 'pavel-silinskii-back-to-top' ),
					'slide' => __( 'Slide', 'pavel-silinskii-back-to-top' ),
					'none'  => __( 'None', 'pavel-silinskii-back-to-top' ),
				),
			),
			'smooth_scroll'    => array(
				'section' => 'psbtt_behavior',
				'label'   => __( 'Smooth scroll', 'pavel-silinskii-back-to-top' ),
				'type'    => 'checkbox',
				'text'    => __( 'Animate scrolling to the top', 'pavel-silinskii-back-to-top' ),
			),
			'scroll_duration'  => array(
				'section' => 'psbtt_behavior',
				'label'   => __( 'Scroll duration', 'pavel-silinskii-back-to-top' ),
				'type'    => 'number',
				'min'     => 100,
				'max'     => 3000,
				'step'    => 50,
				'unit'    => 'ms',
				'desc'    => __( 'Used only by browsers without native smooth scrolling.', 'pavel-silinskii-back-to-top' ),
			),
			'z_index'          => array(
				'section' => 'psbtt_behavior',
				'label'   => __( 'z-index', 'pavel-silinskii-back-to-top' ),
				'type'    => 'number',
				'min'     => 0,
				'max'     => 2147483647,
			),
			// Advanced.
			'custom_css'       => array(
				'section' => 'psbtt_advanced',
				'label'   => __( 'Custom CSS', 'pavel-silinskii-back-to-top' ),
				'type'    => 'textarea',
				'rows'    => 6,
				'code'    => true,
				'desc'    => __( 'Extra CSS printed after the button styles, e.g. #psbtt-btn { box-shadow: none; }', 'pavel-silinskii-back-to-top' ),
			),
		);
	}

	/**
	 * Render one field.
	 *
	 * @param array<string,mixed> $args Field args.
	 * @return void
	 */
	public function render_field( array $args ): void {
		$key   = $args['key'];
		$field = $args['field'];
		$opts  = Settings::get();
		$value = $opts[ $key ];
		$id    = 'psbtt_' . $key;
		$name  = Settings::OPTION . '[' . $key . ']';

		switch ( $field['type'] ) {
			case 'checkbox':
				printf(
					'<input type="hidden" name="%1$s" value="0"><label><input type="checkbox" id="%2$s" name="%1$s" value="1" %3$s> %4$s</label>',
					esc_attr( $name ),
					esc_attr( $id ),
					checked( ! empty( $value ), true, false ),
					esc_html( $field['text'] )
				);
				break;

			case 'radio':
				$wrap_class = empty( $field['shapes'] ) ? 'psbtt-radios' : 'psbtt-radios psbtt-shape-radios';
				echo '<fieldset class="' . esc_attr( $wrap_class ) . '"><legend class="screen-reader-text">' . esc_html( $field['label'] ) . '</legend>';
				foreach ( $field['choices'] as $choice => $label ) {
					printf(
						'<label class="psbtt-radio"><input type="radio" name="%1$s" value="%2$s" %3$s>%4$s<span>%5$s</span></label>',
						esc_attr( $name ),
						esc_attr( $choice ),
						checked( $value, $choice, false ),
						empty( $field['shapes'] ) ? '' : '<i class="psbtt-shape-icon psbtt-shape-icon-' . esc_attr( $choice ) . '" aria-hidden="true"></i>',
						esc_html( $label )
					);
				}
				echo '</fieldset>';
				break;

			case 'select':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
				foreach ( $field['choices'] as $choice => $label ) {
					printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $choice ), selected( $value, $choice, false ), esc_html( $label ) );
				}
				echo '</select>';
				break;

			case 'range':
				printf(
					'<div class="psbtt-range"><input type="range" id="%1$s" name="%2$s" value="%3$s" min="%4$s" max="%5$s" step="%6$s"><output class="range-value" for="%1$s" data-unit="%7$s">%3$s%7$s</output></div>',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					esc_attr( (string) $field['min'] ),
					esc_attr( (string) $field['max'] ),
					esc_attr( (string) $field['step'] ),
					esc_attr( $field['unit'] )
				);
				break;

			case 'number':
				printf(
					'<input type="number" class="small-text" id="%1$s" name="%2$s" value="%3$s" min="%4$s" max="%5$s" step="%6$s"> %7$s',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					esc_attr( (string) $field['min'] ),
					esc_attr( (string) $field['max'] ),
					esc_attr( (string) ( $field['step'] ?? 1 ) ),
					esc_html( $field['unit'] ?? '' )
				);
				break;

			case 'color':
				printf(
					'<input type="text" class="psbtt-color" id="%1$s" name="%2$s" value="%3$s" data-default-color="%4$s">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value ),
					esc_attr( Settings::DEFAULTS[ $key ] )
				);
				break;

			case 'text':
				printf(
					'<input type="text" class="regular-text" id="%1$s" name="%2$s" value="%3$s" maxlength="20">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value )
				);
				break;

			case 'textarea':
				printf(
					'<textarea id="%1$s" name="%2$s" rows="%3$d" class="large-text%4$s" spellcheck="false">%5$s</textarea>',
					esc_attr( $id ),
					esc_attr( $name ),
					(int) $field['rows'],
					empty( $field['code'] ) ? '' : ' code',
					esc_textarea( (string) $value )
				);
				break;

			case 'post_types':
				$types = get_post_types( array( 'public' => true ), 'objects' );
				echo '<fieldset class="psbtt-checkboxes"><legend class="screen-reader-text">' . esc_html( $field['label'] ) . '</legend>';
				echo '<input type="hidden" name="' . esc_attr( $name ) . '[]" value="">';
				foreach ( $types as $type ) {
					if ( 'attachment' === $type->name ) {
						continue;
					}
					printf(
						'<label><input type="checkbox" name="%1$s[]" value="%2$s" %3$s> %4$s</label><br>',
						esc_attr( $name ),
						esc_attr( $type->name ),
						checked( in_array( $type->name, (array) $value, true ), true, false ),
						esc_html( $type->labels->singular_name )
					);
				}
				echo '</fieldset>';
				break;
		}

		if ( ! empty( $field['desc'] ) ) {
			echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
		}
	}

	/**
	 * Sanitize the settings array.
	 *
	 * @param mixed $input Raw input.
	 * @return array<string,mixed>
	 */
	public function sanitize_settings( $input ): array {
		$d     = Settings::DEFAULTS;
		$input = is_array( $input ) ? $input : array();
		$out   = array();

		$choice = static function ( string $key, array $allowed ) use ( $input, $d ): string {
			$v = isset( $input[ $key ] ) ? sanitize_key( $input[ $key ] ) : '';
			return in_array( $v, $allowed, true ) ? $v : $d[ $key ];
		};
		$int = static function ( string $key, int $min, int $max ) use ( $input, $d ): int {
			if ( ! isset( $input[ $key ] ) || '' === $input[ $key ] ) {
				return $d[ $key ];
			}
			return min( $max, max( $min, absint( $input[ $key ] ) ) );
		};

		// Booleans.
		$out['enabled']       = ! empty( $input['enabled'] );
		$out['smooth_scroll'] = ! empty( $input['smooth_scroll'] );

		// Choices.
		$out['show_on']        = $choice( 'show_on', array( 'all', 'mobile_only', 'desktop_only' ) );
		$out['position']       = $choice( 'position', array( 'right', 'left' ) );
		$out['shape']          = $choice( 'shape', array( 'circle', 'rounded', 'square' ) );
		$out['icon_type']      = $choice( 'icon_type', array( 'arrow', 'text', 'custom_svg' ) );
		$out['animation_show'] = $choice( 'animation_show', array( 'fade', 'slide', 'none' ) );

		// Numbers.
		$out['size']            = $int( 'size', 32, 80 );
		$out['bottom_offset']   = $int( 'bottom_offset', 0, 500 );
		$out['side_offset']     = $int( 'side_offset', 0, 500 );
		$out['scroll_offset']   = $int( 'scroll_offset', 0, 10000 );
		$out['scroll_duration'] = $int( 'scroll_duration', 100, 3000 );
		$out['z_index']         = $int( 'z_index', 0, 2147483647 );
		$out['opacity']         = isset( $input['opacity'] )
			? round( min( 1.0, max( 0.1, (float) $input['opacity'] ) ), 2 )
			: $d['opacity'];

		// Colors.
		foreach ( array( 'bg_color', 'text_color', 'bg_color_hover', 'text_color_hover' ) as $key ) {
			$color       = isset( $input[ $key ] ) ? sanitize_hex_color( $input[ $key ] ) : null;
			$out[ $key ] = $color ? $color : $d[ $key ];
		}

		// Strings.
		$out['icon_text']  = isset( $input['icon_text'] ) ? mb_substr( sanitize_text_field( $input['icon_text'] ), 0, 20 ) : $d['icon_text'];
		$out['custom_svg'] = isset( $input['custom_svg'] ) ? trim( wp_kses( (string) $input['custom_svg'], Icons::allowed_svg() ) ) : '';
		$out['custom_css'] = isset( $input['custom_css'] ) ? trim( wp_strip_all_tags( (string) $input['custom_css'] ) ) : '';

		// Post types.
		$public            = get_post_types( array( 'public' => true ) );
		$raw_types         = isset( $input['post_types'] ) ? (array) $input['post_types'] : array();
		$out['post_types'] = array_values(
			array_filter(
				array_map( 'sanitize_key', $raw_types ),
				static fn( $t ) => '' !== $t && isset( $public[ $t ] )
			)
		);

		return array_merge( $d, $out );
	}

	/**
	 * Enqueue admin assets on our page only.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		if ( $hook !== $this->hook ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'psbtt-admin', PSBTT_PLUGIN_URL . 'assets/css/psbtt-admin.css', array( 'wp-color-picker' ), PSBTT_VERSION );
		wp_enqueue_script( 'psbtt-admin', PSBTT_PLUGIN_URL . 'assets/js/psbtt-admin.js', array( 'wp-color-picker' ), PSBTT_VERSION, true );
	}
}
