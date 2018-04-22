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
function affpromos_vendor_extend_columns( $defaults ) {

    $defaults['affpromos_vendor_thumb'] = __( 'Thumbnail', AFFILIATE_PROMOTIONS_PLUG );
//    $defaults['affpromos_vendor_shortcodes'] = __( 'Shortcodes', AFFILIATE_PROMOTIONS_PLUG );

    return $defaults;
}
add_filter('manage_affpromos_vendor_posts_columns', 'affpromos_vendor_extend_columns', 10);

/*
 * Add columns content
 */
function affpromos_vendor_extend_columns_content( $column_name, $postid ) {

    if ( $column_name == 'affpromos_vendor_thumb' ) {

        $image = affpromos_get_vendor_thumbnail( $postid, 'small' );

        if ( ! empty ( $image['url'] ) ) {
            ?>
            <img src="<?php echo $image['url'];?>" alt="thumbnail" />
            <?php
        }

    }
//    else if ( $column_name == 'affpromos_vendor_shortcodes' ) {
//        echo '[affpromos id=' . $postid . ']';
//    }
}
add_action('manage_affpromos_vendor_posts_custom_column', 'affpromos_vendor_extend_columns_content', 10, 2);