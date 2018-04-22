<?php
/**
 * Settings
 *
 * Source: https://codex.wordpress.org/Settings_API
 *
 * @package     AffiliatePromotions\Settings
 * @since       1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Include guzzle dependencies
require_once AFFILIATE_PROMOTIONS_DIR . 'includes/libs/vendor/autoload.php';
use GuzzleHttp\Client;

if ( ! class_exists('Affpromos_Settings') ) {
	
	class Affpromos_Settings
	{
		public $options;
		
		public function __construct()
		{
			// Options
			$this->options = affpromos_get_options();
			
			// Initialize
			add_action('admin_menu', array( &$this, 'add_admin_menu') );
			add_action('admin_init', array( &$this, 'init_settings') );
			add_action('admin_init', 'do_admin_action' );
			
		}
		
		function add_admin_menu()
		{
			/*
			 * Source: https://codex.wordpress.org/Function_Reference/add_options_page
			 */
			add_submenu_page(
				'edit.php?post_type=affpromos_promotion',
				__( 'Affiliate Promotions - Settings', AFFILIATE_PROMOTIONS_PLUG ), // Page title
				__( 'Settings', AFFILIATE_PROMOTIONS_PLUG ), // Menu title
				'manage_options', // Capabilities
				'affpromos_settings', // Menu slug
				array( &$this, 'options_page' ) // Callback
			);
			
		}
		
		function init_settings()
		{
			register_setting(
				'affpromos_settings',
				'affpromos_settings',
				array( &$this, 'validate_input_callback' )
			);
			
			// SECTION: Quickstart
			add_settings_section(
				'affpromos_settings_section_quickstart',
				__('Quickstart Guide', AFFILIATE_PROMOTIONS_PLUG),
				array( &$this, 'section_quickstart_render' ),
				'affpromos_settings'
			);
			
			
			// SECTION TWO
			add_settings_section(
				'affpromos_settings_section_promotions',
				__('Promotions', AFFILIATE_PROMOTIONS_PLUG),
				array( &$this, 'section_two_render' ), // Optional you can output a description for each section
				'affpromos_settings'
			);
			
			add_settings_field(
				'affpromos_settings_promotion_aff_token',
				__('AccessTrade Access Token', AFFILIATE_PROMOTIONS_PLUG),
				array(&$this, 'promotion_aff_token_at_render'),
				'affpromos_settings',
				'affpromos_settings_section_promotions'
			);
			
			add_settings_field(
				'affpromos_settings_promotion_auto_update',
				__('Auto Update', AFFILIATE_PROMOTIONS_PLUG),
				array(&$this, 'promotion_auto_update_render'),
				'affpromos_settings',
				'affpromos_settings_section_promotions'
			);
			
			add_settings_field(
				'affpromos_settings_promotion_lifetime',
				__('Expiration', AFFILIATE_PROMOTIONS_PLUG),
				array(&$this, 'promotion_lifetime_render'),
				'affpromos_settings',
				'affpromos_settings_section_promotions'
			);
			add_settings_field(
				'affpromos_settings_aff_omit_offer_update',
				__('Not update Offers', AFFILIATE_PROMOTIONS_PLUG),
				array(&$this, 'promotion_aff_omit_offer_update_render'),
				'affpromos_settings',
				'affpromos_settings_section_promotions'
			);
		}
		
		function validate_input_callback( $input ) {
			
			/*
			 * Here you can validate (and manipulate) the user input before saving to the database
			 */
			
			return $input;
		}
		
		function section_quickstart_render() {
			?>
            <div class="postbox">
                <h3 class='hndle'><?php _e('Quickstart Guide', AFFILIATE_PROMOTIONS_PLUG); ?></h3>
                <div class="inside">
                    <p>
                        <strong><?php _e( 'First Steps', AFFILIATE_PROMOTIONS_PLUG ); ?></strong>
                    </p>
                    <ol>
                        <li><?php _e( 'Create vendors', AFFILIATE_PROMOTIONS_PLUG ); ?></li>
                        <li><?php _e( 'Create promotions', AFFILIATE_PROMOTIONS_PLUG ); ?></li>
                        <li><?php _e( 'Link promotions to vendors', AFFILIATE_PROMOTIONS_PLUG ); ?></li>
                        <li><?php _e( 'Assign categories and/or types to promotions if needed', AFFILIATE_PROMOTIONS_PLUG ); ?></li>
                        <li><?php _e( 'Display promotions inside your posts/pages by using shortcodes', AFFILIATE_PROMOTIONS_PLUG ); ?></li>
                        <li><?php _e( 'Or just use the', AFFILIATE_PROMOTIONS_PLUG ); ?> <strong>Add Post Element</strong><?php _e( ' in Post edit section', AFFILIATE_PROMOTIONS_PLUG ); ?></li>

                    </ol>

                    <p>
                        <strong><?php _e( 'Displaying promotions', AFFILIATE_PROMOTIONS_PLUG ); ?></strong>
                    </p>
                    <p>
                        <code>[aff-promotions]</code>
                    </p>
					<?php _e( 'By passing the category/type id or slug you can filter the results individually. Limiting items get by passing max :', AFFILIATE_PROMOTIONS_PLUG ); ?>
                    <p>
                        <code>[aff-promotions category="group-xyz" type="type-abc" max="10"  hide_expired="true"]</code>
                    </p>
					<?php _e( 'And templates (default will be grid template). Passing grid for number of items each row : ', AFFILIATE_PROMOTIONS_PLUG ); ?>
                    <p>
                        <code>[aff-promotions template="grid" grid="4"]</code> <?php _e( 'or', AFFILIATE_PROMOTIONS_PLUG ); ?> <code>[affpromos_promotions template="line"]</code>
                    </p>
                    <hr>
                    <p>
                        <strong><?php _e( 'Multiple offers ', AFFILIATE_PROMOTIONS_PLUG ); ?></strong><br />
                    </p>
                    <p>
                        <code>[aff-offers category="group-xyz" type="type-abc" max="10" hide_expired="true"]</code>
                    </p>
					<?php _e( 'just "grid" template : ', AFFILIATE_PROMOTIONS_PLUG ); ?>
                    <p>
                        <code>[aff-offers grid="3"]</code>
                    </p>
                    <hr>
                    <p>
                        <strong><?php _e( 'Single offer', AFFILIATE_PROMOTIONS_PLUG ); ?></strong><br />
                    </p>
					<?php _e( 'You can display offer by passing id of the offer and add sticker or sub_header for it : ', AFFILIATE_PROMOTIONS_PLUG ); ?>
                    <p>
                        <code>[aff-product id="101" sticker="Best product ever" sub_header="You'll need this"]</code>
                    </p>
					<?php _e( 'or url to the detail page of the offer (will ignore id) : ', AFFILIATE_PROMOTIONS_PLUG ); ?>
                    <p>
                        <code>[aff-product url="https://www.adayroi.com/apple-iphone-7-32gb-bac-hang-nhap-khau-p-dRa09-f1-2?pi=wayRB" sticker="Buy this" sub_header="Please !" ]</code>
                    </p>
                    <br>
					
					<?php do_action( 'affpromos_settings_quickstart_render' ); ?>
                </div>
            </div>
			
			<?php
		}
		
		
		function promotion_aff_token_at_render() {
			
			
			$aff_at_token = isset ( $this->options[AFFILIATE_PROMOTIONS_AT_PREFIX.'token'] ) ? $this->options[AFFILIATE_PROMOTIONS_AT_PREFIX.'token'] : 'XXX';
			?>

            <input type="text" id="aff_token_input" name="affpromos_settings[<?php echo AFFILIATE_PROMOTIONS_AT_PREFIX.'token';?>]" value="<?php echo $aff_at_token; ?>" />

            <label for="affpromos_settings_promotion_aff_token"><?php _e('Get your') ?> <a href="<?php echo AFFILIATE_PROMOTIONS_ACCESSTRADE_TOKEN_PAGE; ?>"><?php _e('Here',AFFILIATE_PROMOTIONS_PLUG) ?></a></label>
			<?php
		}
		
		function promotion_auto_update_render() {
			
			$manually_update_link = admin_url( 'edit.php?post_type=affpromos_promotion&page=affpromos_settings&action=update_promotions' );
			$auto_update_promotions = ( isset ( $this->options['auto_update_promotions'] ) && $this->options['auto_update_promotions'] == '1' ) ? 1 : 0;
			?>

            <input type="checkbox" id="affpromos_settings_auto_pdate_promotions" name="affpromos_settings[auto_update_promotions]" value="1" <?php echo($auto_update_promotions == 1 ? 'checked' : ''); ?> />
            <label for="affpromos_settings_auto_pdate_promotions"><?php _e('Auto update latest promotions every '.AFFILIATE_AUTO_UPDATE_HOURS_PER_UPDATE.' hours (Click ', AFFILIATE_PROMOTIONS_PLUG); ?> <a href="<?php echo $manually_update_link; ?>"> here</a> to update manualy).</label>
			<?php
		}
		
		function promotion_lifetime_render() {
			
			$hide_expired_promotions = ( isset ( $this->options['hide_expired_promotions'] ) && $this->options['hide_expired_promotions'] == '1' ) ? 1 : 0;
			?>

            <input type="checkbox" id="affpromos_settings_hide_expired_promotions" name="affpromos_settings[hide_expired_promotions]" value="1" <?php echo($hide_expired_promotions == 1 ? 'checked' : ''); ?> />
            <label for="affpromos_settings_hide_expired_promotions"><?php _e('Hide promotions after they expired', AFFILIATE_PROMOTIONS_PLUG); ?></label>
			<?php
		}
		
		function promotion_aff_omit_offer_update_render() {
			
			$aff_omit_offer_update = ( isset ( $this->options['aff_omit_offer_update'] ) && $this->options['aff_omit_offer_update'] == '1' ) ? 1 : 0;
			?>

            <input type="checkbox" id="affpromos_settings_aff_omit_offer_update" name="affpromos_settings[aff_omit_offer_update]" value="1" <?php echo($aff_omit_offer_update == 1 ? 'checked' : ''); ?> />
            <label for="affpromos_settings_aff_omit_offer_update"><?php _e('Will omit Offer when update items (too much will slow down your web)', AFFILIATE_PROMOTIONS_PLUG); ?></label>
			<?php
		}
		// TODO: Dummies
		
		function text_field_01_render() {
			
			$text = ( ! empty($this->options['text_01'] ) ) ? esc_attr( trim($this->options['text_01'] ) ) : ''
			
			?>
            <input type="text" name="affpromos_settings[text_01]" id="affpromos_settings_text_field_01" value="<?php echo esc_attr( trim( $text ) ); ?>" />
			<?php
		}
		
		function select_field_01_render() {
			
			$select_options = array(
				'0' => __('Please select...', AFFILIATE_PROMOTIONS_PLUG),
				'1' => __('Option One', AFFILIATE_PROMOTIONS_PLUG),
				'2' => __('Option Two', AFFILIATE_PROMOTIONS_PLUG),
				'3' => __('Option Three', AFFILIATE_PROMOTIONS_PLUG)
			);
			
			$selected = ( isset ( $this->options['select_01'] ) ) ? $this->options['select_01'] : '0';
			
			?>
            <select id="affpromos_settings_select_field_01" name="affpromos_settings[select_01]">
				<?php foreach ( $select_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $selected, $key ); ?>><?php echo $label; ?></option>
				<?php } ?>
            </select>
			<?php
		}
		
		function checkbox_field_01_render() {
			
			$checked = ( isset ( $this->options['checkbox_01'] ) && $this->options['checkbox_01'] == '1' ) ? 1 : 0;
			?>

            <input type="checkbox" id="affpromos_settings_checkbox_field_01" name="affpromos_settings[checkbox_01]" value="1" <?php echo($checked == 1 ? 'checked' : ''); ?> />
            <label for="affpromos_settings_checkbox_field_01"><?php _e('Activate in order to do some cool stuff.', AFFILIATE_PROMOTIONS_PLUG); ?></label>
			<?php
		}
		
		function text_field_02_render() {
			
			$text = ( ! empty($this->options['text_02'] ) ) ? esc_attr( trim($this->options['text_02'] ) ) : ''
			
			?>
            <input type="text" name="affpromos_settings[text_02]" id="affpromos_settings_text_field_02" value="<?php echo esc_attr( trim( $text ) ); ?>" />
			<?php
		}
		
		function options_page() {
			?>
            <div class="wrap">
				<?php screen_icon(); ?>
                <h2><?php _e('Affiliate Promotions', AFFILIATE_PROMOTIONS_PLUG); ?>
					<?php if( isset($_GET['aff_page']) && $_GET['aff_page'] == 'logs' ) {
						unset($_GET['action']);
						unset($_GET['aff_page']);
						?>
                        <a href="<?php echo ("?" . http_build_query($_GET)); ?>" class="button-primary"><?php _e('View Settings', AFFILIATE_PROMOTIONS_PLUG); ?></a>
					
					<?php } else {
						unset($_GET['action']);
						$_GET['aff_page'] = 'logs';
						?>
                        <a href="<?php echo ("?" . http_build_query($_GET)); ?>" class="button-primary"><?php _e('View Logs', AFFILIATE_PROMOTIONS_PLUG); ?></a>
					<?php } ?>

                </h2>


                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <div class="meta-box-sortables ui-sortable">
                                <form action="options.php" method="post">
									<?php
									
									settings_fields('affpromos_settings');
									if( isset($_GET['aff_page']) && $_GET['aff_page'] == 'logs' ) {
										affpromos_do_settings_sections('affpromos_settings');
										?>
                                        <p><?php submit_button('Save Changes', 'button-primary', 'submit', false); ?></p>
										<?php
									}else{
										affpromos_logs_sections();
										
									}?>
                                </form>
                            </div>

                        </div>
                        <!-- /#post-body-content -->
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="meta-box-sortables">
								<?php
								/*
								 * require_once WP_UDEMY_DIR . 'includes/libs/flowdee_infobox.php';
								$flowdee_infobox = new Flowdee_Infobox();
								$flowdee_infobox->set_plugin_slug('udemy');
								$flowdee_infobox->display();
								*/
								?>
                            </div>
                            <!-- /.meta-box-sortables -->
                        </div>
                        <!-- /.postbox-container -->
                    </div>
                </div>
            </div>
			<?php
			
			
		}
		
		
	}
}

