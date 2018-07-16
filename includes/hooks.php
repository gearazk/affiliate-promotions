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


function filter_post_by_vendor( $wp_query ) {
	if (is_admin()) {
		$post_type = $wp_query->query['post_type'];
		if ( $post_type == 'affpromos_promotion' && isset($_GET['vendor']) ) {
			$wp_query->set('meta_key', 'affpromos_promotion_vendor');
			$wp_query->set('meta_value',$_GET['vendor'] );
		}elseif ($post_type == 'affpromos_offer' && isset($_GET['vendor'])){
			$wp_query->set('meta_key', 'affpromos_offer_vendor');
			$wp_query->set('meta_value',$_GET['vendor'] );
		}
	}
}

add_filter('pre_get_posts', 'filter_post_by_vendor');