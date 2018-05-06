<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 05/05/2018
 * Time: 11:44
 */
class ProductCrawler {
	
	private $parser_callback = array(
		'adayroi.com'           => 'crawl_site_adayroi',
		'adayroi.vn'            => 'crawl_site_adayroi',
		'deal.adayroi.com'      => 'crawl_site_adayroi_deal',
		'tiki.vn'               => 'crawl_site_tiki',
		'lazada.com'            => 'crawl_site_lazada',
		'lazada.vn'             => 'crawl_site_lazada',
		'laneige.com'           => 'crawl_site_laneige',
	
	);
	
	private $defaul_opt = array(
		'timeout'   => 10,
		'headers'   => array(
			'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.103 Safari/537.36',
		)
	);
	
	function open_site_conc($url)
	{
		$host = str_replace('www.','',parse_url($url)['host']);
		$parser = $this->parser_callback;
		if(!isset($parser[$host]))
		{
			return array(
				'error' => 'We are not support this site yet !'
			);
		}
		$response = wp_remote_get($url,$this->defaul_opt);
		if(!isset($response['body']))
			return array(
				'error' => 'Url error or connection loss !!',
			);
		
		$_html = str_get_html($response['body']);
		
		try{
			
			return call_user_func_array(array($this,$parser[$host]) ,array($_html,$url));
		}catch (Error $e)
		{
			return array(
				'error' => 'The format of this site is unnoticed ! Please wait for new update '
			);
		}

		
	}
	
	public function crawl_site_tiki($_html, $url)
	{
		
		$_html = $_html->find('div[class=container]',4);
		
		if (empty($_html))
		{
			return array(
				'error'=>'Site not supported !!'
			);
		}
		
		$title = $_html->find('h1[class=item-name]',0)->plaintext;
		
		$price_sale = intval($_html->find('p[id=p-specialprice]',0)->getAttribute('data-value'));
		try{
			$price = intval($_html->find('p[id=p-listpirce]',0)->getAttribute('data-value'));
		}catch (Error $e){
			$price = $price_sale;
		}
		$images = ($_html->find('*[data-zoom-image]'));
		$image_url = strval($images[0]->getAttribute('data-zoom-image'));
		
		
		if(count($images) > 2){
			$thumbnail_url = strval($images[1]->children(0)->getAttribute('src'));
		}else{
			$thumbnail_url = $image_url;
		}
		
		
		$specs = strval($_html->find('div[class=product-table-box is-left]',0));
//
		$desc = strval($_html->find('div[class=product-content-box]',0));
		
		
		try{
			$short_desc = ($_html->find('div[class=top-feature-item]',0)->plaintext);
			$short_desc = ($short_desc == null) ? '' : $short_desc;
		}catch (Error $e){
			$short_desc = '';
		}
		
		$obj_offer = (object) array(
			'title'                 => $title,
			'thumbnail'             => $thumbnail_url,
			'featured_image'        => $image_url,
			'vendor'                => 'tiki.vn',
			'url'                   => $url,
			'desc'                  => $desc,
			'short_desc'            => $short_desc,
			'specs'                 => $specs,
			'price'                 => intval($price),
			'sale_price'            => intval($price_sale),
			'start_timestamp'       => time(),
			'expiration_timestamp'  => 99999999999,
		);
		
		$id = insert_new_offer($obj_offer);
		return array(
			'offer_id' => $id
		);
		
	}
	
	public function crawl_site_adayroi($_html, $url)
	{
//		var_dump(esc_html($_html));
//		$_html = $_html->find('div[id=page_content_left]',0);
		
		if (empty($_html))
		{
			return array(
				'error'=>'Site not supported !!'
			);
		}
		$title = $_html->find('h1',0)->plaintext;
		
		$price_sale_el = $_html->find('.sticky-header__info-price',0);
		$price_sale = $price_sale_el->plaintext;
		$price_el = $price_sale_el->children(0);
		if($price_el){
			$price = $price_el->plaintext;
			$price_sale = str_replace($price,'',$price_sale);
		}else{
			$price = $price_sale;
		}
		$price_sale = intval(str_replace('.','',$price_sale));
		$price = intval(str_replace('.','',$price));
		
		
		$image_url = strval($_html->find('.product-item__thumbnail img',0)->getAttribute('src'));
		
		$specs = $_html->find('.short-des__content .panel-body',0)->plaintext;

		
		try{
			$thumnail_url = strval($_html->find('.thumbnails .items .item img',1)->getAttribute('src'));
		}catch(Error $e){
			$thumnail_url = $image_url;
		}
		$obj_offer = (object) array(
			'title'                 => $title,
			'thumbnail'             => $thumnail_url,
			'featured_image'        => $image_url,
			'vendor'                => 'adayroi.com',
			'url'                   => $url,
			'desc'                  => $specs,
			'short_desc'            => $specs,
			'specs'                 => $specs,
			'price'                 => intval($price),
			'sale_price'            => intval($price_sale),
			'start_timestamp'       => time(),
			'expiration_timestamp'  => 99999999999,
		);
		
		$id = insert_new_offer($obj_offer);
		return array(
			'offer_id' => $id
		);
	}
	