$aff_settings = new Affpromos_Settings();
/*
 * Custom settings section output
 *
 * Replacing: do_settings_sections('affpromos_settings');
 */
function affpromos_do_settings_sections( $page ) {
	
	global $wp_settings_sections, $wp_settings_fields;
	
	if (!isset($wp_settings_sections[$page]))
		return;
	
	foreach ((array)$wp_settings_sections[$page] as $section) {
		
		$title = '';
		
		if ($section['title'])
			$title = "<h3 class='handle'>{$section['title']}</h3>\n";
		
		if ($section['callback'])
			call_user_func($section['callback'], $section);
		
		if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]))
			continue;
		
		echo '<div class="postbox">';
		echo $title;
		echo '<div class="inside">';
		echo '<table class="form-table">';
		do_settings_fields($page, $section['id']);
		echo '</table>';
		echo '</div>';
		echo '</div>';
	}
}



function affpromos_logs_sections(){
	
	$def_day = 7;
	$data = affpromos_query_logs_from($def_day);
	
	echo '<div class="postbox">';
	echo "<h3 class='handle'>Logs </h3>\n";
	
	echo '<div class="inside">';
	echo '<table class="form-table">';
	foreach ($data as $item){
		echo "<div>(". $item->time.") :: <strong>".$item->message." </strong> | total: ". $item->amount ." items  -- ". $item->status." </div>
        <hr>";
	}
	if (count($data)==0){
		echo '<strong>No logs recorded !!</strong>';
	}
	echo '</table>';
	echo '</div>';
	echo '</div>';
	
}

