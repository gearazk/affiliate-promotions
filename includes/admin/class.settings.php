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

require_once AFFILIATE_PROMOTIONS_DIR . 'includes/admin/ajax-functions.php';

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
//			add_settings_section(
//				'affpromos_settings_section_quickstart',
//				__('Quickstart Guide', AFFILIATE_PROMOTIONS_PLUG),
//				array( &$this, 'section_quickstart_render' ),
//				'affpromos_settings'
//			);
			
			
			// SECTION TWO
			add_settings_section(
				'affpromos_settings_section_promotions',
				__('Sync data settings', AFFILIATE_PROMOTIONS_PLUG),
				null,
				'affpromos_settings'
			);
			
                add_settings_field(
                    'affpromos_settings_promotion_aff_token',
                    __('AccessTrade Access Token', AFFILIATE_PROMOTIONS_PLUG),
                    array(&$this, 'promotion_aff_token_at_render'),
                    'affpromos_settings',
                    'affpromos_settings_section_promotions'
                );
                
//                add_settings_field(
//                    'affpromos_settings_promotion_auto_update',
//                    __('Auto Update', AFFILIATE_PROMOTIONS_PLUG),
//                    array(&$this, 'promotion_auto_update_render'),
//                    'affpromos_settings',
//                    'affpromos_settings_section_promotions'
//                );
                
                add_settings_field(
                    'affpromos_settings_promotion_lifetime',
                    __('Expiration', AFFILIATE_PROMOTIONS_PLUG),
                    array(&$this, 'promotion_lifetime_render'),
                    'affpromos_settings',
                    'affpromos_settings_section_promotions'
                );
