<?php
/**
 * Category Taxonomy
 *
 * @package     AffiliatePromotions\Promotions\CategoryTaxonomy
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Register Custom Taxonomy
 */
function affpromos_register_category_taxonomy() {

    $labels = array(
        'name'                       => _x( 'Categories', 'Taxonomy General Name', AFFILIATE_PROMOTIONS_PLUG ),
        'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', AFFILIATE_PROMOTIONS_PLUG ),
        'menu_name'                  => __( 'Categories', AFFILIATE_PROMOTIONS_PLUG ),
        'all_items'                  => __( 'All Categories', AFFILIATE_PROMOTIONS_PLUG ),
        'parent_item'                => __( 'Parent Category', AFFILIATE_PROMOTIONS_PLUG ),
        'parent_item_colon'          => __( 'Parent Category:', AFFILIATE_PROMOTIONS_PLUG ),
        'new_item_name'              => __( 'New Category Name', AFFILIATE_PROMOTIONS_PLUG ),
        'add_new_item'               => __( 'Add New Category', AFFILIATE_PROMOTIONS_PLUG ),
        'edit_item'                  => __( 'Edit Category', AFFILIATE_PROMOTIONS_PLUG ),
        'update_item'                => __( 'Update Category', AFFILIATE_PROMOTIONS_PLUG ),
        'view_item'                  => __( 'View Category', AFFILIATE_PROMOTIONS_PLUG ),
        'separate_items_with_commas' => __( 'Separate categories with commas', AFFILIATE_PROMOTIONS_PLUG ),
        'add_or_remove_items'        => __( 'Add or remove categories', AFFILIATE_PROMOTIONS_PLUG ),
        'choose_from_most_used'      => __( 'Choose from the most used', AFFILIATE_PROMOTIONS_PLUG ),
        'popular_items'              => __( 'Popular Categories', AFFILIATE_PROMOTIONS_PLUG ),
        'search_items'               => __( 'Search Categories', AFFILIATE_PROMOTIONS_PLUG ),
        'not_found'                  => __( 'Not Found', AFFILIATE_PROMOTIONS_PLUG ),
        'no_terms'                   => __( 'No categories', AFFILIATE_PROMOTIONS_PLUG ),
        'items_list'                 => __( 'Categories list', AFFILIATE_PROMOTIONS_PLUG ),
        'items_list_navigation'      => __( 'Categories list navigation', AFFILIATE_PROMOTIONS_PLUG ),
    );
    $rewrite = array(
        'slug'                       => 'promotions/category',
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
    register_taxonomy( 'affpromos_category', array( 'affpromos_promotion','affpromos_offer' ), $args );

}
add_action( 'init', 'affpromos_register_category_taxonomy', 0 );

function add_columns_category($defaults) {
    $defaults['offer_count']  = 'Offers';


    /* ADD ANOTHER COLUMN (OPTIONAL) */
    // $defaults['second_column'] = 'Second Column';

    /* REMOVE DEFAULT CATEGORY COLUMN (OPTIONAL) */
    // unset($defaults['categories']);

    /* TO GET DEFAULTS COLUMN NAMES: */

    return $defaults;
}
function manage_columns_category($c, $column_name, $term_id) {
    if ($column_name == 'offer_count') {
        echo 'sad';
    }
}
//add_filter('manage_edit-affpromos_category_columns', 'add_columns_category');
//add_filter('manage_affpromos_promotion_custom_column', 'manage_columns_category', 10, 3);
