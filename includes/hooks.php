<?php
/**
 * Hooks
 *
 * @package     AffiliatePromotions\Hooks
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Register new image sizes
 */
function affpromos_add_image_sizes() {
    add_image_size( 'affpromos-thumb', 480, 250, array( 'center', 'top' ) );
    add_image_size( 'affpromos-thumb-small', 144, 75, array( 'center', 'top' ) );
}
add_action( 'admin_init', 'affpromos_add_image_sizes' );
