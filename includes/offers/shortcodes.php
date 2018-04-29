<?php
/*
 * Post lists
 */
function affpromos_add_offer_shortcode( $atts, $content ) {
    
    if ( empty( $atts ) ) {
        return;
    }
    
    extract( shortcode_atts( array(
        'category' => null,
        'search' => null,
        'type' => null,
        'vendor'=> null,
        'max' => -1,
        'orderby' => null,
        'grid' => null,
        'hide_expired' => null,
    ), $atts ) );

    // Prepare options
    $options = affpromos_get_options();

    // Default Query Arguments
    $args = array(
        'post_type' => 'affpromos_offer',
        'orderby' => 'modified',
        'order' => 'DESC',
        'posts_per_page' => $max,
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
                'key' => AFFILIATE_PROMOTIONS_PREFIX . 'offer_valid_until',
                'value'   => '',
                'compare' => 'NOT EXISTS',
                'type' => 'NUMERIC'
            ),
            // Already expired
            array(
                'key' => AFFILIATE_PROMOTIONS_PREFIX . 'offer_valid_until',
                'value' => time(),
                'compare' => '>=',
                'type' => 'NUMERIC'
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
        if ($category[0] != 'null') {
            $tax_queries[] = array(
                'taxonomy' => 'affpromos_category',
                'field' => (is_numeric($category[0])) ? 'term_taxonomy_id' : 'slug',
                'terms' => ($category), // array( $category )
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

    // Vendors
    if ( ! empty ( $vendor ) && $vendor !='null' ) {
        $vendor = explode(',',$vendor);
        $args['meta_query'] = array(
            'relation' => 'IN',
            array(
                'key' => AFFILIATE_PROMOTIONS_PREFIX.'offer_vendor',
                'value' => $vendor,
            ),
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

    // Grid
    $grid = ( ! empty ( $grid ) && is_numeric( $grid ) ) ? intval( $grid ) : 2;
    $grid = ( $grid > 4 || $grid < 1) ? 2 : $grid;



    // The Query
    $posts = new WP_Query( $args );

    ob_start();

    if ( $posts->have_posts() ) {

        // Get template file
        $file = affpromos_get_template_file( 'offers-grid', 'promotions' );

        echo '<div class="affpromos-offer">';

        if (  $file ) {
            include( $file );
        } else {
            _e('Template not found.', AFFILIATE_PROMOTIONS_PLUG);
        }

        echo '</div>';

        ?>
        <?php
    } else {
        echo '<p>' . __('No Offer found.', AFFILIATE_PROMOTIONS_PLUG) . '</p>';
    }

    $str = ob_get_clean();

    // Restore original Post Data
    wp_reset_postdata();

    return $str;
}
add_shortcode('aff-offers', 'affpromos_add_offer_shortcode');