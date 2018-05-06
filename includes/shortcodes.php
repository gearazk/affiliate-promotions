<?php
/*
 * Post lists
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Include guzzle dependencies
require_once AFFILIATE_PROMOTIONS_DIR . 'includes/libs/vendor/autoload.php';
require_once AFFILIATE_PROMOTIONS_DIR . 'includes/libs/simple_html_dom.php';
require_once AFFILIATE_PROMOTIONS_DIR . 'includes/admin/class.settings.php';
require_once AFFILIATE_PROMOTIONS_DIR . 'includes/product-crawler.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;

function affpromos_add_promotions_shortcode( $atts, $content ) {
	
	if ( empty( $atts ) ) {
		return;
	}
	
	extract( shortcode_atts( array(
		'template'      =>'grid',
		'search'        => null,
		'category'      => null,
		'type'          => null,
		'vendor'        => null,
		'max'           => 20,
		'orderby'       => null,
		'grid'          => null,
		'hide_expired'  => null,
	), $atts ) );
	
	// Prepare options
	$options = affpromos_get_options();
	
	// Default Query Arguments
	$max = min($max,20);
	$max = max(1,20);
	$args = array(
		'post_type'         => 'affpromos_promotion',
		'orderby'           => 'modified',
		'order'             => 'DESC',
		'posts_per_page'    => $max,
	
	);
	
	if ( ! empty ( $search ) ) {
		$args['s'] = $search;
	}
	
	// Hide expired promotions
	$hide_expired_promotions = ( isset ( $options['hide_expired_promotions'] ) ) ? true : false;
	
	if ( ! empty ( $hide_expired ) ) // Maybe overwrite by shortcode
		$hide_expired_promotions = ( 'true' == $hide_expired ) ? true : false;
	
	if ( $hide_expired_promotions ) {
		
		$args['meta_query'] = array(
			'relation' => 'OR',
			// Until date not set yet
			array(
				'key'       => AFFILIATE_PROMOTIONS_PREFIX . 'promotion_valid_until',
				'compare'   => 'NOT EXISTS', // works!
				'type'      => 'numeric',
			),
			// Already expired
			array(
				'key'       => AFFILIATE_PROMOTIONS_PREFIX . 'promotion_valid_until',
				'value'     => intval(time()),
				'compare'   => '>',
				'type'      => 'numeric',
			)
		);
	}
	
	// Tax Queries
	$tax_queries = array(
		'relation' => 'AND'
	);
	
	// Categories
	if ( ! empty ( $category ) ) {
		$category = explode(',',$category);
		if ($category[0] != 'null')
		{
			$tax_queries[] = array(
				'taxonomy' => 'affpromos_category',
				'field' => ( is_numeric( $category[0] ) ) ? 'term_taxonomy_id' : 'slug',
				'terms' => ( $category ), // array( $category )
				'operator' => 'IN'
			);
		}
	}
	
	// Types
	if ( ! empty ( $type ) ) {
		$tax_queries[] = array(
			'taxonomy' => 'affpromos_promotion_type',
			'field' => ( is_numeric( $type ) ) ? 'term_taxonomy_id' : 'slug',
			'terms' => esc_attr( $type ), // array( $category )
			'operator' => 'IN'
		);
	}
	
	if ( sizeof( $tax_queries ) > 1 ) {
		$args['tax_query'] = $tax_queries;
	}
	
	// Max
	$args['numberposts'] = ( ! empty ( $max ) ) ? esc_attr( $max ) : '-1';
	
	// Orderby
	if ( !empty ( $orderby ) )
		$args['orderby'] = esc_attr( $orderby );
	
	// Template
	$template = ($template != 'grid' && $template != 'line') ? 'grid' : $template;
	
	// Grid
	$grid = ( ! empty ( $grid ) && is_numeric( $grid ) ) ? esc_attr( $grid ) : 3;
	
	// Vendors
	if ( ! empty ( $vendor ) && $vendor !='null' ) {
		$vendor = explode( ',', $vendor );
		$vendors_query = array(
			'relation' => 'IN',
			array(
				'key'       => AFFILIATE_PROMOTIONS_PREFIX.'promotion_vendor',
				'value'     => $vendor,
				'compare'   => 'IN',
			),
		);
		if ( ! empty($args['meta_query']) ) {
			$meta_query = $args['meta_query'];
			
			$args['meta_query'] = array(
				'relation' => 'AND',
				$meta_query,
				$vendors_query
			);
		}
	}
	
	// The Query
	$posts = new WP_Query( $args );
	
	ob_start();
	
	if ( $posts->have_posts() ) {
		// Get popup template file
		$popup_file = affpromos_get_template_file( 'code-popup','promotions' );
		
		// Get template file
		$file = affpromos_get_template_file( 'promotions-'.$template, 'promotions' );
		
		echo '<div class="affpromos">';
		
		if ($file) {
			if ($popup_file) {
				include_once ($popup_file);
			}
			include( $file );
		} else {
			_e('Template not found.', AFFILIATE_PROMOTIONS_PLUG);
		}
		echo '</div>';
		?>
		<?php
	} else {
		echo '<p>' . __('No promotions found.', AFFILIATE_PROMOTIONS_PLUG) . '</p>';
	}
	
	$str = ob_get_clean();
	
	// Restore original Post Data
	wp_reset_postdata();
	
	return $str;
}


function crawl_content ($url){
	$offer = new ProductCrawler();
	return $offer->open_site_conc($url);
}

function aff_add_product_shortcode( $atts, $content = '' ) {
	
	if ( empty( $atts ) ) {
		return;
	}
	
	global $wpdb;
	extract( shortcode_atts( array(
		'id'            => null,
		'url'           => null,
		'align'         => 'right',
		'sticker'       => 'Staff pick',
		'sub_header'    => 'The best option',
	), $atts ) );
	
	if($id == null && $url == null ){
		echo '<p>' . __('No offer identifier provided.', AFFILIATE_PROMOTIONS_PLUG) . '</p>';
		return'';
	}
	
	$options = affpromos_get_options();
	
	if (isset($url)){
		$sql = "SELECT post_id FROM $wpdb->postmeta WHERE ( meta_value LIKE '".$url."');" ;
		$res = $wpdb->get_results($sql,OBJECT);
		
		if(!empty( $res ) ){
			$id = $res[0]->post_id;
		} else {
			$res = crawl_content($url);
			if (isset($res['error'] )){
				echo '<p>'.__('Error : <b>'.$res['error'].'</b>', AFFILIATE_PROMOTIONS_PLUG) .'</p>';
				wp_reset_postdata();
				return '';
			}
			$id = $res['offer_id'];
		}
	}
	
	$post = get_post($id);
	
	ob_start();
	
	$shortcode_content = "";
	
	if ( $post != null  ) {
		
		$file = affpromos_get_template_file( 'product-card', 'promotions' );
		echo '<div class="affpromos">';
		if ( $file ) {
			include( $file );
		} else {
			_e('Template not found.', AFFILIATE_PROMOTIONS_PLUG);
		}
		
		echo '</div>';
	} else {
		echo '<p>' . __('No promotions found.', AFFILIATE_PROMOTIONS_PLUG) . '</p>';
	}
	
	$shortcode_content = ob_get_clean();
	
	// Restore original Post Data
	wp_reset_postdata();
	
	return $shortcode_content;
}

function add_shortcode_ui() {
	if( ! function_exists( 'shortcode_ui_register_for_shortcode' ) )
		return;
	
	shortcode_ui_register_for_shortcode(
		'aff-product',
		array(
			'label' => esc_html__( 'Affiliate Offer', 'shortcode-ui-example' ),
			'listItemImage' => 'dashicons-editor-quote',
			'attrs'          => array(
				array(
					'label'        => 'ID of Offer',
					'attr'         => 'id',
					'type'         => 'text',
				),
				array(
					'label'        => 'Link to the Offer',
					'attr'         => 'url',
					'type'         => 'url',
					'description'  => 'Full URL',
				
				),
				array(
					'label'        => 'Sticker label',
					'attr'         => 'sticker',
					'type'         => 'text',
				),
				array(
					'label'        => 'Subheader text',
					'attr'         => 'sub_header',
					'type'         => 'text',
				),
				array(
					'label'       => esc_html__( 'Alignment', 'shortcode-ui-example' ),
					'description' => esc_html__( 'Whether the quotation should be displayed as pull-left, pull-right', 'shortcode-ui-example' ),
					'attr'        => 'align',
					'type'        => 'select',
					'options'     => array(
						array( 'value' => 'right', 'label' => esc_html__( 'Pull Right', 'shortcode-ui-example' ) ),
						array( 'value' => 'left', 'label' => esc_html__( 'Pull Left', 'shortcode-ui-example' ) ),
						array( 'value' => 'block', 'label' => esc_html__( 'Block', 'shortcode-ui-example' ) ),
					
					),
				),
			),
			
			'post_type'     => array( 'post', 'page' ),
		)
	);
	
	shortcode_ui_register_for_shortcode(
		'aff-promotions',
		array(
			'label' => esc_html__( 'Affiliate Multiple Promotion', 'shortcode-ui-example' ),
			'listItemImage' => 'dashicons-flag',
			'attrs'          => array(
				array(
					'label'         => 'Search keyword',
					'description'   => 'Input keyword to apply filter',
					'attr'          => 'search',
					'placeholder'   => 'search',
					'type'          => 'text',
				),
				array(
					'label'         => 'Templates',
					'attr'          => 'template',
					'type'          => 'select',
					'options'       => array(
						array( 'value' => 'grid', 'label' => esc_html__( 'Grid ', 'shortcode-ui-example' ) ),
						array( 'value' => 'line', 'label' => esc_html__( 'Line ', 'shortcode-ui-example' ) ),
					),
				),
				array(
					'label'         => __('Max number of promotions',AFFILIATE_PROMOTIONS_PLUG),
					'attr'          => 'max',
					'type'          => 'number',
					'meta'          => array(
						'placeholder'   => 'Max is 20',
					),
				),
				array(
					'label'         => 'Vendor',
					'description'   => 'Select vendor to apply filter',
					'attr'          => 'vendor',
					'type'          => 'post_select',
					'query'         => array('post_type' => 'affpromos_vendor'),
					'multiple'      => true,
				),
				array(
					'label'        => 'Promotion category',
					'description'  => 'Select promotion to apply filter',
					'attr'         => 'category',
					'type'         => 'term_select',
					'taxonomy'     => 'affpromos_category',
					'multiple'     => true,
				),
				array(
					'label'        => 'Item each row ',
					'attr'         => 'grid',
					'description'  => 'Only if use "grid" template',
					'type'         => 'select',
					'options'      => array(
						array( 'value' => '3', 'label' => esc_html__( '3 items', 'shortcode-ui-example' ) ),
						array( 'value' => '2', 'label' => esc_html__( '2 items', 'shortcode-ui-example' ) ),
						array( 'value' => '4', 'label' => esc_html__( '4 items', 'shortcode-ui-example' ) ),
					),
				),
				array(
					'label'         => 'Type of promotions ',
					'attr'          => 'type',
					'type'          => 'select',
					'options'       => array(
						array( 'value' => '', 'label' => esc_html__( 'All types', 'shortcode-ui-example' ) ),
						array( 'value' => 'promotion-type', 'label' => esc_html__( 'Promotions only', 'shortcode-ui-example' ) ),
						array( 'value' => 'coupon-type', 'label' => esc_html__( 'Coupon only', 'shortcode-ui-example' ) ),
					),
				),
				array(
					'label'        => 'Hide the expired Promotion',
					'attr'         => 'hide_expired',
					'type'         => 'checkbox',
				),
			),
			'post_type'=> array( 'post', 'page' ),
		)
	);
	
	shortcode_ui_register_for_shortcode(
		'aff-offers',
		array(
			'label' => esc_html__( 'Affiliate Multiple Offers', 'shortcode-ui-example' ),
			'listItemImage' => 'dashicons-lightbulb',
			'attrs'          => array(
				array(
					'label'         => 'Templates',
					'attr'          => 'template',
					'type'          => 'select',
					'options'       => array(
						array( 'value' => 'grid', 'label' => esc_html__( 'Grid ', 'shortcode-ui-example' ) ),
						array( 'value' => 'line', 'label' => esc_html__( 'Line ', 'shortcode-ui-example' ) ),
					),
				),
				array(
					'label'        => 'Search keyword',
					'description'  => 'Input keyword to apply filter',
					'attr'         => 'search',
					'type'         => 'text',
				),
				array(
					'label'        => 'Max number of offers',
					'attr'         => 'max',
					'type'         => 'number',
					'meta'   => array(
						'placeholder' => 'Get all if blank',
					),
				),
				array(
					'label'    => 'Select Vendor',
					'attr'     => 'vendor',
					'type'     => 'post_select',
					'query'    => array( 'post_type' => 'affpromos_vendor' ),
					'multiple' => true,
				),
				array(
					'label'        => 'Offer Category',
					'attr'         => 'category',
					'type'         => 'term_select',
					'taxonomy'     => 'affpromos_category',
					'multiple'     => true,
					'description'  => 'Type in to search'
				),
				array(
					'label'        => 'Item each row',
					'attr'         => 'grid',
					'description'  => 'Only apply for "grid" template',
					'type'         => 'select',
					'options'      => array(
						array( 'value' => 2, 'label' => esc_html__( '2 items', 'bs' ) ),
						array( 'value' => 3, 'label' => esc_html__( '3 items', 'bs' ) ),
						array( 'value' => 4, 'label' => esc_html__( '4 items', 'bs' ) ),
						array( 'value' => 1, 'label' => esc_html__( '1 items', 'bs' ) ),
					),
				),
				array(
					'label'        => 'Hide the expired offers',
					'attr'         => 'hide_expired',
					'type'         => 'checkbox',
				),
			),
			'post_type'     => array( 'post', 'page' ),
		)
	);
	
}

function add_custom_css_shortcode_editor() {
	add_editor_style( plugins_url('public/assets/css/product-shortcode.css', dirname(__FILE__)) );
	add_editor_style( plugins_url('public/assets/css/promos-line.css', dirname(__FILE__)) );
	add_editor_style( plugins_url('public/assets/css/product-shortcode.css', dirname(__FILE__)) );
	add_editor_style( plugins_url('public/assets/css/styles.min.css', dirname(__FILE__)) );
	add_editor_style( plugins_url('public/assets/css/offer-grid.css', dirname(__FILE__)) );
	add_editor_style( plugins_url('public/assets/css/boostrap-grid.css', dirname(__FILE__)) );
	
	if(!wp_script_is('promotions-line-postload','enqueued')){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'promotions-line-postload', plugins_url( '/public/assets/js/promotions-line-postload.js', dirname(__FILE__) ), array(), '0.1', true );
	}
}

add_shortcode('aff-promotions', 'affpromos_add_promotions_shortcode');
add_shortcode('aff-product', 'aff_add_product_shortcode');
add_action('init','add_custom_css_shortcode_editor' );
add_action('init','add_shortcode_ui');
