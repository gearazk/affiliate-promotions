<?php


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Offer Template classes
 */


function affpromos_get_offer_title ($postid = null){

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    return get_the_title( $postid );
}
function affpromos_get_offer_url ($postid = null){

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $url = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'offer_url', true );
    if (!isset($url))
        $url = '#';
    return $url;
}
function affpromos_get_offer_thumbnail_url ($postid = null){

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $url = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'offer_image_url', true );
    if (empty($url))
        $url = '#';
    return $url;

}
function format_vnd($price){

    $t_price = strval($price);
    $text = substr($t_price,0, strlen($t_price)%3 );
    $t_price = substr($t_price,-(strlen($t_price) - strlen($t_price)%3) );
    for($i = 0; $i < strlen($t_price) ; $i+=3)
    {
        if(strlen($text)>0)
            $text .= '.'.substr($t_price,$i,$i+3);
        else
            $text .= substr($t_price,$i,$i+3);
    }

    return $text.'đ';
}
function affpromos_get_offer_price_vnd ($postid = null){

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $price = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'offer_price', true );
    $price_sale = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'offer_price_sale', true );

    if (!empty($price)  && $price != $price_sale)
    {
        return format_vnd($price);
    }
    return '';
}

function affpromos_get_offer_price_sale_vnd ($postid = null){

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $price = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'offer_price_sale', true );

    if (!empty($price))
        return format_vnd($price);
    return '';
}
function affpromos_get_offer_discount_percent ($postid = null){

    if ( empty ( $postid ) )
        $postid = get_the_ID();

    $price = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'offer_price', true );
    $price_sale = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'offer_price_sale', true );

    if (!empty($price) && !empty($price_sale) && $price != $price_sale)
    {
        $percent = intval((1-($price_sale/$price))*100);
        return $percent.'%';
    }
    return false;
}

function affpromos_get_offer_specs ($postid = null){
    if ( empty ( $postid ) )
        $postid = get_the_ID();
    return get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'offer_specs', true );

}

function affpromos_get_offer_vendor_name ($postid = null){
    if ( empty ( $postid ) )
        $postid = get_the_ID();
    $vendor_id = get_post_meta( $postid, AFFILIATE_PROMOTIONS_PREFIX . 'offer_vendor', true );

    if (!empty($vendor_id) )
    {
        return get_the_title($vendor_id);
    }
    return '';
}
function affpromos_get_offer_vendor_thumbnail_url($postid = null){
    if ( empty ( $postid ) )
        $postid = get_the_ID();
    $vendor_id = affpromos_get_promotion_vendor_id( $postid );
    if ( ! empty ( $vendor_id ) ) {
        $vendor_image = affpromos_get_vendor_thumbnail( $vendor_id );
        if ( ! empty ( $vendor_image ) )
            return $vendor_image['url'];
    }
    return AFFILIATE_PROMOTIONS_URL . '/public/assets/img/thumb.png';
}


