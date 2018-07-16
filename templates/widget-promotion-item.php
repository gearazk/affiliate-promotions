<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="offer_box offer_box_<?php echo $promotion_per_row ?> coupon-padding " >
    <div class="offer_box-item" >

        <div class="promotion-img-wrapper">
            <img class="lozad visible" src="<?php the_post_thumbnail_url() ?>" alt="<?php the_title() ?>" title="<?php the_title() ?>">
            <span class="imghelper"></span>
        </div>
        <div class="aff_vendor-logo">
			<?php affpromos_get_promotion_vendor_thumbnail() ?>
        </div>
        <div class="title">

            <h3 class="text-loaded">

                <a href="<?php echo affpromos_get_promotion_url() ?>" title="<?php the_title() ?>">
					<?php the_title() ?>
                </a>
            </h3>
        </div>
        <div class="clearfix info">
            <div class="icons icons-loaded">
				
				<?php if (affpromos_get_promotion_discount()){ ?>
                    <div class="icons_percent">
                        <i class="fa fa-scissors" aria-hidden="true"></i>
                        <p><?php echo affpromos_get_promotion_discount() ?></p>
                    </div>
				<?php }else{ ?>
                    <i class="fa fa-gift fa-2x" aria-hidden="true"></i>
				<?php } ?>

            </div>
            <ul class="list-loaded">
                <li>
                    <span class="time">
                        <i class="fa fa-clock-o"></i>
	                    <?php echo affpromos_the_promotion_valid_text() ?>
                    </span>
                </li>
            </ul>
        </div>
        <div class="offer_footer">
			<?php if (affpromos_get_promotion_code()){ ?>
                <a href="<?php echo affpromos_get_promotion_url() ?>">
                    <div class="affbtn button-code">
                        <div class="apla"></div>
                        <div class="btn-label">LẤY MÃ NGAY</div>
                        <div class="corner" style="background:url(<?php echo AFFILIATE_PROMOTIONS_URL?>public/assets/assets/button-corner_red.png) left top no-repeat"></div>
                        <span class="in"><?php echo affpromos_get_promotion_code() ?></span>
                    </div>
                </a>
			<?php }else{ ?>
                <a href="<?php echo affpromos_get_promotion_url() ?>">
                    <button class="affbtn promo-btn"> XEM KHUYẾN MÃI </button>
                </a>
			<?php } ?>
        </div>
    </div>
</div>
