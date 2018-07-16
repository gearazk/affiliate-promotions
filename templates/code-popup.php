<?php


    if ( isset($_GET['popup-code']) && is_numeric($_GET['popup-code']) ){
        $popup_id = intval($_GET['popup-code']);
        $post = get_post($popup_id);
        if (isset($post) && affpromos_get_promotion_code($post->ID) ){
            $title = affpromos_get_promotion_title($post->ID);
            $url = affpromos_get_promotion_url($post->ID);

            if(!wp_script_is('code-popup.css', 'enqueued')){
                wp_enqueue_style( 'code-popup.css', plugins_url('public/assets/css/code-popup.css', dirname(__FILE__)), array(), '0.1' );
                wp_enqueue_script('promotions-line-postload', plugins_url('/public/assets/js/promotions-line-postload.js', dirname(__FILE__)), array(), '0.1', true);
            }
?>
<div id="coupon-popup" class="popup">
    <div class="inner">
        <div class="header">
            <div class="coupon-popup-img">
                <?php affpromos_get_promotion_vendor_thumbnail($post->ID); ?>
            </div>
            <div class="coupon-popup-name">
                <?php echo $title; ?>
            </div>
        </div>
        <div class="coupon-popup-text">
            <p>Mã được sử dụng 1 lần/1 tháng và không được hoàn trả hay quy đổi thành tiền mặt.</p>
        </div>
        <div class="body">
            <div class="code-title"> Đây là coupon của bạn! </div>
            <div class="code-subtitle">
                Đi đến <a href="<?php echo $url; ?>" rel="nofollow" data-trigger="ga-conversion" data-action="coupon" data-merchant="<?php echo affpromos_get_offer_vendor_name($post->ID) ?>">trang này</a> và nhập mã coupon khi thanh toán </div>
            <div class="coupon-popup-code-table">
                <div class="code-text-row">
                    <span id="code" class="code-text-cell"> <?php echo affpromos_get_promotion_code($post->ID) ?> </span>
                </div>
                <div class="copy-code-row">
                    <div class="copy-code-cell">
                        <a href="#" id="copy-text" class="copy-code-button" data-clipboard-target="#code" data-id="<?php echo affpromos_get_promotion_code($post->ID) ?>"> Sao chép </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="coupon-newsletter inner">
        <div class="separator">
            <?php affpromos_get_promotion_vendor_thumbnail($post->ID); ?>
        </div>
    </div>
</div>
<?php
        }
    }
?>