<?php
/*
 * Product Card template
 */

//if ( ! isset ( $posts ) ) { return; }

if(!wp_script_is('product-card.css', 'enqueued')){
    wp_enqueue_style( 'product-card.css', plugins_url('public/assets/css/product-shortcode.css', dirname(__FILE__)), array(), '0.4' );
}

$title = affpromos_get_offer_title($post->ID);
$url = affpromos_get_offer_url($post->ID);

?>

<div class='product clearfix product-align-<?php echo $align; ?>'>
	<div class='product-inner'>
		<div class='product-ribbon'>
			<span><?php echo $sticker; ?></span>
		</div>
		
		<!-- Left side (Product imgages + Buy button) -->
		<div class='product-<?php echo $align; ?>'>
			<a class='product-thumbnail' href='<?php echo $url;?>' rel='nofollow' target='_blank'><img src='<?php echo get_the_post_thumbnail_url($post->ID)?>'>
			</a>
			<div class='buy-product'>
				<div class='product-pricebox product-pricebox-0'>
					<a href='<?php echo $url;?>' rel='nofollow' target='_blank'>
						<span itemprop='price'><?php echo affpromos_get_offer_price_sale_vnd($post->ID);?> from <?php echo affpromos_get_offer_vendor_name($post->ID);?>  </span>
					</a>
				</div>
			</div>
		</div>
		
		<!-- Right side (Title, Short Desc) -->
		<div class='product-<?php echo $align; ?>  '>
			
			<div class='product-title'>
				<a href='#' rel='nofollow' target='_blank'><?php echo $sub_header; ?></a>
			</div>

			<a href='<?php echo $url; ?>' class='product-make-model product-name' rel='nofollow' target='_blank'><?php echo $title; ?>
			</a>

			<div class='product-text product-description' itemprop='description'>
				<?php echo get_the_excerpt($post->ID);?>
			</div>
		</div>
	</div>
</div>