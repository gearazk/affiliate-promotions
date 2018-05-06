<?php
if (!defined('ABSPATH')) exit;

if (!class_exists('SyncAjax')){
	class SyncAjax {
		
		public static $AJAX_SYNC_OFFER_TRIGGER_ID       = AFFILIATE_PROMOTIONS_PREFIX.'sync_offer_trigger';
		public static $AJAX_SYNC_PROMOTION_TRIGGER_ID   = AFFILIATE_PROMOTIONS_PREFIX.'sync_promotion_trigger';
		public static $AJAX_SYNC_FULL_TRIGGER_ID        = AFFILIATE_PROMOTIONS_PREFIX.'sync_full_trigger';
		
		public static function ajax_sync_trigger_render($trigger_id,$btn_trigger_text,$js_get_settings_function=null){
			$ajax_action = $trigger_id.'_ajax_action';
			?>
            <button type="button" id="<?php echo $trigger_id ?>" class="button-secondary">
                <b><?php echo $btn_trigger_text; ?></b>
            </button>
            <script>
                jQuery(document).ready( function($) {
                    $('#<?php echo $trigger_id ?>').on('click',function (e) {
                        _this = $('#<?php echo $trigger_id ?>');
                        if(_this.data('loading') === '1')return;
                        _this.data('loading','1');
                        old_html = _this.html();
                        _this.html('Updating...');
                        var data = {
                            action: '<?php echo $ajax_action ?>',
							<?php
							if ($js_get_settings_function){
							?>
                            settings: <?php echo $js_get_settings_function ?>()
							<?php
							}
							?>
                        };
                        $.post(ajaxurl, data, function(response) {
                            _this.data('loading','0');
                            _this.html(old_html);
                            result = JSON.parse(response);
                            type = result.type || 'warning';
                            message = result.message || 'Not responding';
                            notice = $('<div class="notice notice-'+type+' is-dismissible"><p><strong>'+message+'</strong></p></div>');
                            _this.after(notice);
                            setTimeout(function(){
                                notice.slideUp(300,function(){notice.remove()})
                            },2000)
                        })
                    });
                });
            </script>
			<?php
		}
		
		public static function ajax_sync_offer_trigger_render() {
			$trigger_id = SyncAjax::$AJAX_SYNC_OFFER_TRIGGER_ID;
			$btn_trigger_text = __('Sync offer',AFFILIATE_PROMOTIONS_PLUG);
			$js_get_settings_function = 'sync_offer_settings_getter';
			?>
            <script>
                function <?php echo $js_get_settings_function ?>() {
                    const $ = jQuery;
                    return {
                        'offer_limit':$('#<?php echo AFFILIATE_PROMOTIONS_PREFIX.'offer_limit' ?>').val(),
                        'offer_vendor':$('#<?php echo AFFILIATE_PROMOTIONS_PREFIX.'offer_vendor' ?>').val(),
                        'offer_category':$('#<?php echo AFFILIATE_PROMOTIONS_PREFIX.'offer_category' ?>').val()
                    }
                }
            </script>
			<?php
			SyncAjax::ajax_sync_trigger_render($trigger_id,$btn_trigger_text,$js_get_settings_function);
		}
		
		public static function ajax_sync_promotion_trigger_render() {
			$trigger_id = SyncAjax::$AJAX_SYNC_PROMOTION_TRIGGER_ID;
			$btn_trigger_text = __('Sync promotions',AFFILIATE_PROMOTIONS_PLUG);
			
			SyncAjax::ajax_sync_trigger_render($trigger_id,$btn_trigger_text);
		}
		
		public static function ajax_sync_full_trigger_render() {
			$trigger_id = SyncAjax::$AJAX_SYNC_FULL_TRIGGER_ID;
			$btn_trigger_text = __('Sync everything',AFFILIATE_PROMOTIONS_PLUG);
			
			SyncAjax::ajax_sync_trigger_render($trigger_id,$btn_trigger_text);
		}
		
		
		public static function sync_offer_ajax_handle() {
			$ajax_action = SyncAjax::$AJAX_SYNC_OFFER_TRIGGER_ID.'_ajax_action';
			add_action('wp_ajax_'.$ajax_action, function (){
				do {
					if (!$_POST['settings']) break;
					
					$offer_limit    = $_POST['settings']['offer_limit'];
					if (!$offer_limit) break;
					
					$offer_vendor   = $_POST['settings']['offer_vendor'];
					if (!$offer_vendor) break;
					
					$offer_category = $_POST['settings']['offer_category'];
					if (!$offer_category) break;
					
					require_once AFFILIATE_PROMOTIONS_DIR . 'includes/apis/AccessTrade_Api.php';
					$client = new AccessTrade_Api(affpromos_get_options());
					$client->sync_offers_with_local();
					echo json_encode(array(
						'type'      =>'success',
						'message'   =>__('Updated',AFFILIATE_PROMOTIONS_PLUG),
					));
					die();
					
				}while(false);
				
				echo json_encode(array(
					'type'      =>'error',
					'message'   =>__('Some data is invalid',AFFILIATE_PROMOTIONS_PLUG),
				));
				die();
			});
		}
		
		public static function sync_promotion_ajax_handle() {
			$ajax_action = SyncAjax::$AJAX_SYNC_PROMOTION_TRIGGER_ID.'_ajax_action';
			add_action('wp_ajax_'.$ajax_action, function (){
				require_once AFFILIATE_PROMOTIONS_DIR . 'includes/apis/AccessTrade_Api.php';
				$client = new AccessTrade_Api(affpromos_get_options());
				$client->sync_promotions_with_local();
				
				echo json_encode(array(
					'type'      =>'success',
					'message'   =>__('Updated',AFFILIATE_PROMOTIONS_PLUG),
				));
				die();
			});
		}
		
		public static function sync_full_ajax_handle() {
			$ajax_action = SyncAjax::$AJAX_SYNC_FULL_TRIGGER_ID.'_ajax_action';
			add_action('wp_ajax_'.$ajax_action, function (){
				require_once AFFILIATE_PROMOTIONS_DIR . 'includes/apis/AccessTrade_Api.php';
				$client = new AccessTrade_Api(affpromos_get_options());
				$client->run_full_update();
				
				echo json_encode(array(
					'type'      =>'success',
					'message'   =>__('Updated',AFFILIATE_PROMOTIONS_PLUG),
				));
				die();
			});
		}
		
	}
	
	SyncAjax::sync_offer_ajax_handle();
	SyncAjax::sync_promotion_ajax_handle();
	SyncAjax::sync_full_ajax_handle();
}
