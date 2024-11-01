
<?php

	global $wp_query;
	$options = get_option('hss_woo_options');
	$vidpost = get_post($wp_query->query_vars['hss-video-id']);

	$product = wc_get_product($vidpost->ID);
?>
        <div class="hss_woo_myaccount_video_header"><?php echo $product->get_title();?></div>
        <tbody>
<?php
        $productid = urldecode($wp_query->query_vars['hss-video-id']);
        hss_woo_before_download_content($productid,True);
        echo '<div class="hss_woo_myaccount_back"><a href="'.get_permalink( get_option('woocommerce_myaccount_page_id')).'/videos/">'.__( 'Back', 'hss-woo' ).'</div></tbody>';
?>
