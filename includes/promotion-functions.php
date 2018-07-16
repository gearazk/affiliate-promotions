<?php
/**
 * Promotion functions
 *
 * @package     AffiliatePromotions\PromotionFunctions
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Promotion Template classes
 */
function affpromos_the_promotion_classes( $classes ) {

    // Add classes

    echo $classes;
}

function affpromos_get_promotion_vendor_id( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $vendor_id = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_vendor', true );

    return ( ! empty ( $vendor_id ) ) ? $vendor_id : false;
}

/*
 * Promotions
 */
function affpromos_get_promotion_thumbnail( $postid = null, $size = null ) {

    if ( empty ( $postid ) ) {
        $postid = get_the_ID();
    }

    // Get thumbnail from promotion
    $image_size = ( 'small' === $size ) ? 'affpromos-thumb-small' : 'affpromos-thumb';

    $images = rwmb_meta( AFFILIATE_PROMOTIONS_PREFIX . 'promotion_image', 'size=' . $image_size, $postid );

    if ( ! empty ( $images ) ) {
        
        if (is_numeric($images)) {
            return array(
                'url'=> get_the_guid($images),
                'alt'=> get_the_title($images),
            );      
        
            
        }
        
        return array_shift( $images );

    // Get thumbnail from vendor
    } else {
        $vendor_id = affpromos_get_promotion_vendor_id( $postid );

        if ( ! empty ( $vendor_id ) ) {
            $vendor_image = affpromos_get_vendor_thumbnail( $vendor_id, $size );

            if ( ! empty ( $vendor_image ) )
                return $vendor_image;
        }
    }

    // No thumbnail found
    return false;
}

function affpromos_get_promotion_vendor_thumbnail($postid = null,$size = null) {
    
    if ( empty ( $postid ) ) {
        $postid = get_the_ID();
    }
    
    $vendor_id = affpromos_get_promotion_vendor_id( $postid );

    if ( ! empty ( $vendor_id ) ) {
        $vendor_image = affpromos_get_vendor_thumbnail( $vendor_id, $size );

        if ( ! empty ( $vendor_image ) )
        {
            echo '<img alt="'.$vendor_image['title'].'" src="'.$vendor_image['url']. '"/ >';
            return;
        }
    }
    echo "<img src='" . AFFILIATE_PROMOTIONS_URL . '/public/assets/img/thumb.png' . "' alt='" . 'none' . "' />";

}

function affpromos_the_promotion_thumbnail( $postid = null ) {

    if ( empty ( $postid ) ) {
        $postid = get_the_ID();
    }

    $thumbnail = affpromos_get_promotion_thumbnail( $postid );

    // Prepare attributes
    $thumb_url = ( ! empty ( $thumbnail['url'] ) ) ? $thumbnail['url'] : AFFILIATE_PROMOTIONS_URL . '/public/assets/img/thumb.png';
    $thumb_alt = ( ! empty ( $thumbnail['alt'] ) ) ? $thumbnail['alt'] : affpromos_get_promotion_title( $postid );

    // Build thumbnail
    $thumbnail = "<img src='" . $thumb_url . "' alt='" . $thumb_alt . "' />";

    // Output
    echo $thumbnail;
}

function affpromos_get_promotion_discount( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $discount = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_discount', true );

    return ( ! empty ( $discount ) ) ? $discount : false;
}

function affpromos_get_promotion_title( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    // Promotion
    $title = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_title', true );

    if ( ! empty ( $title ) )
        return $title;

    // Vendor
    $vendor_id = affpromos_get_promotion_vendor_id( $postid );

    if ( ! empty ( $vendor_id ) )
        $title = get_the_title( $vendor_id );

    // Fallback
    if ( empty ( $title ) )
        $title = get_the_title( $postid );

    return $title;
}

function affpromos_get_promotion_vendor_name( $postid = null ) {
	
	if ( empty ( $postid ) )
		$postid = get_the_ID();
	
	// Vendor
	$vendor_id = affpromos_get_promotion_vendor_id( $postid );
	
	if ( ! empty ( $vendor_id ) ){
		$title = get_the_title( $vendor_id );
		return $title;
	}
    return false;
}

function affpromos_get_promotion_description( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    // Promotion
    $description = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_description', true );

    // Vendor
    if ( empty ( $description ) ) {
        $vendor_id = affpromos_get_promotion_vendor_id( $postid );

        if ( ! empty ( $vendor_id ) )
            $description = get_post_meta( $vendor_id, AFFILIATE_PROMOTIONS_PREFIX . 'vendor_description', true );
    }

    // Fallback
    if ( empty ( $description ) )
        $description = affpromos_get_post_content( $postid );

    return $description;
}

function affpromos_get_promotion_code( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $code = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_code', true );

    return ( ! empty ( $code ) ) ? $code : false;
}

