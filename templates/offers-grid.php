<?php
/*
 * Promotions template
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! isset ( $posts ) )
    return;

// Default values

if ( ! isset ( $grid ) ) {
    $col = 6;
} else {
    $col = intval(12/$grid);
}

if(!wp_script_is('offer-grid.css', 'enqueued')){
    wp_enqueue_style( 'boostrap-grid.css', plugins_url('public/assets/css/boostrap-grid.css', dirname(__FILE__)),array() );
    wp_enqueue_style( 'offer-grid.css', plugins_url('public/assets/css/offer-grid.css', dirname(__FILE__)),array() );
}

?>
<div class="container page-list page-category-level-2">
    <div class="main-section">
        <div class="wrap-list-item">
            <div class="row body-list-item">
                <?php while ( $posts->have_posts() ) { ?>
                <?php $posts->the_post();
                    $title = affpromos_get_offer_title();
                    $url = affpromos_get_offer_url();
                    $discount = affpromos_get_offer_discount_percent();
                    ?>
                <div class="col-md-<?php echo $col;?> col-xs-<?php echo $col;?>">
                    <div class="item" >
                        <div class="post-thumb">
                            <span class="mask">
                                <a href="<?php echo $url; ?>" title="<?php echo $title; ?>">
                                    <img class="lazy imglist"  alt="<?php echo $title; ?>" src="<?php the_post_thumbnail_url(); ?>" style="display: inline-block;">
                                </a>
                            </span>
                            <div class="tags new-item"></div>
                        </div>
                        <div class="item-content">
                            <div class="title-info">
                                <h4 class="post-title"><a href="<?php echo $url; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a></h4>
                                <div class="item-price">
                                    <span class="amount-1"><?php echo affpromos_get_offer_price_sale_vnd(); ?></span>
                                    <span class="amount-2"><?php echo affpromos_get_offer_price_vnd(); ?></span>
                                    <?php if ($discount != false) {?>
                                    <span class="sale-off"><?php echo $discount; ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="adr-coupon" >
                            </div>
                            <div class="item-rating" >
                                <div class="rating-box">
                                    <div style="width:0%" class="rating"></div>
                                </div>
                                <span id="count-rate-user-680540" class="count-user-rate"></span>
                            </div>
                            <div class="item_banner"></div>
                        </div>
                    </div>
                </div>
                <?php }?>


            </div>
        </div>
    </div>
</div>
