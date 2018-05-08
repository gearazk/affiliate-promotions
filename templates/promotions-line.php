<?php
/*
 * Promotions line template
 */
    
    if ( ! isset ( $posts ) ) {
        return;
    }

    if(!wp_script_is('promos-line.css','enqueued')) {
        wp_enqueue_style( 'promos-line.css', plugins_url('public/assets/css/promos-line.css', dirname(__FILE__)),array(),'0.1' );
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'promotions-line-postload', plugins_url( '/public/assets/js/promotions-line-postload.js', dirname(__FILE__) ), array(), '0.1', true );
    }

?>

<div class="coupon-store vertical">
    <div class="content inner">
        <div class="vertical">
            <div class="content">
                <section class="store-coupon-list">
                    <?php while ( $posts->have_posts() ) { ?>
                    
                    <?php $posts->the_post(); ?>
                    <article class="item coupon ">
                        <div class="inner">
                            <div class="left-inner-wrapper">
                                <div class="store-picture">
                                    <?php affpromos_get_promotion_vendor_thumbnail(); ?>
                                </div>
                                <div class="left">
                                    <a href="<?php echo affpromos_get_promotion_url(); ?>">
                                        <div class="logo-text code">
                                            <div class="logo">
                                                <?php affpromos_get_promotion_vendor_thumbnail(); ?>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="right">
                                    <div class="right-inner">
                                        <div class="tags"> <span class="tag recommended code"><?php echo affpromos_the_promotion_vendor();?></span>
                                            <div class="tag verified">
                                                <i class="fa fa-check-circle-o"></i><span>Xác nhận</span> </div>
                                            <div class="tag tag-hide validity">
                                                <i class="fa fa-clock-o"></i><span><?php echo affpromos_the_promotion_valid_text(); ?>  </span>
                                            </div>
                                        </div>
                                        <div class="name">
                                            <a target="_blank" href="<?php echo affpromos_get_promotion_url(); ?>" rel="nofollow" >
                                                <h3><?php echo affpromos_get_promotion_title(); ?></h3> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php $code = affpromos_get_promotion_code();
                                if (!$code){
                            ?>
                                <div class="action nocode">
                                    <a href="<?php echo affpromos_get_promotion_url(); ?>" rel="nofollow" >
                                        <span class="button nocode">NHẬN KHUYẾN MÃI<i class="icon-call-action"></i></span> </a>
                                </div>
                            <?php }else{
                                    $code = substr($code,-3);
                                    ?>
                                <div class="action orange">
                                    <a class="flip-button flip-main flip-block flip-effect open-tab" data-idoffer="<?php echo get_the_ID() ?>"  href="<?php echo affpromos_get_promotion_url(); ?>" rel="nofollow" >
                                        <div class="label"> <span><?php echo $code ?></span> </div> <span class="button">MÃ GIẢM GIÁ</span> </a>
                                </div>
                            <?php } ?>

                        </div>
                        <div class="details-toggle"> <span> Chi tiết <i class="fa fa-arrow-down"></i> </span> </div>
                        <div class="coupon-details hide-details">

                            <div class="holder">
                                <div class="fields">
                                    <div>
                                        <?php
                                        echo affpromos_get_promotion_description();
                                        ?>
                                    </div>
                                </div>
                                <?php
                                if (!$code){
                                    ?>
                                    <div class="action nocode">
                                        <a href="<?php echo affpromos_get_promotion_url(); ?>" rel="nofollow" >
                                            <span class="button nocode">NHẬN KHUYẾN MÃI<i class="icon-call-action"></i></span> </a>
                                    </div>
                                <?php }else{
                                    ?>
                                    <div class="action orange">
                                        <a class="flip-button flip-main flip-block flip-effect open-tab" data-idoffer="<?php echo get_the_ID() ?>" href="<?php echo affpromos_get_promotion_url(); ?>" rel="nofollow" >
                                            <div class="label"> <span><?php echo $code ?></span> </div> <span class="button">MÃ GIẢM GIÁ</span> </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                    <?php } ?>

                </section>
            </div>
        </div>
    </div>
</div>