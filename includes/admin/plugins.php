<?php
/**
 * Settings
 *
 * @package     AffiliatePromotions\Admin\Plugins
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugins row action links
 *
 * @param array $links already defined action links
 * @param string $file plugin file path and name being processed
 * @return array $links
 */
function affpromos_action_links( $links, $file ) {

    $settings_link = '<a href="' . admin_url( 'edit.php?post_type=affpromos_promotion&page=affpromos_settings' ) . '">' . esc_html__( 'Settings', AFFILIATE_PROMOTIONS_PLUG ) . '</a>';

    if ( $file == 'affiliate-promotions/affiliate-promotions.php' )
        array_unshift( $links, $settings_link );

    return $links;
}
add_filter( 'plugin_action_links', 'affpromos_action_links', 10, 2 );

/**
 * Plugin row meta links
 *
 * @param array $input already defined meta links
 * @param string $file plugin file path and name being processed
 * @return array $input
 */
function affpromos_row_meta( $input, $file ) {

    if ( $file != 'affiliate-promotions/affiliate-promotions.php' )
        return $input;

    $custom_link = esc_url( add_query_arg( array(
            'utm_source'   => 'plugins-page',
            'utm_medium'   => 'plugin-row',
            'utm_campaign' => 'Affiliate Promotions',
        ), 'https://wordpress.org/plugins/affiliate-promotions/' )
    );

    $links = array(
        '<a href="' . $custom_link . '">' . esc_html__( 'Example Link', AFFILIATE_PROMOTIONS_PLUG ) . '</a>',
    );

    $input = array_merge( $input, $links );

    return $input;
}
//add_filter( 'plugin_row_meta', 'affpromos_row_meta', 10, 2 );