	public function crawl_site_adayroi_deal($_html, $url)
	{
		$top_html = $_html->find('div[class=service_info]',0);
		
		if (empty($top_html))
		{
			return array(
				'error'=>'Site not supported !!'
			);
		}
		$title = $top_html->find('h1',0)->plaintext;
		
		$price = $top_html->find('div[class=item-original-price]',0)->plaintext;
		$price = str_replace('.','',$price);
		try{
			$price_sale = $top_html->find('div[class=item-price]',0)->plaintext;
			$price_sale = str_replace('.','',$price_sale);
		}catch (Error $e){
			$price_sale = $price;
		}
		
		$image_url = strval($top_html->find('img[class=lazyOwl]',0)->getAttribute('data-src'));
		
		$short_desc = strval($_html->find('div[id=tab_content_service_conditions]',0));
		
		$desc = strval($_html->find('div[id=tab_content_service_descriptions]',0));
		
		$specs = $short_desc;
		
		try{
			$thumnail_url = strval($top_html->find('img[class=lazyOwl]',1)->getAttribute('data-src'));
		}catch (Error $e){
			$thumnail_url = $image_url;
		}
		
		$obj_offer = (object) array(
			'title'                 => $title,
			'thumbnail'             => $thumnail_url,
			'featured_image'        => $image_url,
			'vendor'                => 'adayroi.com',
			'url'                   => $url,
			'desc'                  => $desc,
			'short_desc'            => $short_desc,
			'specs'                 => $specs,
			'price'                 => intval($price),
			'sale_price'            => intval($price_sale),
			'start_timestamp'       => time(),
			'expiration_timestamp'  => 99999999999,
		);
		
		$id = insert_new_offer($obj_offer);
		return array(
			'offer_id' => $id
		);
	}
	
	public function crawl_site_lazada($_html,$url){
		
		$_html = $_html->find('div[id=prd-detail-page]',0);
		
		$title = $_html->find('h1[id=prod_title]',0)->plaintext;
		
		$price_sale = $_html->find('span[id=product_price]',0)->plaintext;
		
		try{
			$price = $_html->find('span[id=price_box]',0)->plaintext;
			$price = str_replace(' VND','',$price);
			$price = str_replace('.','',$price);
		}catch (Error $e){
			$price = $price_sale;
		}
		
		$images = ($_html->find('*[data-big]'));
		$image_url = strval($images[0] ->getAttribute('data-big'));
		
		if(count($images) > 2){
			$thumbnail_url = strval($images[1] ->getAttribute('data-swap-image'));
		}else{
			$thumbnail_url = strval($images[0] ->getAttribute('data-swap-image'));
		}
		
		$specs = strval($_html->find('div[class=product-description__inbox toclear]',0));
		
		$short_desc = strval($_html->find('div[class=prod_details]',0)->plaintext);
		
		$desc = strval($_html->find('div[id=productDetails]',0));
		
		
		$obj_offer = (object) array(
			'title'                 => $title,
			'thumbnail'             => $thumbnail_url,
			'featured_image'        => $image_url,
			'vendor'                => 'lazada.vn',
			'url'                   => $url,
			'desc'                  => $desc,
			'short_desc'            => $short_desc,
			'specs'                 => $specs,
			'price'                 => intval($price),
			'sale_price'            => intval($price_sale),
			'start_timestamp'       => time(),
			'expiration_timestamp'  => 99999999999,
		);
		
		$id = insert_new_offer($obj_offer);
		
		return array(
			'offer_id' => $id
		);
	}
	
}
