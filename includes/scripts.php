<?php
/**
 * Scripts
 *
 * @package     AffiliatePromotions\Scripts
 * @since       1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @global      string $post_type The type of post that we are editing
 * @return      void
 */
function affpromos_admin_scripts( $hook ) {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) ? '' : '.min';

    /**
     *	Settings page only
     */
    $screen = get_current_screen();

    if ( ! empty( $screen->base ) && ( $screen->base == 'settings_page_affiliate_promotions' || $screen->base == 'widgets' ) ) {

        wp_enqueue_script( 'affpromos_admin_js', AFFILIATE_PROMOTIONS_URL . 'public/assets/js/admin' . $suffix . '.js', array( 'jquery' ), AFFILIATE_PROMOTIONS_VER );
        wp_enqueue_style( 'affpromos_admin_css', AFFILIATE_PROMOTIONS_URL . 'public/assets/css/admin' . $suffix . '.css', false, AFFILIATE_PROMOTIONS_VER );
    }
}
add_action( 'admin_enqueue_scripts', 'affpromos_admin_scripts', 100 );

/**
 * Load frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function affpromos_scripts( $hook ) {

    global $post;

    if( ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'affpromos_promotions' ) ) ) { // TODO: Multiple shortcodes

        // Use minified libraries if SCRIPT_DEBUG is turned off
        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        wp_enqueue_script( 'affpromos_scripts', AFFILIATE_PROMOTIONS_URL . 'public/assets/js/scripts' . $suffix . '.js', array( 'jquery' ), AFFILIATE_PROMOTIONS_VER, true );
        wp_enqueue_style( 'affpromos_styles', AFFILIATE_PROMOTIONS_URL . 'public/assets/css/styles' . $suffix . '.css', false, AFFILIATE_PROMOTIONS_VER );

    }
}
add_action( 'wp_enqueue_scripts', 'affpromos_scripts' );