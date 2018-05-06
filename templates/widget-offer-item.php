<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<li class="affoffer type-offer status-publish has-post-thumbnail ">
	<div class="affoffer-thumb-wrap">
        <?php if (affpromos_get_offer_discount_percent()) {?>
		<span class="affonsale-wrapper">
			<span class="affonsale"></span>
			<span class="affonsale-text"><?php echo affpromos_get_offer_discount_percent() ?></span>
		</span>
        <?php } ?>
		<img width="300" height="300" src="<?php the_post_thumbnail_url() ?>" class="attachment-affpromos_latest_offer_widget_thumbnail size-affpromos_latest_offer_widget_thumbnail wp-post-image" alt="" />
		<div class="add-to-cart-wrap">
			<a href="<?php echo affpromos_get_offer_url() ?>" class="button product_type_simple add_to_cart_button ajax_add_to_cart" rel="nofollow">
				<?php _e('GET IT NOW',AFFILIATE_PROMOTIONS_PLUG) ?>
			</a>
		</div>
	</div>
	<div class="affoffer-info-wrap">
		<a href="<?php echo affpromos_get_offer_url() ?>" class="affpromos_latest_offer_widget-LoopProduct-link affpromos_latest_offer_widget-loop-product__link">
			<h2 class="affpromos_latest_offer_widget-loop-product__title"><?php the_title() ?></h2>
		</a>
		<span class="price">
			<del>
				<span class="affpromos_latest_offer_widget-Price-amount amount">
					<?php echo affpromos_get_offer_price_vnd(); ?>
				</span>
			</del>
			<ins>
				<span class="affpromos_latest_offer_widget-Price-amount amount">
                    <?php echo affpromos_get_offer_price_sale_vnd(); ?>
				</span>
			</ins>
		
		</span>
	</div>
</li>
