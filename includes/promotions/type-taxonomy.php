<?php
/**
 * Type Taxonomy
 *
 * @package     AffiliatePromotions\Promotions\TypeTaxonomy
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Register Custom Taxonomy
 */
function affpromos_register_promotion_type_taxonomy() {

    $labels = array(
        'name'                       => _x( 'Types', 'Taxonomy General Name', AFFILIATE_PROMOTIONS_PLUG ),
        'singular_name'              => _x( 'Type', 'Taxonomy Singular Name', AFFILIATE_PROMOTIONS_PLUG ),
        'menu_name'                  => __( 'Types', AFFILIATE_PROMOTIONS_PLUG ),
        'all_items'                  => __( 'All Types', AFFILIATE_PROMOTIONS_PLUG ),
        'parent_item'                => __( 'Parent Type', AFFILIATE_PROMOTIONS_PLUG ),
        'parent_item_colon'          => __( 'Parent Type:', AFFILIATE_PROMOTIONS_PLUG ),
        'new_item_name'              => __( 'New Type Name', AFFILIATE_PROMOTIONS_PLUG ),
        'add_new_item'               => __( 'Add New Type', AFFILIATE_PROMOTIONS_PLUG ),
        'edit_item'                  => __( 'Edit Type', AFFILIATE_PROMOTIONS_PLUG ),
        'update_item'                => __( 'Update Type', AFFILIATE_PROMOTIONS_PLUG ),
        'view_item'                  => __( 'View Type', AFFILIATE_PROMOTIONS_PLUG ),
        'separate_items_with_commas' => __( 'Separate types with commas', AFFILIATE_PROMOTIONS_PLUG ),
        'add_or_remove_items'        => __( 'Add or remove types', AFFILIATE_PROMOTIONS_PLUG ),
        'choose_from_most_used'      => __( 'Choose from the most used', AFFILIATE_PROMOTIONS_PLUG ),
        'popular_items'              => __( 'Popular Types', AFFILIATE_PROMOTIONS_PLUG ),
        'search_items'               => __( 'Search Types', AFFILIATE_PROMOTIONS_PLUG ),
        'not_found'                  => __( 'Not Found', AFFILIATE_PROMOTIONS_PLUG ),
        'no_terms'                   => __( 'No types', AFFILIATE_PROMOTIONS_PLUG ),
        'items_list'                 => __( 'Types list', AFFILIATE_PROMOTIONS_PLUG ),
        'items_list_navigation'      => __( 'Types list navigation', AFFILIATE_PROMOTIONS_PLUG ),
    );
    $rewrite = array(
        'slug'                       => 'promotions/type',
        'with_front'                 => true,
        'hierarchical'               => true,
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'rewrite'                    => $rewrite,
    );
    register_taxonomy( 'affpromos_promotion_type', array( 'affpromos_promotion' ), $args );

}
add_action( 'init', 'affpromos_register_promotion_type_taxonomy', 0 );