<?php
/**
 * Plugin Name:     Affiliate Promotions
 * Plugin URI:      https://wordpress.org/plugins/affiliate-tools-promotions/
 * Description:     Promote promotions and deals of products and earn money with affiliate referrals.
 * Version:         0.1.6
 * Author:          gearazk
 * Author URI:      https://giaregiatot.vn
 * Text Domain:     affiliate-promotions
 *
 * @package         AffiliatePromotions
 * @author          Nix
 * @copyright       Copyright (c) leduchuy89vn
 *
 * Copyright (c) 2016 - flowdee ( https://twitter.com/leduchuy89vn )
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'Affpromos_Plugin' ) ) {

    /**
     * Main Affiliate_Promotions class
     *
     * @since       0.1
     */
    class Affpromos_Plugin {

        /**
         * @var         Affpromos_Plugin $instance The one true Affpromos_Plugin
         * @since       0.1
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       0.1
         * @return      object self::$instance The one true Affpromos_Plugin
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new Affpromos_Plugin();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       0.1
         * @return      void
         */
        private function setup_constants() {

            // Plugin name
            define( 'AFFILIATE_PROMOTIONS_NAME', 'Affiliate Promotions' );
            
            // Plugin domain plug
            define( 'AFFILIATE_PROMOTIONS_PLUG', 'aff-promotion' );

	        // Plugin affiliate AccessTrade prefix
            define( 'AFFILIATE_PROMOTIONS_AT_PREFIX', 'affpromos_accesstrade_' );

            // Plugin version
            define( 'AFFILIATE_PROMOTIONS_VER', '0.1.6' );

            // Plugin path
            define( 'AFFILIATE_PROMOTIONS_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'AFFILIATE_PROMOTIONS_URL', plugin_dir_url( __FILE__ ) );

            // Plugin prefix
            define( 'AFFILIATE_PROMOTIONS_PREFIX', 'affpromos_' );

            // Constant hours auto update (HOURS_PER_UPDATE)
            define( 'AFFILIATE_AUTO_UPDATE_HOURS_PER_UPDATE', 3 );

            // Publisher homepage
            define( 'AFFILIATE_PROMOTIONS_HOMEPAGE', 'https://khuyenmaimuasam.com/affiliate/' );
            
            // AccessTrade get key page
            define( 'AFFILIATE_PROMOTIONS_ACCESSTRADE_TOKEN_PAGE', 'https://pub.accesstrade.vn/accounts/profile' );
            
            // APIs
            define( 'AFFILIATE_PROMOTIONS_ACCESSTRADE_GET_VENDOR_API', 'https://api.accesstrade.vn/v1/campaigns' );
            define( 'AFFILIATE_PROMOTIONS_ACCESSTRADE_GET_PROMOTION_API', 'https://api.accesstrade.vn/v1/offers_informations' );
            define( 'AFFILIATE_PROMOTIONS_ACCESSTRADE_GET_OFFER_API', 'https://api.accesstrade.vn/v1/datafeeds' );

            // Logs table name
            define( 'AFFILIATE_ACTION_LOG_TABLE',AFFILIATE_PROMOTIONS_PREFIX.'action_log'  );

            // Cron for auto-update feature)
//            if (!defined('ALTERNATE_WP_CRON') || ALTERNATE_WP_CRON == false) {
//                define('ALTERNATE_WP_CRON', true);
//            }
            // Select2 libs in Shortcode UI
            define( 'SELECT2_NOCONFLICT', true );

        }

        /**
         * Include necessary files
         *
         * @access      private
         * @since       0.1.0
         * @return      void
         */
        private function includes() {

            // Basic
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/helper.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/scripts.php';

            // Dependencies
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/libs.php';

            // Admin only
            if ( is_admin() ) {
                require_once AFFILIATE_PROMOTIONS_DIR . 'includes/admin/plugins.php';
                require_once AFFILIATE_PROMOTIONS_DIR . 'includes/admin/class.settings.php';
            }

            // Promotions
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/promotions/post-type.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/promotions/type-taxonomy.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/promotions/category-taxonomy.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/promotions/manage-promotions.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/promotions/metaboxes.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/promotion-functions.php';

            // Vendors
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/vendors/post-type.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/vendors/manage-vendors.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/vendors/metaboxes.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/vendor-functions.php';

            // Offers
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/offers/post-type.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/offers/manage-offers.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/offers/metaboxes.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/offers/shortcodes.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/offer-functions.php';


            // Anything else
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/hooks.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/functions.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/shortcodes.php';
            
            // Widget
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/widgets/latest_promotion.php';
            require_once AFFILIATE_PROMOTIONS_DIR . 'includes/widgets/latest_offer.php';
	
            // Auto update
	        require_once AFFILIATE_PROMOTIONS_DIR . 'includes/admin/update.php';
	
	
	        // css scripts
            wp_register_style( AFFILIATE_PROMOTIONS_PREFIX.'custom_style',  plugin_dir_url( __FILE__ ) . 'public/assets/css/admin.min.css' );
            wp_enqueue_style( AFFILIATE_PROMOTIONS_PREFIX.'custom_style' );
//	        wp_enqueue_style( AFFILIATE_PROMOTIONS_PREFIX.'latest_promotions_widget', plugins_url('public/assets/css.css',dirname(__FILE__) ));


	        wp_enqueue_script( AFFILIATE_PROMOTIONS_PREFIX.'custom_scrips', plugin_dir_url( __FILE__ ) . 'public/assets/js/affpromos_custom.js', array(), null, true );
	
	
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       0.1.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = AFFILIATE_PROMOTIONS_DIR . '/languages/';
            $lang_dir = apply_filters( 'affiliate_promotions_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), AFFILIATE_PROMOTIONS_PLUG );
            $mofile = sprintf( '%1$s-%2$s.mo', AFFILIATE_PROMOTIONS_PLUG, $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/affiliate-promotions/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/affiliate-promotions/ folder
                load_textdomain( AFFILIATE_PROMOTIONS_PLUG, $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/affiliate-promotions/languages/ folder
                load_textdomain( AFFILIATE_PROMOTIONS_PLUG, $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( AFFILIATE_PROMOTIONS_PLUG, false, $lang_dir );
            }
        }
    }
} // End if class_exists check

class AffiliatePromotionWidget {

    /**
     * Hook to wp_dashboard_setup to add the widget.
     */

    public static function init() {
        wp_add_dashboard_widget(
            "id_promotion_dashboard_widget",
            __( 'Promotion Statistic', 'affiliate_promotion' ),
            array('AffiliatePromotionWidget', 'render_widget')
        );
    }

    public static function get_count_promotion_of_vendor()
    {
        global $wpdb;

        $sql_count = 'SELECT meta_value as vendor_id, count(post_id) as count FROM wp_postmeta where (meta_key="'.AFFILIATE_PROMOTIONS_PREFIX.'promotion_vendor") group by meta_value order by count desc';
        $sql = 'select post.guid as image_url, tab.count as count, tab.vendor_id as vendor_id from '.$wpdb->posts.' as post, ('.$sql_count.') as tab where (post.post_parent = tab.vendor_id and post.post_type="attachment") order by count desc limit 10;';

        return $wpdb->get_results($sql);
    }
    public static function get_count_offer_of_vendor($vendor_id){
        global $wpdb;
        $sql_count = 'SELECT count(post_id) as offers FROM wp_postmeta where (meta_key = "'.AFFILIATE_PROMOTIONS_PREFIX.'offer_vendor" and meta_value='.$vendor_id.');  ';
        return $wpdb->get_results($sql_count)[0]->offers;
    }

    public static function render_widget(){

        if(!wp_script_is('affproms-widget.css', 'enqueued')) {
            wp_enqueue_style( 'affproms-widget.css', plugins_url('public/assets/css/affproms-widget.css', __FILE__), array(), false);
        }
        $stripe = true;
        $data = AffiliatePromotionWidget::get_count_promotion_of_vendor();
        echo "<div class='stats-table'>
                <div class='stats-row header'>
                    <div class='column '>Vendor Name</div><div class='column '>Promotions</div><div class='column '>Offers</div>
                </div>";
        foreach ($data as $vendor)
        {

            ?>
            <div class='stats-row <?php if($stripe) echo 'stripe';?> '  >
                <div class='stats-cell vendor-logo'><img class='verdor-img' src=<?php echo $vendor->image_url ?> ></a></div>
                <div class='stats-cell' onclick="window.open('/wp-admin/edit.php?post_type=affpromos_promotion&vendor=<?php echo $vendor->vendor_id ?>' );"><div class='count-box'><?php echo $vendor->count ?> </div></div>
                <div class='stats-cell' onclick="window.open('/wp-admin/edit.php?post_type=affpromos_offer&vendor=<?php echo $vendor->vendor_id ?>' );"><div class='count-box count-offer'><?php echo AffiliatePromotionWidget::get_count_offer_of_vendor($vendor->vendor_id) ?> </div></div>

            </div>
            <?php ;
            $stripe = !$stripe;
        }
        if (count($data)==0){
            ?>
            <div class='stats-row'  >
                <p> No records ! Please update in the <b>Settings</b> section or <b>Add</b> some items </p>
            </div>

            <?php
        }
        echo "</div>";
    }
}

add_action( 'wp_dashboard_setup', array('AffiliatePromotionWidget','init') );



/**
 * The main function responsible for returning the one true Affpromos_Plugin
 * instance to functions everywhere
 *
 * @since       0.1
 * @return      \Affpromos_Plugin The one true Affpromos_Plugin
 *
 */
function affpromos_load() {
    return Affpromos_Plugin::instance();
}
add_action( 'plugins_loaded', 'affpromos_load' );

/**
 * The activation hook
 * Will ask if the user want to use the Shortcode UI feature.
 * Download it from Wordpress and activate if user confirm
 *
 * @since       0.1
 *
 */

function affpromos_activation() {

    $_SHORTCODE_UI_URL = 'https://downloads.wordpress.org/plugin/shortcode-ui.0.7.1.zip';

    $_SHORTCODE_UI_DIR = plugin_dir_path( __DIR__ ).'shortcode-ui.zip';

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if (!file_exists(plugin_dir_path( __DIR__ ).'shortcode-ui/shortcode-ui.php'))
    {
        if ( !isset($_GET['shortcode'])  )
            wp_die('
            <div class="wp-core-ui">
                <p>We are highly recommand using <a href="https://vi.wordpress.org/plugins/shortcode-ui/"><strong>Shortcode-UI</strong></a> plugin for better visualize shortcode in WP editor.</p>
                <a href="'.$_SERVER['REQUEST_URI'].'&shortcode=1'.'" type="button" class="button button-primary" style="background: #008ec2;
        border-color: #006799;
        color:#fff">GET & ACTIVE
                </a>
                <a href="'.$_SERVER['REQUEST_URI'].'&shortcode=0'.'" type="button" class="button button-primary">NO
                </a>
            </div>
            ');
        if ( $_GET['shortcode']=='1'){
            include_once plugin_dir_path(__FILE__). 'includes/libs/vendor/autoload.php';
            include_once(ABSPATH .'/wp-admin/includes/file.php');

            $client = new GuzzleHttp\Client();

            $response = $client->request('GET', $_SHORTCODE_UI_URL, ['decode_content' => 'zip','sink' => $_SHORTCODE_UI_DIR]);

            if($response->getStatusCode()==200){
                chmod($_SHORTCODE_UI_DIR,0777);
                WP_Filesystem();
                $unzip = unzip_file($_SHORTCODE_UI_DIR, plugin_dir_path(__DIR__) );
                wp_delete_file($_SHORTCODE_UI_DIR);

                if($unzip != true){
                    wp_die('Some errors when extract zip file. Consider change your FTP configures <a href="/wp-admin/plugins.php">Return</a>');
                }
            }else{
                wp_die('Connection error !! <a href="/wp-admin/plugins.php">Please try again</a>');
            }
        }
    }
    
    if(!is_plugin_active('shortcode-ui/shortcode-ui.php') && file_exists(plugin_dir_path( __DIR__ ).'shortcode-ui/shortcode-ui.php') )
    {
        if ( !isset($_GET['shortcode-active'])  )
            wp_die('
            <div class="wp-core-ui">
                <p>We see you have the Shortcode UI plugin but not active. Do you want to activate it ?</p>

                <a href="'.$_SERVER['REQUEST_URI'].'&shortcode-active=1'.'" type="button" class="button button-primary" style="background: #008ec2;
        border-color: #006799;
        color:#fff">OK
                </a>
                <a href="'.$_SERVER['REQUEST_URI'].'&shortcode-active=0'.'" type="button" class="button button-primary">NO
                </a>
            </div>');
        if ( $_GET['shortcode-active']=='1')
            if (!_active_plugin('shortcode-ui/shortcode-ui.php')){
                wp_die('Error when activate Shortcode UI plugin ! you can activate it manually  <a href="/wp-admin/plugins.php">Return</a>');
            }

    }



}




/* *
 * Active plugin by directory (use for Shortcode UI activate if didn't )
 *
 * @param string $installer| plugin path from plugins directory.
 *
 * @since       0.1
 * @return      bool| Activation success or not
 * */

function _active_plugin($installer)
{
    $current = get_option('active_plugins');
    $plugin = plugin_basename(trim($installer));

    if(!in_array($plugin, $current))
    {
            $current[] = $plugin;
            sort($current);
            do_action('activate_plugin', trim($plugin));
            update_option('active_plugins', $current);
            do_action('activate_'.trim($plugin));
            do_action('activated_plugin', trim($plugin));
            return true;
    }
    return false;
}

register_activation_hook( __FILE__, 'affpromos_activation' );

/**
 * The deactivation hook
 */
function affpromos_deactivation() {
    // Cleanup your tables, transients etc. here
}
register_deactivation_hook(__FILE__, 'affpromos_deactivation');