function init_promotion_type()
{
	$term_coupon = term_exists( 'Coupon', AFFILIATE_PROMOTIONS_PREFIX.'promotion_type' );
	$term_promotion = term_exists( 'Promotion', AFFILIATE_PROMOTIONS_PREFIX.'promotion_type' );
	
	
	if( $term_coupon == 0 || $term_coupon == null)
	{
		$type = wp_insert_term ('Coupon',AFFILIATE_PROMOTIONS_PREFIX.'promotion_type',
			array(
				'description'=> 'coupon',
				'slug' => 'coupon-type',
			)
		);
	}
	
	if( $term_promotion == 0 || $term_promotion == null)
	{
		$type = wp_insert_term ('Promotion',AFFILIATE_PROMOTIONS_PREFIX.'promotion_type',
			array(
				'description'=> 'promotion',
				'slug' => 'promotion-type',
			)
		);
	}
	
}
//---- Action for chuck

function _defaul($obj,$def){
	return isset($obj) ? $obj : $def;
}

function update_category($token='')
{
	
	$opt_category_timestamp = AFFILIATE_PROMOTIONS_PREFIX . 'last_update_category_timestamp';
	
	$category_timestamp = get_option($opt_category_timestamp, 0);
//    $category_timestamp =0;
	
	$get_categories_url = AFFILIATE_PROMOTIONS_API."categories/";
	
	$get_categories_url = add_query_arg( array(
		'from'      =>      $category_timestamp,
		'client'    =>      AFFILIATE_PROMOTIONS_VER,
		'token'     =>      $token,
	
	), $get_categories_url );
	
	
	$client = new Client();
	
	try {
		$response = $client->request('GET', $get_categories_url,['http_errors' => false]);
	}catch (Exception $e) {
		return ['error'=>'Connection error ! API Category '];
	}
	if ($response->getStatusCode() != 200){
		$log_data = (object) array(
			'item'=>'category',
			'status'=>$response->getStatusCode(),
			'message'=> $response->getBody()->getContents(),
		);
		affpromos_log_action($log_data);
		return ['error'=>'Connection error ! '.$get_categories_url];
		
	}
	$json_res = json_decode($response->getBody());
	
	if ($json_res->status == "success") {
		foreach ($json_res->data as $data) {
			
			$term = term_exists( $data->guid, 'affpromos_category' );
			
			if( $term !== 0 && $term !== null)
				continue;
			
			$cate_id = wp_insert_term ($data->name,'affpromos_category',
				array(
					'description'=> $data->desc,
					'slug' => $data->guid,
				)
			);
		}
		update_option( $opt_category_timestamp, time());
		$log_data = (object) array(
			'item'=>'category',
			'status'=>$json_res->status,
			'amount'=>count($json_res->data),
		);
		affpromos_log_action($log_data);
		return array(
			'item_count'=>count($json_res->data)
		);
	}else {
		$log_data = (object) array(
			'item'=>'category',
			'status'=>$json_res->status,
			'message'=>$json_res->message,
		);
		affpromos_log_action($log_data);
		
		return array(
			'error' => $json_res->message
		);
	}
}

