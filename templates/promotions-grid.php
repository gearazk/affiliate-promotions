<?php
/*
 * Promotions template
 */

if ( ! isset ( $posts ) )
    return;

// Default values
if ( ! isset ( $grid ) )
    $grid = '2';
    
if(!wp_script_is('promotion-grid.css', 'enqueued')){
    wp_enqueue_style( 'promotion-grid.css', plugins_url('public/assets/css/styles.min.css', dirname(__FILE__)),array() );
    wp_enqueue_script( 'affpromos-script', plugins_url( '/public/assets/js/scripts.min.js', dirname(__FILE__) ), array(), '0.1', true );
}

?>

<div class="affpromos-promotions-grid affpromos-promotions-grid--col-<?php echo $grid; ?>">
    <?php while ( $posts->have_posts() ) { ?>
        <?php
            $posts->the_post();
            $promo_url = affpromos_get_promotion_url();
        ?>
        <div class="affpromos-promotions-grid__item">

            <div class="<?php affpromos_the_promotion_classes('affpromos-promotion'); ?>">
                <div class="affpromos-promotion__header">
                    <div class="affpromos-promotion__thumbnail">
                        <a href="<?php echo $promo_url; ?>" >
                            <?php affpromos_the_promotion_thumbnail(); ?>
                        </a>
                    </div>

                    <?php if ( affpromos_get_promotion_discount() ) { ?>
                        <span class="affpromos-promotion__discount"><?php echo affpromos_get_promotion_discount(); ?></span>
                    <?php } ?>
                </div>

                <div class="affpromos-promotion__content">
                    <a class="affpromos-promotion__title" href="<?php echo $promo_url; ?>"><?php echo affpromos_get_promotion_title(); ?></a>
                    
                    <?php if ( affpromos_promotion_has_valid_dates() ) { ?>
                        <div class="affpromos-promotion__valid-dates">
                            <?php affpromos_the_promotion_valid_dates(); ?>
                        </div>
                    <?php } ?>
                    
                    <div class="affpromos-promotion__description">
                        <?php echo affpromos_get_promotion_description(); ?>
                    </div>

                    <?php if ( affpromos_get_promotion_code() ) { ?>
                        <div class="affpromos-promotion__code">
                            <?php affpromos_the_promotion_code(); ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="affpromos-promotion__footer">
                    <a class="affpromos-promotion__button" href="<?php echo $promo_url; ?>" title="<?php echo affpromos_get_promotion_title(); ?>" rel="nofollow" target="_blank"><span class="affpromos-promotion__button-icon"></span> <?php _e('Go to the deal', AFFILIATE_PROMOTIONS_PLUG); ?></a>
                </div>

            </div>

        </div>
    <?php } ?>
</div>