function affpromos_the_promotion_code( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $copy_img = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAMAAAC6V+0/AAABI1BMVEUAAAAzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzPLD7eUAAAAYHRSTlMAAQIEBQYHCAoLDg8QEhMUFRYXGBocHR4gISIjKy0xMjM2ODo9P0FCRUZJSktMTlFSWFtcYmRma21wcXN3eHt8f4CDho6SmqKlqLC0ucDDx8jT3ODi5Obo6evt7/H1+f2nUYbbAAAAy0lEQVQYGa3BezvCUADA4d/RUCm3mrlMiZAIxVaas9wSucyllPv5/p/C9oznyd+8L3+VvelfmPw2/3kiO+pcEErYY4B0QTRUhUDEWhtyJmD3FhBvD/jydoSFfUDrXSaJv3cFaTcBpWUCcU/5ys1DE0R9km/G1nZOtqIaI0fDDCrYK+vT14v8mG0+dq6WojubG+T2CLmqXW0pr2Sc6hA7HsWXfU0Bz0+AXjMQlgkcSKCozgjozhxyHDIfDede3WmEZqwpfKveSzsv+Edfyg4bpMKWWckAAAAASUVORK5CYII=';

    $copy_img = apply_filters( 'affpromos_promotion_copy_img_src', $copy_img );

    $code = affpromos_get_promotion_code( $postid );
    ?>

    <div class="affpromos-clipboard affpromos-promotion-code" data-clipboard-text="<?php echo $code; ?>" data-affpromos-clipboard-confirmation-text="<?php _e('Copied!', AFFILIATE_PROMOTIONS_PLUG); ?>">
        <img class="affpromos-promotion-code__copy" src="<?php echo $copy_img; ?>" alt="<?php _e('Copy', AFFILIATE_PROMOTIONS_PLUG); ?>" />
        <?php echo $code; ?>
    </div>
    <?php
}

function affpromos_get_promotion_url( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $url = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_url', true );

    if ( empty ( $url ) ) {
        $vendor_id = affpromos_get_promotion_vendor_id( $postid );

        if ( ! empty ( $vendor_id ) )
            $url = affpromos_get_vendor_url( $vendor_id );
    }

    return $url;
}

function affpromos_get_promotion_types( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $term_list = wp_get_post_terms( $postid, 'affpromos_promotion_type', array( "fields" => "all" ) );

    if ( sizeof( $term_list ) > 0 ) {
        return $term_list;
    }

    return false;
}

function affpromos_the_promotion_types( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $types = '';

    $term_list = affpromos_get_promotion_types( $postid );

    if ( is_array( $term_list ) && sizeof( $term_list ) > 0 ) {

        foreach($term_list as $term_single) {
            echo '<span>';
            echo $term_single->name;
            echo '</span>';
        }
    }

    echo $types;
}

function affpromos_promotion_has_valid_dates( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $valid_from = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_valid_from', true );
    $valid_until = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_valid_until', true );

    return ( ! empty ( $valid_from ) || ! empty ( $valid_until ) ) ? true : false;
}

function affpromos_the_promotion_valid_dates( $postid = null ) {

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $date_format = get_option( 'date_format' );
    $date_format = apply_filters( 'affpromos_promotion_validation_date_format', $date_format );

    $dates = '';

    $valid_from = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_valid_from', true );
    $valid_until = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_valid_until', true );
    
    if ( ! empty ( $valid_from ) && time() < $valid_from ) {
        
        $dates .= __('Valid from', AFFILIATE_PROMOTIONS_PLUG) . ' ' . date_i18n( $date_format, $valid_from );
        
        if ( ! empty ( $valid_until ) ) {
    
            $dates .= ( empty ( $dates ) ) ? __('Valid until', AFFILIATE_PROMOTIONS_PLUG) : ' ' . __('until', AFFILIATE_PROMOTIONS_PLUG);
            $dates .= ' ' . date_i18n( $date_format, $valid_until );
        }
        
    } else {
        $timeDiff = abs($valid_until - time());
        $numberDays = $timeDiff/86400;  // 86400 seconds in one day
        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        $day_title = $numberDays > 1 ? __(' days' . $day_title, AFFILIATE_PROMOTIONS_PLUG) : __(' day' . $day_title, AFFILIATE_PROMOTIONS_PLUG);
        
        $dates .= __('Valid for', AFFILIATE_PROMOTIONS_PLUG) . ' ' . $numberDays . $day_title;
        
    }

    echo $dates;
}

function affpromos_the_promotion_vendor ($postid = null) {
    if ( empty ( $postid ) )
        $postid = get_the_ID();
    $vendor_id = affpromos_get_promotion_vendor_id( $postid );

    if ( ! empty ( $vendor_id ) )
        return get_the_title( $vendor_id );
    return '';
}

function affpromos_the_promotion_valid ($postid = null ) {
    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $valid_until = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'promotion_valid_until', true );
    if ( ! empty ( $valid_until ) ) {

        $interval = intval((intval($valid_until) - time())/ 86400) ;
        if($interval < 0)
	        return -1;
        return $interval;
    }
    return false;

}
function affpromos_the_promotion_valid_text ($postid = null ) {
	
    $valid = affpromos_the_promotion_valid($postid);
    if ($valid == -1){
        return 'Đã quá thời hạn';
    }else if ($valid == false){
        return 'Xác nhận';
    }else if ($valid == 0){
	    return 'Ít hơn 1 ngày';
    }else if ($valid > 30){
	    return 'Một khoảng thời gian';
    }
    return 'Còn '.$valid.' ngày';
	
}

function affpromos_get_the_promotion_discount_content ($postid = null ) {
    $code = affpromos_get_promotion_code($postid);
    if ($code) return $code;
    $discount = affpromos_get_promotion_discount($postid);
	if ($discount) return $discount;
    return false;
}
