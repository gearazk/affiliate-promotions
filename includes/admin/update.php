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
		'interval'	=> 3600*AFFILIATE_AUTO_UPDATE_HOURS_PER_UPDATE,
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
		
		wp_schedule_event( time()+10, AFFILIATE_PROMOTIONS_PREFIX.'auto_update_interval', $schedule_name );
	}
}

function schedule_full_update() {
	
	update_option(AFFILIATE_PROMOTIONS_PREFIX.'last_full_update_timestamp',time());
	
	require_once AFFILIATE_PROMOTIONS_DIR . 'includes/apis/AccessTrade_Api.php';
	(new AccessTrade_Api())->run_full_update();
}


function add_task_to_schedule($task,$schedule_name) {
	add_action($task,$schedule_name,0,0);
}


$CRON_UPDATE_ALL_NAME = AFFILIATE_PROMOTIONS_PREFIX.'cron_update_all';
add_task_to_schedule('schedule_full_update',$CRON_UPDATE_ALL_NAME);
init_routine_check($CRON_UPDATE_ALL_NAME);

