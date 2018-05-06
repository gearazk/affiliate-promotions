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
	
	
	$defaults['affpromos_promotion_thumb']  = __( 'Thumbnail', AFFILIATE_PROMOTIONS_PLUG );
	$defaults['affpromos_promotion_vendor'] = __( 'Vendors', AFFILIATE_PROMOTIONS_PLUG );
	
	return $defaults;
}
add_filter('manage_affpromos_promotion_posts_columns', 'affpromos_promotion_extend_columns', 10);

/*
 * Add columns content
 */
function affpromos_promotion_extend_columns_content( $column_name, $postid ) {
	
	if ( $column_name == 'affpromos_promotion_vendor' ) {
		
		$vendor_name = affpromos_get_promotion_vendor_name();
		$valid = affpromos_the_promotion_valid_text();
		
		echo $valid.'<br>';
		if ($vendor_name){
			echo "<h2>".$vendor_name."</h2>";
		}else{
			echo "<h1>...</h1>";
		}
		
	}
	
    if ( $column_name == 'affpromos_promotion_thumb' ) {

        $image = affpromos_get_promotion_thumbnail( $postid, 'small' );
        $url = affpromos_get_promotion_url( $postid );
    
        if ( ! empty ( $image['url'] ) ) {
            ?>
            <a href="<?php echo $url ?>" target="_blank">
                <img src="<?php echo $image['url'];?>" alt="thumbnail" st/>
            </a>
            <?php
        }

    }
	
}
add_action('manage_affpromos_promotion_posts_custom_column', 'affpromos_promotion_extend_columns_content', 10, 2);

function affpromos_delete_promotion_meta_data($post_id){
//    delete_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_id');
}
add_action( 'deleted_post', 'affpromos_delete_promotion_meta_data', 10 );