function update_offer($chuck_num=25, $token=''){
	$opt_offer_name = AFFILIATE_PROMOTIONS_PREFIX . 'last_update_offer_timestamp';
	$opt = affpromos_get_options();
	if (_defaul($opt['aff_omit_offer_update'],'0') == '1'){
		update_option( $opt_offer_name, time());
		update_option( AFFILIATE_PROMOTIONS_PREFIX.'last_manualy_update', time());
		return[ 'item_count'=>0 ];
	}
	
	$offer_timestamp = get_option($opt_offer_name, 0);
	
	$get_offers_url = AFFILIATE_PROMOTIONS_API."offers/";
	
	$get_offers_url = add_query_arg( array(
		'from'      =>      $offer_timestamp,
		'client'    =>      AFFILIATE_PROMOTIONS_VER,
		'token'     =>      $token,
	
	), $get_offers_url );
	
	$client = new Client();
	try {
		$response = $client->request('GET', $get_offers_url,['http_errors' => false]);
	}catch (Exception $e) {
		return ['error'=>'Connection error ! API Offer'];
	}
	if ($response->getStatusCode() != 200){
		$log_data = (object) array(
			'item'=>'offer',
			'status'=>$response->getStatusCode(),
			'message'=> $response->getBody()->getContents(),
		);
		affpromos_log_action($log_data);
		return ['error'=>'Connection error ! '.$get_offers_url];
		
	}
	$json_res = json_decode($response->getBody());
	
	if ($json_res->status == "success") {
		if (count($json_res->data) >= $chuck_num )
		{
			$json_res->data = array_slice($json_res->data,0,$chuck_num);
		}else
		{
			update_option( $opt_offer_name, time());
			update_option( AFFILIATE_PROMOTIONS_PREFIX.'last_manualy_update', time());
		}
		foreach ($json_res->data as $data) {
			insert_new_offer($data);
		}
		update_option($opt_offer_name,$json_res->latest_offer+1);
		$log_data = (object) array(
			'item'=>'offer',
			'status'=>$json_res->status,
			'amount'=>count($json_res->data),
		);
		affpromos_log_action($log_data);
		return array(
			'item_count'=>count($json_res->data)
		);
		
	}else{
		// ERROR
		$log_data = (object) array(
			'item'=>'offer',
			'status'=>$json_res->status,
			'message'=>$json_res->message,
		);
		affpromos_log_action($log_data);
		
		return array(
			'error'=>$json_res->message
		);
	}
}

