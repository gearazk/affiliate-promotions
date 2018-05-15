<?php
/**
 * Functions
 *
 * @package     AffiliatePromotions\Functions
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



function affpromos_dropdown_multi_select_render( $args , $choices) {
	
	$defaults = array(
		'id'            => '',
		'class'         => 'widefat',
		'name'          => '',
		'selected'      => 0,
		'placeholder'   => __('Select a choice',AFFILIATE_PROMOTIONS_PLUG),
		'_func_map'     => function($choice){
			return (object) array(
				'value' => $choice->value,
				'text'  => $choice->text
			);
		},
	);
	
	$r = wp_parse_args( $args, $defaults );
	$output = '';
	if ( ! empty( $choices ) ) {
		$output = "<select data-placeholder='".$r['placeholder']."' style='width:100%' multiple='multiple' name='" . esc_attr( $r['name'] ) . "[]' id='" . esc_attr( $r['id'] ) . "' class='" . esc_attr( $r['class'] ) . " aff_multiselect'>\n";
		foreach ( $choices as $choice ) {
			$choice = $r['_func_map']($choice);
			$output .= '<option value="' . esc_attr( $choice->value ) . '" ';
			$output .= in_array($choice->value, $r['selected'] ) ? 'selected' :'';
			$output .= selected( $r['selected'], $choice->value, false );
			$output .= '>' . esc_html( $choice->text ) . '</option>\n';
		}
		$output .= "</select>\n";
	}
	echo $output;
	
	
	if (!wp_style_is(AFFILIATE_PROMOTIONS_PREFIX.'multiselect','queue'))
		wp_enqueue_style( AFFILIATE_PROMOTIONS_PREFIX.'multiselect', AFFILIATE_PROMOTIONS_URL.'public/assets/select2/select2.min.css' );
	
	if (!wp_script_is(AFFILIATE_PROMOTIONS_PREFIX.'multiselect','queue'))
		wp_enqueue_script( AFFILIATE_PROMOTIONS_PREFIX.'multiselect', AFFILIATE_PROMOTIONS_URL.'public/assets/select2/select2.min.js' );
	
	if (!wp_script_is(AFFILIATE_PROMOTIONS_PREFIX.'multiselect_selector','queue'))
		wp_enqueue_script( AFFILIATE_PROMOTIONS_PREFIX.'multiselect_selector', AFFILIATE_PROMOTIONS_URL.'public/assets/select2/select2_selector.js' );
}

/*
 * Get content from a single post
 */
function affpromos_get_post_content( $postid = null ) {
	
	if ( empty ( $postid ) )
		$postid = get_the_ID();
	
	$post = get_post( $postid );
	$content = $post->post_content;
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	
	return $content;
}

/*
 * Get template file
 */
function affpromos_get_template_file( $template, $type ) {
	
	$template_file = AFFILIATE_PROMOTIONS_DIR . 'templates/' . $template . '.php';
	
	$template_file = apply_filters( 'affpromos_template_file', $template_file, $template, $type );
	
	if ( file_exists( $template_file ) )
		return $template_file;
	return false;
}

function affpromos_insert_vendor( $vendor ) {
	
	global $wpdb;
	
	$post_type = AFFILIATE_PROMOTIONS_PREFIX.'vendor';
	$vendor_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE (post_title = '{$vendor->merchant}' and post_type = '{$post_type}') ;");
	
	if($vendor_id)
		return $vendor_id;
	
	$vendor_id = wp_insert_post(array(
		'post_title'    =>      $vendor->merchant,
		'post_name'     =>      $vendor->merchant,
		'post_status'   =>      "publish",
		'post_author'   =>      get_current_user_id(),
		'post_type'     =>      $post_type,
	));
	add_post_meta($vendor_id ,AFFILIATE_PROMOTIONS_PREFIX .'vendor_url',$vendor->url);
//	if (isset($vendor->category))
//		add_post_meta($vendor_id ,AFFILIATE_PROMOTIONS_PREFIX .'vendor_category',$vendor->category);
	
	return $vendor_id;
}

function affpromos_insert_custom_post( $promotion ,$post_type) {
	return wp_insert_post(array(
		'post_title'    =>      $promotion->name,
		'post_status'   =>      "publish",
		'post_content'  =>      $promotion->content,
		'post_type'     =>      $post_type,
	));
}

