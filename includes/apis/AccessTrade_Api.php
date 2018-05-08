<?php
if (!defined('ABSPATH')) exit;

if ( ! class_exists('AccessTrade_Api') ) {
	
	class AccessTrade_Api {
	 
		
		public function __construct($opt=null) {
		    
		    if (!isset($opt))
			    $opt = affpromos_get_options();
			
		    $this->opt                  = $opt;
		    
		    // TODO: Add more platforms beside AccessTrade
			$this->token                = $opt[AFFILIATE_PROMOTIONS_AT_PREFIX.'token'];
			
			$this->api_options          = array(
				'timeout'   => 10,
				'headers'   => array(
					'Content-Type'  =>'application/json',
					'Authorization' =>'Token '.$this->token,
				)
			);
			
			// Because there're tons of them every time
			$this->offer_limit              = $opt[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_limit'] ? $opt[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_limit']
				: 30 ;
			$this->offer_limit              = max($this->offer_limit,30);
			
			$this->offer_vendors            = $opt[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_vendor'] ? $opt[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_vendor']
				: '' ;
			$this->offer_categories         = $opt[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_category'] ? $opt[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_category']
				: '' ;
			
		}
		
		public static function add_feature_image_to_post($image_url,$post_id){
			require_once(ABSPATH . 'wp-admin/includes/media.php');
			$image_id = media_sideload_image($image_url, $post_id,null,'id');
			set_post_thumbnail($post_id,$image_id);
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_image',$image_id);
			
		}
		
		public function run_full_update() {
			$this->sync_vendors_with_local();
			$this->sync_promotions_with_local();
			$this->sync_offers_with_local();
		}
		
		private function admin_notice($mess,$type='success'){
			add_action( 'admin_notices', function() use ($mess,$type) {
				?>
                <div class="notice notice-<?php echo $type; ?> is-dismissible">
                    <p><?php _e( $mess , AFFILIATE_PROMOTIONS_PLUG ); ?></p>
                </div>
				<?php
			}
			);
		}
		
		public function fetch_api($api_url,$params=array()) {
			$api_url = add_query_arg($params, $api_url);
			$resp = wp_remote_get($api_url,$this->api_options);
			return ( is_wp_error( $resp ) ) ? false : json_decode($resp['body']);
		}
		
		//---- Vendor
		public function sync_vendors_with_local() {
			$data = $this->fetch_api(AFFILIATE_PROMOTIONS_ACCESSTRADE_GET_VENDOR_API);
			if ( ! $data ) {
				return;
			}
			
			foreach ($data->data as $vendor){
				$this->process_vendor_data($vendor);
			}
		}
		
		public static function process_vendor_data($vendor) {
			global $wpdb;
			
			$post_type = AFFILIATE_PROMOTIONS_PREFIX.'vendor';
			$vendor_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE (post_title = '{$vendor->merchant}' and post_type = '{$post_type}') ;");
			
			if( $vendor_id == null ){
				$vendor_id = wp_insert_post(array(
					'post_title'    =>      $vendor->merchant,
					'post_name'     =>      $vendor->merchant,
					'post_status'   =>      "publish",
					'post_author'   =>      get_current_user_id(),
					'post_type'     =>      $post_type,
				));
				add_post_meta($vendor_id ,AFFILIATE_PROMOTIONS_PREFIX .'vendor_url',$vendor->url);
				if (isset($vendor->category))
					add_post_meta($vendor_id ,AFFILIATE_PROMOTIONS_PREFIX .'vendor_category',$vendor->category);
			}
			
			return $vendor_id;
		}
		//---- End Vendor
		
		//---- Promotion
		public function sync_promotions_with_local() {
			$data = $this->fetch_api(AFFILIATE_PROMOTIONS_ACCESSTRADE_GET_PROMOTION_API,array(
				'status'    =>1,
			));
			if ( ! $data ) {
				$this->admin_notice(__('Connection error when update promotions ',AFFILIATE_PROMOTIONS_PLUG),'danger');
				return;
			}
			foreach ($data->data as $promotion){
				$this->process_promotion_data($promotion);
			}
			if( count($data->data) > 0){
				$this->admin_notice(__('Updated '.count($data->data).' promotion(s)',AFFILIATE_PROMOTIONS_PLUG));
			}
		}
		
		
		public static function process_promotion_data($promotions){
			global $wpdb;
			
			$meta_id_name = AFFILIATE_PROMOTIONS_PREFIX .'promotion_id';
			$meta_id = AFFILIATE_PROMOTIONS_AT_PREFIX .$promotions->id;
			$post_id = $wpdb->get_var( "SELECT * FROM $wpdb->postmeta WHERE (meta_key = '{$meta_id_name}' and meta_value = '{$meta_id}' );");
			
			
			if ($post_id !== null){
				return $post_id;
			}
			
			$post_type = AFFILIATE_PROMOTIONS_PREFIX.'promotion';
			
			$post_id = wp_insert_post(array(
				'post_title'    =>      $promotions->name,
				'post_status'   =>      "publish",
				'post_content'  =>      $promotions->content,
				'post_type'     =>      $post_type,
			));
			
			AccessTrade_Api::add_feature_image_to_post($promotions->image,$post_id);
			
			$vendor_id = AccessTrade_Api::process_vendor_data((object) array(
				'merchant'  =>$promotions->merchant,
				'url'       =>$promotions->domain,
			));
			
			if( empty($promotions->coupons) ){
				wp_set_object_terms($post_id, 'Promotion' ,AFFILIATE_PROMOTIONS_PREFIX.'promotion_type');
			}else{
				wp_set_object_terms($post_id, 'Coupon' ,AFFILIATE_PROMOTIONS_PREFIX.'promotion_type');
				foreach ($promotions->coupons as $coupon){
					$coupon_code[] = $coupon->coupon_code;
					$coupon_save[] = $coupon->coupon_save;
				}
				add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_code', implode($coupon_code,',') );
				add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_discount', implode($coupon_save,',') );
			}
			foreach ($promotions->categories as $category){
				$cate_id = AccessTrade_Api::process_categories_data($category);
				wp_set_object_terms( $post_id, $cate_id, AFFILIATE_PROMOTIONS_PREFIX . 'category' );
			}
			
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_vendor',$vendor_id);
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_url',$promotions->link);
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_title',$promotions->name);
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_valid_from', DateTime::createFromFormat("Y-m-d", $promotions->start_time)->getTimestamp());
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_valid_until', DateTime::createFromFormat("Y-m-d", $promotions->end_time)->getTimestamp());
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_description',$promotions->content);
			
			add_post_meta($post_id,$meta_id_name,$meta_id);
			
			return $post_id;
		}
		//---- End Promotion
		
		//---- Category
		public static function process_categories_data($category){
			$term_id = get_term_by('slug', $category->category_name, AFFILIATE_PROMOTIONS_PREFIX.'category' );
			
			if( $term_id )
				return $term_id->term_id;
			
			return wp_insert_term ($category->category_name_show,AFFILIATE_PROMOTIONS_PREFIX.'category',
				array(
					'slug' => $category->category_name
				)
			);
			
		}
		//---- End Category
		
		//---- Offers
		public function sync_offers_with_local() {
			
			$params = array(
				'limit'     =>$this->offer_limit,
				'campaign'  =>$this->offer_vendors,
				'cate'      =>$this->offer_categories,
			);
			
			$data = $this->fetch_api(AFFILIATE_PROMOTIONS_ACCESSTRADE_GET_OFFER_API,$params);
			
			if ( ! $data ) {
				$this->admin_notice(__('Connection error when update offers ',AFFILIATE_PROMOTIONS_PLUG),'danger');
				return;
			}
			foreach ($data->data as $offer){
				AccessTrade_Api::process_offer_data($offer);
			}
			
			if( count($data->data) > 0){
				$this->admin_notice(__('Updated '.count($data->data).' offer(s)',AFFILIATE_PROMOTIONS_PLUG));
			}
		}
		
		public static function process_offer_data($offer) {
		 
			global $wpdb;
			
			$meta_id_name = AFFILIATE_PROMOTIONS_PREFIX .'offer_id';
			$meta_id = AFFILIATE_PROMOTIONS_AT_PREFIX .$offer->sku;
			$post_id = $wpdb->get_var( "SELECT * FROM $wpdb->postmeta WHERE (meta_key = '{$meta_id_name}' and meta_value = '{$meta_id}' );");
			if ($post_id !== null){
				return $post_id;
			}
			
			$post_type = AFFILIATE_PROMOTIONS_PREFIX.'offer';
			
			$post_id = wp_insert_post(array(
				'post_title'    =>      $offer->name,
				'post_status'   =>      "publish",
				'post_content'  =>      $offer->desc ? $offer->desc : '',
				'post_type'     =>      $post_type,
			));
			
			AccessTrade_Api::add_feature_image_to_post($offer->image,$post_id);
			
			$vendor_id = AccessTrade_Api::process_vendor_data((object) array(
				'merchant'  =>$offer->merchant,
				'url'       =>$offer->domain,
			));
			
			$cate_id = AccessTrade_Api::process_categories_data((object) array(
				'category_name_show'=>$offer->cate,
				'category_name'     =>$offer->cate,
			));
			
			wp_set_object_terms( $post_id, $cate_id, AFFILIATE_PROMOTIONS_PREFIX . 'category' );

			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_vendor',$vendor_id);
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_url',$offer->url);
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_title',$offer->name);
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_price',$offer->price);
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_price_sale',$offer->discount);
			
			add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_description',$offer->desc);
			
			add_post_meta($post_id,$meta_id_name,$meta_id);
			
			return $post_id;
		}
		
		//---- End Offers
		
	}
}