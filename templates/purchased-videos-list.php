<?php

                $options = get_option('hss_woo_options');

$customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types( 'view-orders' ),
        'post_status' => array_keys( wc_get_order_statuses() ),
) ) );

if ( $customer_orders ) : ?>

        <div class="hss_woo_myaccount_videolist_header"><?php echo __( 'Purchased Videos', 'hss-woo' );?></div>

                <tbody>
                        <?php
                        $videos = array();
                        foreach ( $customer_orders as $customer_order ) :
                                $order      = wc_get_order( $customer_order );
                                $item_count = $order->get_item_count();
                                foreach( $order->get_items() as $item_id => $item ) {
                                        $product = $item->get_product();
                                        if($product !== false)
                                        {
                                                $product_name = $product->get_title();
                                                if($product->get_type()=="variation"){
                                                        $product = new WC_Product($product->get_parent_id());
                                                }
                                                if(get_post_meta($product->get_id(), 'is_streaming_video', true)) {
                                                        $hss_video_id = get_post_meta($product->get_id(), '_woo_video_id', true);
                                                        if(!in_array($hss_video_id,$videos)){
                                                                $is_visible        = true;
                                                                $product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
                                                                echo "<div class=\"hss_woo_myaccount_videolist_video_link\">";
                                                                echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', get_permalink( get_option('woocommerce_myaccount_page_id'))."/view-video/?hss-video-id=".$product->get_id(), $product_name ) : $product_name, $item, $is_visible );
                                                                echo "</div>";
                                                                array_push($videos,$hss_video_id);
                                                        }
                                                }elseif(get_post_meta($product->get_id(), 'is_streaming_video_bundle', true)) {
                                                        $hss_group_id = get_post_meta($product->get_id(), '_hss_woo_group_id', true);
                                                        if(!in_array($hss_group_id,$videos)){
                                                                $is_visible        = true;
                                                                $product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
                                                                echo "<div class=\"hss_woo_myaccount_videolist_video_link\">";
                                                                echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', get_permalink( get_option('woocommerce_myaccount_page_id'))."/view-video/?hss-video-id=".$product->get_id(), $product_name ) : $product_name, $item, $is_visible );
                                                                echo "</div>";
                                                                array_push($videos,$hss_group_id);
                                                        }
                                                }
                                        }
                                }

                                ?>
                        <?php endforeach; ?>
                </tbody>
        </table>
<?php endif;

?>
