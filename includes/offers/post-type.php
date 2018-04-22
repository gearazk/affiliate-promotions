<?php
/**
 * Offer post type
 *
 * @package     AffiliatePromotions\Offers\PostType
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Register Custom Post Type
 */
function affpromos_register_offer_post_type() {

    $labels = array(
        'name'                  => _x( 'Offers', 'Post Type General Name', AFFILIATE_PROMOTIONS_PLUG ),
        'singular_name'         => _x( 'Offer', 'Post Type Singular Name', AFFILIATE_PROMOTIONS_PLUG ),
        'menu_name'             => __( 'Offers', AFFILIATE_PROMOTIONS_PLUG ),
        'name_admin_bar'        => __( 'Offer', AFFILIATE_PROMOTIONS_PLUG ),
        'archives'              => __( 'Offer Archives', AFFILIATE_PROMOTIONS_PLUG ),
        'parent_item_colon'     => __( 'Parent Offer:', AFFILIATE_PROMOTIONS_PLUG ),
        'all_items'             => __( 'Offers', AFFILIATE_PROMOTIONS_PLUG ), // Submenu name
        'add_new_item'          => __( 'Add New Offer', AFFILIATE_PROMOTIONS_PLUG ),
        'add_new'               => __( 'Add Offer', AFFILIATE_PROMOTIONS_PLUG ),
        'new_item'              => __( 'New Offer', AFFILIATE_PROMOTIONS_PLUG ),
        'edit_item'             => __( 'Edit Offer', AFFILIATE_PROMOTIONS_PLUG ),
        'update_item'           => __( 'Update Offer', AFFILIATE_PROMOTIONS_PLUG ),
        'view_item'             => __( 'View Offer', AFFILIATE_PROMOTIONS_PLUG ),
        'search_items'          => __( 'Search Offer', AFFILIATE_PROMOTIONS_PLUG ),
        'not_found'             => __( 'Not found', AFFILIATE_PROMOTIONS_PLUG ),
        'not_found_in_trash'    => __( 'Not found in Trash', AFFILIATE_PROMOTIONS_PLUG ),
        'featured_image'        => __( 'Featured Image', AFFILIATE_PROMOTIONS_PLUG ),
        'set_featured_image'    => __( 'Set featured image', AFFILIATE_PROMOTIONS_PLUG ),
        'remove_featured_image' => __( 'Remove featured image', AFFILIATE_PROMOTIONS_PLUG ),
        'use_featured_image'    => __( 'Use as featured image', AFFILIATE_PROMOTIONS_PLUG ),
        'insert_into_item'      => __( 'Insert into offer', AFFILIATE_PROMOTIONS_PLUG ),
        'uploaded_to_this_item' => __( 'Uploaded to this offer', AFFILIATE_PROMOTIONS_PLUG ),
        'items_list'            => __( 'Offers list', AFFILIATE_PROMOTIONS_PLUG ),
        'items_list_navigation' => __( 'Offers list navigation', AFFILIATE_PROMOTIONS_PLUG ),
        'filter_items_list'     => __( 'Filter offers list', AFFILIATE_PROMOTIONS_PLUG ),
    );
    $rewrite = array(
        'slug'                  => 'offer',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __( 'Offer', AFFILIATE_PROMOTIONS_PLUG ),
        'description'           => __( 'Offers', AFFILIATE_PROMOTIONS_PLUG ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => 'edit.php?post_type=affpromos_promotion',
        'menu_position'         => 25,
        'menu_icon'             => false,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'offers',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'affpromos_offer', $args );

}
add_action( 'init', 'affpromos_register_offer_post_type', 0 );