//                add_settings_field(
//                    'affpromos_settings_aff_omit_offer_update',
//                    __('Not update Offers', AFFILIATE_PROMOTIONS_PLUG),
//                    array(&$this, 'promotion_aff_omit_offer_update_render'),
//                    'affpromos_settings',
//                    'affpromos_settings_section_promotions'
//                );
                add_settings_field(
                    'affpromos_settings_promotion_auto_update',
                    __('Sync everything', AFFILIATE_PROMOTIONS_PLUG),
                    array(&$this, 'sync_full_fields_render'),
                    'affpromos_settings',
                    'affpromos_settings_section_promotions'
                );
                add_settings_field(
                    AFFILIATE_PROMOTIONS_PREFIX.'sync_promotion_fields',
                    __('Sync Promotions', AFFILIATE_PROMOTIONS_PLUG),
                    array(&$this, 'sync_promotion_fields_render'),
                    'affpromos_settings',
                    'affpromos_settings_section_promotions'
                );
                
			
			add_settings_section(
				'affpromos_offer_sync_setting',
				__('Sync Offers', AFFILIATE_PROMOTIONS_PLUG),
				null,
				'affpromos_settings'
			);
                add_settings_field(
                    AFFILIATE_PROMOTIONS_PREFIX.'offer_limit_field',
                    __('Offer Limit (<30)', AFFILIATE_PROMOTIONS_PLUG),
                    array(&$this, 'offer_limit_field_renderer'),
                    'affpromos_settings',
                    'affpromos_offer_sync_setting'
                );
                add_settings_field(
                    AFFILIATE_PROMOTIONS_PREFIX.'offer_vendor_field',
                    __('Vendor', AFFILIATE_PROMOTIONS_PLUG),
                    array(&$this, 'offer_vendor_field_renderer'),
                    'affpromos_settings',
                    'affpromos_offer_sync_setting'
                );
                add_settings_field(
                    AFFILIATE_PROMOTIONS_PREFIX.'offer_cate_field',
                    __('Category', AFFILIATE_PROMOTIONS_PLUG),
                    array(&$this, 'offer_category_field_renderer'),
                    'affpromos_settings',
                    'affpromos_offer_sync_setting'
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
		
		function offer_limit_field_renderer() {
			$offer_limit = isset ( $this->options[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_limit'] ) ? $this->options[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_limit'] : 30;
		    ?>
            <input type="number" id="<?php echo AFFILIATE_PROMOTIONS_PREFIX.'offer_limit';?>" name="affpromos_settings[<?php echo AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_limit';?>]" value="<?php echo $offer_limit; ?>" />
            <?php
			SyncAjax::ajax_sync_offer_trigger_render();
		}
		
		function offer_vendor_field_renderer() {
		    $vendor_list = get_posts(['post_type'       =>AFFILIATE_PROMOTIONS_PREFIX.'vendor',
                                      'posts_per_page'  =>-1,
                                      'order'           =>'ASC',
                                      'orderby'         =>'post_title',
            ]);
			
			$selected = ( isset ( $this->options[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_vendor'] ) ) ? $this->options[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_vendor']
                : '';
			?>
            <select id="<?php echo AFFILIATE_PROMOTIONS_PREFIX.'offer_vendor';?>" name="affpromos_settings[<?php echo AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_vendor';?>]">
                <option value="" <?php selected( $selected, '' ); ?>>(<?php _e('All vendors',AFFILIATE_PROMOTIONS_PLUG) ?>)</option>
                <?php foreach ( $vendor_list as $vendor) { ?>
                    <option value="<?php echo $vendor->post_name; ?>" <?php selected( $selected, $vendor->post_name ); ?>><?php echo $vendor->post_name; ?></option>
				<?php } ?>
            </select>
			<?php
		}
		
		function offer_category_field_renderer() {
			$cate_list = get_terms(['taxonomy'=>AFFILIATE_PROMOTIONS_PREFIX.'category','posts_per_page'=>-1,'order'=>'ASC','orderby'=>'post_title',]);
			
			$selected = ( isset ( $this->options[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_category'] ) ) ? $this->options[AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_category']
				: '';
			?>
            <select id="<?php echo AFFILIATE_PROMOTIONS_PREFIX.'offer_category';?>" name="affpromos_settings[<?php echo AFFILIATE_PROMOTIONS_AT_PREFIX.'offer_category';?>]">
                <option value="" <?php selected( $selected, '' ); ?>>(<?php _e('All categories',AFFILIATE_PROMOTIONS_PLUG) ?>)</option>
				<?php foreach ( $cate_list as $cate) { ?>
                    <option value="<?php echo $cate->slug; ?>" <?php selected( $selected, $cate->slug ); ?>><?php echo $cate->name; ?></option>
				<?php } ?>
            </select>
			<?php
		}
		
		function sync_promotion_fields_render() {
			SyncAjax::ajax_sync_promotion_trigger_render();
		}
		
		function sync_full_fields_render() {
			SyncAjax::ajax_sync_full_trigger_render();
			$last_auto = get_option(AFFILIATE_PROMOTIONS_PREFIX.'last_full_update_timestamp') ;
            if ($last_auto)
	            $last_auto = date("d F Y H:i:s", $last_auto);
            else
	            $last_auto = 'Not yet';
            ?>
            <p>
                - <?php _e('Sync vendors, promotions, offers in that order',AFFILIATE_PROMOTIONS_PLUG)?><br>
                - <?php _e('All settings saved in each section will be used',AFFILIATE_PROMOTIONS_PLUG)?><br>
                * <?php _e('Should only be used when you have time to waste or just lazy or whenever you want',AFFILIATE_PROMOTIONS_PLUG)?>
                <br>
                <br>
                * Last auto update: <?php echo $last_auto ?>
            </p>
            <?php
		}
		
		function promotion_aff_token_at_render() {
			
			$aff_at_token = isset ( $this->options[AFFILIATE_PROMOTIONS_AT_PREFIX.'token'] ) ? $this->options[AFFILIATE_PROMOTIONS_AT_PREFIX.'token'] : 'XXX';
			?>

            <input type="text" id="aff_token_input" name="affpromos_settings[<?php echo AFFILIATE_PROMOTIONS_AT_PREFIX.'token';?>]" value="<?php echo $aff_at_token; ?>" />
            <label for="affpromos_settings_promotion_aff_token"><?php _e('Get your',AFFILIATE_PROMOTIONS_PLUG) ?> <a href="<?php echo AFFILIATE_PROMOTIONS_ACCESSTRADE_TOKEN_PAGE; ?>"><?php _e('Here',AFFILIATE_PROMOTIONS_PLUG) ?></a></label>
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
				<?php screen_icon();
				$_get = $_GET;
				
				?>
                <h2><?php _e('Affiliate Promotions', AFFILIATE_PROMOTIONS_PLUG); ?>
					<?php if( isset($_get['aff_page']) && $_get['aff_page'] == 'logs' ) {
						unset($_get['action']);
						unset($_get['aff_page']);
						?>
                        <a href="<?php echo ("?" . http_build_query($_get)); ?>" class="button-primary"><?php _e('View Settings', AFFILIATE_PROMOTIONS_PLUG); ?></a>
					
					<?php } else {
						unset($_get['action']);
						$_get['aff_page'] = 'logs';
						?>
                        <a href="<?php echo ("?" . http_build_query($_get)); ?>" class="button-primary"><?php _e('View Logs', AFFILIATE_PROMOTIONS_PLUG); ?></a>
					<?php } ?>
                </h2>

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <div class="meta-box-sortables ui-sortable">
                                <form action="options.php" method="post">
									<?php
									settings_fields('affpromos_settings');
									if(isset($_GET['aff_page']) && $_GET['aff_page'] == 'logs') {
										affpromos_logs_sections();
									}else{
										affpromos_do_settings_sections('affpromos_settings');
										?>
                                        <p><?php submit_button('Save Changes', 'button-primary', 'submit', false); ?></p>
										<?php
									}?>
                                </form>
                            </div>
                        </div>
                        <!-- /#post-body-content -->
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
			$title = "<h3 class='hndle'>{$section['title']}</h3>\n";
		
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

function is_manual_update(){
	return isset($_GET['action']) && $_GET['action'] === 'update_promotions';
}

function do_admin_action()
{
	if (is_manual_update()) {
		
		require_once AFFILIATE_PROMOTIONS_DIR . 'includes/apis/AccessTrade_Api.php';
		$client = new AccessTrade_Api();
		$client->run_full_update();
	}
}

function admin_noti_mess ($mess, $type='success')
{
	add_action( 'admin_notices', function() use ($mess,$type) {
		?>
        <div class="notice notice-<?php echo $type; ?> is-dismissible">
            <p><?php echo $mess ?></p>
        </div>
		<?php
	}
	);
}


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

