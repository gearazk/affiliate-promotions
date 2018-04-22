<?php
/**
 * Helper
 *
 * @package     AffiliatePromotions\Helper
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get options
 *
 * return array options or empty when not available
 */
function affpromos_get_options() {
    return get_option( 'affpromos_settings', array() );
}

/*
 * Public assets folder
 */
function affpromos_the_assets() {
    echo AFFILIATE_PROMOTIONS_URL . 'public/assets';
}

/*
 * Better debugging
 */
function affpromos_debug( $args, $title = false ) {

    if ( $title ) {
        echo '<h3>' . $title . '</h3>';
    }

    if ( $args ) {
        echo '<pre>';
        print_r($args);
        echo '</pre>';
    }
}