function update_promotions($chuck_num=35,$token='')
{
	
	$opt_promotion_name = AFFILIATE_PROMOTIONS_PREFIX . 'last_update_promos_timestamp';
	
	$promotions_timestamp = get_option($opt_promotion_name,0);
//    $promotions_timestamp = 0;
	
	$get_promotions_url = AFFILIATE_PROMOTIONS_API."promos/" ;
	
	$get_promotions_url = add_query_arg( array(
		'from'      =>      $promotions_timestamp,
		'client'    =>      AFFILIATE_PROMOTIONS_VER,
		'token'     =>      $token,
	
	), $get_promotions_url );
	
	
	$client = new Client();
	try {
		$response = $client->request('GET',$get_promotions_url,['http_errors' => false]);
	}catch (Exception $e) {
		return ['error'=>'Connection error ! API Promotion'];
	}
	if ($response->getStatusCode() != 200){
		$log_data = (object) array(
			'item'=>'promotion',
			'status'=>$response->getStatusCode(),
			'message'=> $response->getBody()->getContents(),
		);
		affpromos_log_action($log_data);
		return ['error'=>'Connection error ! '.$get_promotions_url];
	}
	
	$json_res  = json_decode($response->getBody());
	
	if( $json_res->status=="success" ){
		if (count($json_res->data) > $chuck_num )
		{
			$json_res->data = array_slice($json_res->data,0,$chuck_num);
		}else
		{
			update_option( $opt_promotion_name, time());
		}
		foreach ($json_res->data as $data) {
			insert_new_promotion($data);
			update_option( $opt_promotion_name, $data->modified_timestamp+1);
		}
		
		$log_data = (object) array(
			'item'=>'promotion',
			'status'=>$json_res->status,
			'amount'=>count($json_res->data),
		);
		affpromos_log_action($log_data);
		
		return array(
			'item_count'=>count($json_res->data)
		);
	}else{
		// ERROR
		$log_data = (object) array(
			'item'=>'promotion',
			'status'=>$json_res->status,
			'message'=>$json_res->message,
		);
		affpromos_log_action($log_data);
		
		return array(
			'error'=>$json_res->message
		);
	}
	
	//-----------------------------
}

