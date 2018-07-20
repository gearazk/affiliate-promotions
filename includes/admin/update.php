<?php
if (!defined('ABSPATH')) exit;

/**
 * Update, Sync data of Promotions, Offer, etc
 * NOT the update the plugin
 */

function add_custom_cron_intervals( $schedules ) {
	/*
	 * Custom cron interval
	 * */
	$schedules[AFFILIATE_PROMOTIONS_PREFIX.'auto_update_interval'] = array(
		'interval'	=> AFFILIATE_AUTO_UPDATE_HOURS_PER_UPDATE * 3600,
		'display'	=> 'Once Every '.AFFILIATE_AUTO_UPDATE_HOURS_PER_UPDATE.' hours '
	);
	return (array)$schedules;
}
add_filter( 'cron_schedules', 'add_custom_cron_intervals', 0, 1 );


function init_routine_check($schedule_name){
	
	/**
	 * If there isn't one schedule for update. Make one
	 */
	
	if( !wp_next_scheduled( $schedule_name ) ) {
		wp_schedule_event( time(), AFFILIATE_PROMOTIONS_PREFIX.'auto_update_interval', $schedule_name );
	}
}

function schedule_full_update() {
	# Just flip the switch to  true
	# SyncAjax::ajax_sync_background_render() will do the rest
	update_option(AFFILIATE_AUTO_UPDATE_SWITCH, true);
}

function add_task_to_schedule($task,$schedule_name) {
	add_action($task,$schedule_name);
}

$CRON_UPDATE_ALL_NAME = AFFILIATE_PROMOTIONS_PREFIX.'cron_update_all';
add_action( $CRON_UPDATE_ALL_NAME, 'schedule_full_update' );
init_routine_check($CRON_UPDATE_ALL_NAME);

if (!class_exists('SyncAjax')) {
	require_once AFFILIATE_PROMOTIONS_DIR . 'includes/admin/ajax-functions.php';
}
SyncAjax::ajax_sync_background_render();
