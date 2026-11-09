<?php
/**
 * Settings page template.
 *
 * @package PavelSilinskii\BackToTop
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap psbtt-settings-wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<div class="psbtt-layout">

		<div class="psbtt-form-col">
			<form method="post" action="options.php" id="psbtt-form">
				<?php
				settings_fields( 'psbtt_settings_group' );
				do_settings_sections( 'psbtt-settings' );
				submit_button( __( 'Save Settings', 'pavel-silinskii-back-to-top' ) );
				?>
			</form>
		</div>

		<div class="psbtt-preview-col">
			<div class="psbtt-preview-box" id="psbtt-preview-box">
				<div class="psbtt-preview-page" aria-hidden="true">
					<div class="psbtt-preview-header"></div>
					<div class="psbtt-preview-line"></div>
					<div class="psbtt-preview-line short"></div>
					<div class="psbtt-preview-line"></div>
					<div class="psbtt-preview-line short"></div>
					<div class="psbtt-preview-line"></div>
					<div class="psbtt-preview-line"></div>
					<div class="psbtt-preview-line short"></div>
				</div>
				<button type="button" id="psbtt-preview-btn" class="psbtt-preview-btn" aria-hidden="true" tabindex="-1">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
						stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<polyline points="18 15 12 9 6 15"></polyline>
					</svg>
				</button>
			</div>
			<p class="description psbtt-preview-note">
				<?php esc_html_e( 'Live preview. Hover the button to see hover colors.', 'pavel-silinskii-back-to-top' ); ?>
			</p>
		</div>

	</div>
</div>