function insert_new_offer($data)
{
//    global $wpdb;
	$client = new Client();
	
	$wp_post_offer = array(
		'post_title'    =>      $data->title,
		'post_status'   =>      "publish",
		'post_author'   =>      get_current_user_id(),
		'post_content'  =>      $data->desc,
		'post_excerpt'  =>      $data->short_desc,
		'post_type'     =>      "affpromos_offer",
	);
	$post_id = wp_insert_post($wp_post_offer);
	
	//-----------Save image from url
	$image_url = $data->featured_image;
	$image_name = substr($image_url,strripos($image_url,'/')+1);
	$upload_dir = wp_upload_dir()['path'].'/'.$image_name;
	
	if(! file_exists($upload_dir)) {
		$file_image = fopen($upload_dir, 'wb');
		$client->request('GET', $image_url, ['sink' => $file_image]);
		if(is_resource($file_image))
			fclose($file_image);
	}
	
	//-----------Save image post as attachment
	$image_name_save = substr($image_name,0,strripos($image_name,'.'));
	$wp_attachment_post = array(
		'post_title'    =>      $image_name_save,
		'post_status'   =>      "inherit",
		'post_author'   =>      get_current_user_id(),
		'ping_status'   =>      "closed",
		'post_name'     =>      $image_name_save,
		'post_parent'   =>      $post_id ,
		'post_type'     =>      "attachment",
		'post_mime_type'=>      "image/".substr($image_name,(strripos($image_name,'.')+1)),
		'guid'          =>      wp_upload_dir()['url'].'/'.$image_name,
	);
	$image_id = wp_insert_attachment($wp_attachment_post,$upload_dir);
	
	//------------Image meta data
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$image_meta = wp_generate_attachment_metadata($image_id, $upload_dir);
	wp_update_attachment_metadata( $image_id,  $image_meta );
	set_post_thumbnail($post_id,$image_id);
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_image_url',$data->thumbnail);
	
	//---- Offer vendor
	insert_new_vendor($data->vendor);
	
	//---- Offer categories
	if (isset($data->category_id)) {
		$cate = get_term_by( 'slug', $data->category_id, AFFILIATE_PROMOTIONS_PREFIX . 'category' );
		wp_set_object_terms( $post_id, $cate->term_id, AFFILIATE_PROMOTIONS_PREFIX . 'category' );
	}
	
	//---- Offer URL
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_url',$data->url);
	
	//---- Offer vendor
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_vendor',$vendor_id);
	
	//---- Offer specs
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_specs',$data->specs);
	
	//---- Offer prices
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_price',$data->price);
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_price_sale',$data->sale_price);
	
	//---- Valid time
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_valid_from', intval($data->start_timestamp));
	
	if(intval($data->expiration_timestamp) == 99999999999 )
		$data->expiration_timestamp = time() + 3600*24* 30 ;  // 30 days from now
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'offer_valid_until', intval($data->expiration_timestamp));
	return $post_id;
}

function insert_new_vendor($data){
	global $wpdb;
	$client = new Client();
	
	//--------- Insert vendor
	
	// Some vendors may not have all the info so just the name is ok too
	$full_info = (isset($data->name));
	
	$vendor_name = $full_info ? $data->name : $data ;
	
	$old_vendors = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE (post_title = '" . $vendor_name . "' and post_type='affpromos_vendor');");
	
	if( $old_vendors != null ){
		return $old_vendors;
	}
	$wp_vendor_post = array(
		'post_title'    =>      $vendor_name,
		'post_name'     =>      $vendor_name,
		'post_status'   =>      "publish",
		'post_author'   =>      get_current_user_id(),
		'post_excerpt'  =>      $full_info ? $data->desc : '',
		'post_type'     =>      "affpromos_vendor",
	);
	$vendor_id = wp_insert_post($wp_vendor_post);
	
	if (!$full_info)
		return $vendor_id;
	//-------- Upload logo of vendor
	$image_url = AFFILIATE_PROMOTIONS_HOST.$data->logo_url;
	
	$image_name = substr($image_url,strripos($image_url,'/')+1);
	$upload_dir = wp_upload_dir()['path'].'/'.$image_name;
	
	if(! file_exists($upload_dir)) {
		$file_image = fopen($upload_dir, 'wb');
		$client->request('GET', $image_url, ['sink' => $file_image]);
		if(is_resource($file_image))
			fclose($file_image);
	}
	//--------- Logo meta data
	$image_name_save = substr($image_name,0,strripos($image_name,'.'));
	$wp_attachment_post = array(
		'post_title'    =>      $image_name_save,
		'post_status'   =>      "inherit",
		'post_author'   =>      get_current_user_id(),
		'ping_status'   =>      "closed",
		'post_name'     =>      $image_name_save,
		'post_parent'   =>      $vendor_id ,
		'post_type'     =>      "attachment",
		'post_mime_type'=>      "image/".substr($image_name,(strripos($image_name,'.')+1)),
		'guid'          =>      wp_upload_dir()['url'].'/'.$image_name,
	);
	$image_id = wp_insert_attachment($wp_attachment_post,$upload_dir);
	
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$image_meta = wp_generate_attachment_metadata($image_id, $upload_dir);
	wp_update_attachment_metadata( $image_id,  $image_meta );
	
	//---- Vendor logo
	add_post_meta($vendor_id ,AFFILIATE_PROMOTIONS_PREFIX .'vendor_image',$image_id);
	
	//---- Vendor URL
	add_post_meta($vendor_id ,AFFILIATE_PROMOTIONS_PREFIX .'vendor_url',$data->url);
	
	//---- Vendor description
	add_post_meta($vendor_id ,AFFILIATE_PROMOTIONS_PREFIX .'vendor_description',$data->desc);
	
}

