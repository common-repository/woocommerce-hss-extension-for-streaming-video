
<?php

	global $wp_query;
	$options = get_option('hss_woo_options');
	$vidpost = get_post($wp_query->query_vars['hss-video-id']);


        $product = wc_get_product($vidpost->ID);
        ?>
              <div class="hss_woo_myaccount_videogroup_header"><?php echo $product->get_title();?></div>
              <tbody>
        <?php
        $group_products = get_post_meta($vidpost->ID, '_hss_woo_bundled_products', true);
        foreach ($group_products as $product_id)
        {
		$product = wc_get_product($product_id);

		echo "<div class=\"hss_woo_myaccount_videolist_video_link\">";
                echo '<a href="'.get_permalink( get_option('woocommerce_myaccount_page_id'))."/view-video/?hss-video-id=".$product->id.'">'.$product->get_title().'</a>';
        	echo "</div>";
	}
	echo '<div class="hss_woo_myaccount_back"><a href="'.get_permalink( get_option('woocommerce_myaccount_page_id')).'/videos/">'.__( 'Back', 'hss-woo' ).'</div></tbody>';
?>
