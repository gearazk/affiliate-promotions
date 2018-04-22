<?php
/**
 * Manage Promotions
 *
 * @package     AffiliatePromotions\Promotions\ManagePromotions
 * @since       1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/*
 * Add new columns
 */
function affpromos_promotion_extend_columns( $defaults ) {

    $defaults['affpromos_promotion_thumb'] = __( 'Thumbnail', AFFILIATE_PROMOTIONS_PLUG );

    return $defaults;
}
add_filter('manage_affpromos_promotion_posts_columns', 'affpromos_promotion_extend_columns', 10);

/*
 * Add columns content
 */
function affpromos_promotion_extend_columns_content( $column_name, $postid ) {

    if ( $column_name == 'affpromos_promotion_thumb' ) {

        $image = affpromos_get_promotion_thumbnail( $postid, 'small' );

        if ( ! empty ( $image['url'] ) ) {
            ?>
            <img src="<?php echo $image['url'];?>" alt="thumbnail" />
            <?php
        }

    }
}
add_action('manage_affpromos_promotion_posts_custom_column', 'affpromos_promotion_extend_columns_content', 10, 2);