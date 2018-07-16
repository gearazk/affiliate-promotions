<?php
/**
 * Manage Vendors
 *
 * @package     AffiliatePromotions\Promotions\ManageVendors
 * @since       1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/*
 * Add new columns
 */
function affpromos_offer_extend_columns( $defaults ) {

    $defaults['affpromos_offer_thumb'] = __( 'Thumbnail', AFFILIATE_PROMOTIONS_PLUG );
    $defaults['affpromos_offer_shortcodes_id'] = __( 'ID for shortcode', AFFILIATE_PROMOTIONS_PLUG );

    return $defaults;
}
add_filter('manage_affpromos_offer_posts_columns', 'affpromos_offer_extend_columns', 10);

/*
 * Add columns content
 */
function affpromos_offer_extend_columns_content( $column_name, $postid ) {
    if ( $column_name == 'affpromos_offer_thumb' ) {
        $image = get_the_post_thumbnail( $postid, array(150,150) );
        echo $image;

    }
    else if ( $column_name == 'affpromos_offer_shortcodes_id' ) {
        echo '<strong>'.$postid.'</strong>';
    }
}
add_action('manage_affpromos_offer_posts_custom_column', 'affpromos_offer_extend_columns_content', 10, 2);