function affpromos_insert_promotion_post( $promotion ) {
	return affpromos_insert_custom_post($promotion,AFFILIATE_PROMOTIONS_PREFIX.'promotion');
}

function affpromos_insert_offer_post( $promotion ) {
	return affpromos_insert_custom_post($promotion,AFFILIATE_PROMOTIONS_PREFIX.'offer');
}

function affpromos_insert_category_post( $promotion ) {
	return affpromos_insert_custom_post($promotion,AFFILIATE_PROMOTIONS_PREFIX.'category');
}

function affpromos_insert_vendor_post( $promotion ) {
	return affpromos_insert_custom_post($promotion,AFFILIATE_PROMOTIONS_PREFIX.'vendor');
}


/*
 *
 * @param mixed         $promotion      Input param in to DB
 * Example:
 * array(
 *      'id'=>'',
 *      'name'=>'',
 *      'content'=>'',
 *      'merchant'=>'',
 *      'domain'=>'',
 *      'coupons'=>array(
 *          array('coupon_code'='','coupon_save'=>'')
 *      ),
 *      'categories'=>array(),
 *      'link'=>'',
 *      'start_time'=>'(timestamp)'
 *      'end_time'=>'(timestamp)',
 * @param string|null   $meta_id_name   Type of promotion, default is 'promotion'
 *
 * @return int($post_id)|null  or null on failure
 */
function affpromos_insert_promotion( $promotion ,$meta_id_name=null) {
	
	global $wpdb;
	
	if (!is_object($promotion)){
		$promotion = (object)$promotion;
	}
	if (!$meta_id_name){
		$meta_id_name = AFFILIATE_PROMOTIONS_PREFIX .'promotion_id';
	}
	$post_type = AFFILIATE_PROMOTIONS_PREFIX.'promotion';
	
	$meta_id = AFFILIATE_PROMOTIONS_PREFIX .$promotion->id;
	$post_id = $wpdb->get_var( "SELECT * FROM $wpdb->postmeta WHERE (meta_key = '{$meta_id_name}' and meta_value = '{$meta_id}' );");
	if ($post_id !== null){
		return $post_id;
	}
	
	$post_id        = affpromos_insert_promotion_post($promotion);
	
	$thumnail_id    = affpromos_set_thumbnail($promotion->image,$post_id);
	
	$vendor_id      = affpromos_insert_vendor((object) array(
		'merchant'  =>$promotion->merchant,
		'url'       =>$promotion->domain,
	));
	
	if( empty($promotion->coupons) ){
		wp_set_object_terms($post_id, 'Promotion' ,AFFILIATE_PROMOTIONS_PREFIX.'promotion_type');
	}else{
		wp_set_object_terms($post_id, 'Coupon' ,AFFILIATE_PROMOTIONS_PREFIX.'promotion_type');
		foreach ($promotion->coupons as $coupon){
			$coupon_code[] = $coupon->coupon_code;
			$coupon_save[] = $coupon->coupon_save;
		}
		add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_code', implode($coupon_code,',') );
		add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_discount', implode($coupon_save,',') );
	}
	foreach ($promotion->categories as $category){
		$cate_id = AccessTrade_Api::process_categories_data($category);
		wp_set_object_terms( $post_id, $cate_id, AFFILIATE_PROMOTIONS_PREFIX . 'category' );
	}
	
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX . 'promotion_image',$thumnail_id);
	
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX . 'promotion_vendor',$vendor_id);
	
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX . 'promotion_url',$promotion->link);
	
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX . 'promotion_title',$promotion->name);
	
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX . 'promotion_valid_from', DateTime::createFromFormat("Y-m-d", $promotion->start_time)->getTimestamp());
	
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX . 'promotion_valid_until', DateTime::createFromFormat("Y-m-d", $promotion->end_time)->getTimestamp());
	
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX . 'promotion_description',$promotion->content);
	
	add_post_meta($post_id,$meta_id_name,$meta_id);
	
	return $post_id;
}

function affpromos_set_thumbnail($image_url,$post_id){
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	$image_id = media_sideload_image($image_url, $post_id,null,'id');
	set_post_thumbnail($post_id,$image_id);
	return $image_id;
}

