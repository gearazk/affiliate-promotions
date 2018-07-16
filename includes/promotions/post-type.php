<?php
/**
 * Promotion post type
 *
 * @package     AffiliatePromotions\Promotions\PostType
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * egister Custom Post Type
 */
function affpromos_register_promotion_post_type() {

    $labels = array(
        'name'                  => _x( 'Promotions', 'Post Type General Name', AFFILIATE_PROMOTIONS_PLUG ),
        'singular_name'         => _x( 'Promotion', 'Post Type Singular Name', AFFILIATE_PROMOTIONS_PLUG ),
        'menu_name'             => __( 'Promotions', AFFILIATE_PROMOTIONS_PLUG ),
        'name_admin_bar'        => __( 'Promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'archives'              => __( 'Promotion Archives', AFFILIATE_PROMOTIONS_PLUG ),
        'parent_item_colon'     => __( 'Parent Promotion:', AFFILIATE_PROMOTIONS_PLUG ),
        'all_items'             => __( 'All Promotions', AFFILIATE_PROMOTIONS_PLUG ),
        'add_new_item'          => __( 'Add New Promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'add_new'               => __( 'Add Promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'new_item'              => __( 'New Promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'edit_item'             => __( 'Edit Promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'update_item'           => __( 'Update Promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'view_item'             => __( 'View Promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'search_items'          => __( 'Search Promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'not_found'             => __( 'Not found', AFFILIATE_PROMOTIONS_PLUG ),
        'not_found_in_trash'    => __( 'Not found in Trash', AFFILIATE_PROMOTIONS_PLUG ),
        'featured_image'        => __( 'Featured Image', AFFILIATE_PROMOTIONS_PLUG ),
        'set_featured_image'    => __( 'Set featured image', AFFILIATE_PROMOTIONS_PLUG ),
        'remove_featured_image' => __( 'Remove featured image', AFFILIATE_PROMOTIONS_PLUG ),
        'use_featured_image'    => __( 'Use as featured image', AFFILIATE_PROMOTIONS_PLUG ),
        'insert_into_item'      => __( 'Insert into promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'uploaded_to_this_item' => __( 'Uploaded to this promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'items_list'            => __( 'Promotions list', AFFILIATE_PROMOTIONS_PLUG ),
        'items_list_navigation' => __( 'Promotions list navigation', AFFILIATE_PROMOTIONS_PLUG ),
        'filter_items_list'     => __( 'Filter promotions list', AFFILIATE_PROMOTIONS_PLUG ),
    );
    $rewrite = array(
        'slug'                  => 'promotion',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __( 'Promotion', AFFILIATE_PROMOTIONS_PLUG ),
        'description'           => __( 'Promotions', AFFILIATE_PROMOTIONS_PLUG ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-tickets-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'promotions',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'affpromos_promotion', $args );

}
add_action( 'init', 'affpromos_register_promotion_post_type', 0 );