function insert_new_promotion($data){
	$client = new Client();
	
	$wp_post = array(
		'post_title'    =>      $data->title,
		'post_status'   =>      "publish",
		'post_author'   =>      get_current_user_id(),
		'post_content'  =>      $data->desc,
		'post_excerpt'  =>      $data->short_desc,
		'post_type'     =>      "affpromos_promotion",
	);
	$post_id = wp_insert_post($wp_post);
	
	//-----------Save image from url
	$image_url = $data->image;
	$image_name = substr($image_url,strripos($image_url,'/')+1);
	$upload_dir = wp_upload_dir()['path'].'/'.$image_name;
	if(! file_exists($upload_dir)) {
		$file_image = fopen($upload_dir, 'wb');
		$client->request('GET', $image_url, ['sink' => $file_image]);
		if(is_resource($file_image))
			fclose($file_image);
	}
	
	//-----------Save image post as attachment
	$image_name_save = substr($image_name,0,strripos($image_name,'.'));
	$wp_attachment_post = array(
		'post_title'    =>      $image_name_save,
		'post_status'   =>      "inherit",
		'post_author'   =>      get_current_user_id(),
		'ping_status'   =>      "closed",
		'post_name'     =>      $image_name_save,
		'post_parent'   =>      $post_id ,
		'post_type'     =>      "attachment",
		'post_mime_type'=>      "image/".substr($image_name,(strripos($image_name,'.')+1)),
		'guid'          =>      wp_upload_dir()['url'].'/'.$image_name,
	);
	$image_id = wp_insert_attachment($wp_attachment_post,$upload_dir);
	
	//------------Image meta data
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$image_meta = wp_generate_attachment_metadata($image_id, $upload_dir);
	wp_update_attachment_metadata( $image_id,  $image_meta );
	set_post_thumbnail($post_id,$image_id);
	
	//------------Insert new Vendors if not existed
	$vendor_name = $data->site_name;
	
	$vendor_id = insert_new_vendor($vendor_name);
	
	
	//---- Promotion category
	$cate = get_term_by('slug',$data->cate_guid,AFFILIATE_PROMOTIONS_PREFIX.'category');
	wp_set_object_terms($post_id, $cate->term_id,AFFILIATE_PROMOTIONS_PREFIX.'category');
	
	//---- Promotion type
	if( $data->coupons != null && $data->coupons != '' ){
		wp_set_object_terms($post_id, 'Coupon' ,AFFILIATE_PROMOTIONS_PREFIX.'promotion_type');
		add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_code', $data->coupons );
	}else{
		wp_set_object_terms($post_id, 'Promotion' ,AFFILIATE_PROMOTIONS_PREFIX.'promotion_type');
	}
	
	//---- Promotion URL
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_url',$data->url);
	
	//---- Vendor ID
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_vendor',$vendor_id);
	
	//---- Image ID
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_image',$image_id);
	
	//---- Promotion title
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_title',$data->title);
	
	//---- Valid time
	if(intval($data->expiration_timestamp) == 99999999999 )
		$data->expiration_timestamp = time() + 3600*24* 30 ;  // 30 days from now
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_valid_until', intval($data->expiration_timestamp));
	
	//---- Promotion descriptions
	add_post_meta($post_id,AFFILIATE_PROMOTIONS_PREFIX .'promotion_description',$data->short_desc);
	
}

function is_manual_update(){
	return isset($_GET['action']) && $_GET['action'] === 'update_promotions';
}

function do_admin_action()
{
	if (is_manual_update()) {
		$opt = affpromos_get_options();
		require_once AFFILIATE_PROMOTIONS_DIR . 'includes/apis/AccessTrade_Api.php';
		
		$client = new AccessTrade_Api($opt);
		$client->run_full_update();
		
//		if (wp_redirect( $_SERVER['REQUEST_URI'] ))
//			exit;
	}
}

function admin_noti_mess ($mess, $type='success')
{
	add_action( 'admin_notices', function() use ($mess,$type) {
		?>
        <div class="notice notice-<?php echo $type; ?> is-dismissible">
            <p><?php _e( $mess , 'Noti' ); ?></p>
        </div>
		<?php
	}
	);
}


function filter_post_by_vendor( $wp_query ) {
	if (is_admin()) {
		$post_type = $wp_query->query['post_type'];
		if ( $post_type == 'affpromos_promotion' && isset($_GET['vendor']) ) {
			$wp_query->set('meta_key', 'affpromos_promotion_vendor');
			$wp_query->set('meta_value',$_GET['vendor'] );
		}elseif ($post_type == 'affpromos_offer' && isset($_GET['vendor'])){
			$wp_query->set('meta_key', 'affpromos_offer_vendor');
			$wp_query->set('meta_value',$_GET['vendor'] );
		}
	}
}
add_filter('pre_get_posts', 'filter_post_by_vendor');

