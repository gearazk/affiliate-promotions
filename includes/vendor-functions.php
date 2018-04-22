<?php
/**
 * Vendor functions
 *
 * @package     AffiliatePromotions\VendorFunctions
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function affpromos_get_vendor_url( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $url = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'vendor_url', true );

    return ( ! empty ( $url ) ) ? $url : false;
}

function affpromos_get_vendor_id( $vendor_name ) {
	global $wpdb;
	
	$post_type = AFFILIATE_PROMOTIONS_PREFIX.'vendor';
	return $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE (post_title = '{$vendor_name}' and post_type = '{$post_type}') ;");
	
}
function affpromos_get_vendor_thumbnail( $postid = null, $size = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    // Get thumbnail from promotion
    $image_size = ( 'small' === $size ) ? 'affpromos-thumb-small' : 'affpromos-thumb';

    $images = rwmb_meta( AFFILIATE_PROMOTIONS_PREFIX . 'vendor_image', 'size=' . $image_size, $postid );

    if( ! empty ( $images ) )
    {
        if (is_numeric($images))
        {
            return array(
                'url'=> get_the_guid($images),
                'alt'=> get_the_title($images),
            );
        }
        return array_shift( $images );
    }

    // No thumbnail found
    return false;
}