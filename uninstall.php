<?php
/**
 * Uninstall: remove plugin settings.
 *
 * @package PavelSilinskii\BackToTop
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'psbtt_settings' );