//function init_routine_check()
//{
//	if( !wp_next_scheduled( 'affpromos_cron_update' ) ) {
//		wp_schedule_event( time()+10, 'aff_auto_update_interval', 'affpromos_cron_update' );
//	}
//}
//
//function add_custom_cron_intervals( $schedules ) {
//	$schedules['aff_auto_update_interval'] = array(
//		'interval'	=> 3600*AFFILIATE_AUTO_UPDATE_HOURS_PER_UPDATE,
//		'display'	=> 'Once Every '.AFFILIATE_AUTO_UPDATE_HOURS_PER_UPDATE.' hours '
//	);
//	return (array)$schedules;
//}
//add_filter( 'cron_schedules', 'add_custom_cron_intervals', 0, 1 );

//function affpromos_run_auto_update()
//{
//	update_option(AFFILIATE_PROMOTIONS_PREFIX . 'last_routine_check', time());
//
//	$opt = affpromos_get_options();
//
//	$token = $opt['aff_token'] ;
//	$is_auto_update = $opt['auto_update_promotions'];
//
////    if( (strlen($token) != 32 ) || $is_auto_update != '1' )
////    {
////        return;
////    }
////    $chuck = 35;
//	init_promotion_type();
//
//	$vendor = update_vendor($token);
//	if (isset($vendor['error']))
//	{
//		// TODO : Handle error
//		return;
//	}
//	$chuck -= $vendor['item_count'];
//
//	$category = update_category($token);
//	if (isset($category['error']))
//	{
//		// TODO : Handle error
//		return;
//	}
//
//	$chuck -= $category['item_count'];
//
//	$promotion = update_promotions($chuck, $token);
//	if (isset($promotion['error']))
//	{
//		// TODO : Handle error
//		return;
//	}
//
//	if($chuck > 0){
//		$offer = update_offer($chuck, $token);
//		if (isset($offer['error']))
//		{
//			return;
//		}
//	}
//	$log_data = (object) array(
//		'sync_type'=>'summary_',
//		'status'=>'success',
//		'message'=> implode("-",array(
//			'Vendors :'     . _defaul($vendor['item_count'],0),
//			'Categories :'  . _defaul($category['item_count'],0),
//			'Promotions :'  . _defaul($promotion['item_count'],0),
//			'Offers :'      . _defaul($offer['item_count'],0),
//		)),
//		'amount'=> _defaul($vendor['item_count'],0)
//		           + _defaul($category['item_count'],0)
//		           + _defaul($promotion['item_count'],0)
//		           + _defaul($offer['item_count'],0),
//	);
//	affpromos_log_action($log_data);
//	// TODO : Complete update
//
//}
//add_action('affpromos_cron_update','affpromos_run_auto_update',0,0);
//init_routine_check();

function affpromos_log_action($data){
	global $wpdb;
	$sync_type  = isset($data->sync_type)   ? $data->sync_type :'';
	$sync_type  .= is_manual_update()       ? 'manual_update' : 'auto_update';
	$item       = isset($data->item)        ? $data->item : '';
	$message    = isset($data->message)     ? $data->message : '';
	$status     = isset($data->status)      ? $data->status : '';
	$amount     = isset($data->amount)      ? $data->amount : 0;
	
	$sql = 'INSERT INTO ' . AFFILIATE_ACTION_LOG_TABLE . ' (`sync_type`,`item`,`message`,`status`,`amount`) VALUES (%s,%s,%s,%s,%d)';
	$sql = $wpdb->prepare($sql, $sync_type, $item, $message, $status, $amount);
	$insert = $wpdb->query($sql);
	
	if (!$insert){
		$create_table = affpromos_create_table_item_log();
		if ($create_table!=false ) {
			$insert = $wpdb->query($sql);
			
		}
	}
}

function affpromos_create_table_item_log(){
	global $wpdb;
	
	$sql = 'CREATE TABLE `' . AFFILIATE_ACTION_LOG_TABLE . '` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                                `sync_type` VARCHAR(50) ,
                                                `item` VARCHAR(20),
                                                `message` VARCHAR(512) ,
                                                `status` VARCHAR(20) ,
                                                `amount`  INT UNSIGNED,
                                                `time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                                PRIMARY KEY (`id`))';
	$res = $wpdb->query($sql);
	return $res;
	
}

function affpromos_query_logs_from($days_ago){
	
	global $wpdb;
	
	$sql = 'SELECT * FROM '.AFFILIATE_ACTION_LOG_TABLE.' WHERE (sync_type LIKE "summary_%" and time BETWEEN NOW() - INTERVAL '.$days_ago.' DAY AND NOW()) ORDER BY time DESC';
	return $wpdb->get_results($sql);
}

