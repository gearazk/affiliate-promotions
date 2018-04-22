<?php
/**
 * Libs
 *
 * @package     AffiliatePromotions\Libs
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Meta Box
 */
if ( ! class_exists( 'RW_Meta_Box' ) ) {
    require AFFILIATE_PROMOTIONS_DIR . '/includes/libs/meta-box/meta-box.php';
}

/*
 * Guzzle
 */
//if ( ! class_exists( 'RW_Meta_Box' ) ) {
//    require AFFILIATE_PROMOTIONS_DIR . '/includes/libs/guzzle/meta-box.php';
//}