<?php

register_activation_hook(__FILE__, 'hss_add_defaults');
register_uninstall_hook(__FILE__, 'hss_delete_plugin_options');
add_action('admin_init', 'hss_init');

function hss_add_defaults()
{
        $tmp = get_option('hss_woo_options');
        if (($tmp['chk_default_options_db'] == '1') || (!is_array($tmp))) {
                delete_option('hss_woo_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
                $arr = array("api_key" => "", "jwplayer_stretching" => "uniform", "logging" => "NORMAL", "database_id" => "0", "jwplayer_version" => "videojs5", "jwplayer_logo_file" => "", "jwplayer_logo_link" => "", "jwplayer_logo_hide" => "false", "player_responsive_max_width" => "true", "log_player_events" => "true");
                update_option('hss_woo_options', $arr);
        }
}

function hss_delete_plugin_options()
{
        delete_option('hss_woo_options');
}

function hss_init()
{
        register_setting('hss_plugin_options', 'hss_woo_options', 'hss_validate_options');

        //hss_create_page( esc_sql( _x( 'my-videos', 'page_slug', 'woocommerce' ) ), 'woocommerce_my_videos_page_id', __( 'My Videos', 'woocommerce' ), '[woocommerce_my_videos]', hss_get_page_id( 'myaccount' ) );

        $options = get_option('hss_woo_options');
        $options['responsive_player'] = 0;
        $options['disable_desc_updates'] = 0;
        $options['add_video_on_processing'] = 0;
        $options['add_video_on_processing'] = 0;
        $options['use_non_loggedin_video_links'] = 0;
        $options['videos-template-endpoint'] = "";
        $options['view-video-template-endpoint'] = "";
        $options['disable_player_fast_forward'] = "false";
        $options['product_virtual'] = "virtual";

        if (is_array($options)) {
                if (array_key_exists('database_id', $options)) {
                        if ($options['database_id'] == "") {
                                $options['database_id'] = "0";
                                update_option('hss_woo_options', $options);
                        }
                } else {
                        $options['database_id'] = "0";
                        update_option('hss_woo_options', $options);
                }
                if (array_key_exists('watching_video_text', $options) == false) {
                        $options['watching_video_text'] = "You have access to this video";
                        update_option('hss_woo_options', $options);
                }
                if (array_key_exists('jwplayer_version', $options) == false) {
                        $options['jwplayer_version'] = "videojs5";
                        update_option('hss_woo_options', $options);
                }
                if (array_key_exists('jwplayer_logo_file', $options) == false) {
                        $options['jwplayer_logo_file'] = "";
                        update_option('hss_woo_options', $options);
                }
                if (array_key_exists('jwplayer_logo_link', $options) == false) {
                        $options['jwplayer_logo_link'] = "";
                        update_option('hss_woo_options', $options);
                }
                if (array_key_exists('jwplayer_logo_hide', $options) == false) {
                        $options['jwplayer_logo_hide'] = "false";
                        update_option('hss_woo_options', $options);
                }
                if (array_key_exists('log_player_events', $options) == false) {
                        $options['log_player_events'] = "true";
                        update_option('hss_woo_options', $options);
                }
                if (array_key_exists('disable_player_fast_forward', $options) == false) {
                        $options['disable_player_fast_forward'] = "false";
                        update_option('hss_woo_options', $options);
                }
                if (array_key_exists('product_virtual', $options) == false) {
                        $options['product_virtual'] = "virtual";
                        update_option('hss_woo_options', $options);
                }
        }
}

add_action('plugins_loaded', 'hss_woo_load_textdomain');
function hss_woo_load_textdomain()
{
        load_plugin_textdomain('hss-woo', false, dirname(plugin_basename(__DIR__)) . '/lang/');
}

function hss_create_page($slug, $option, $page_title = '', $page_content = '', $post_parent = 0)
{
        global $wpdb;

        $option_value = get_option($option);

        if ($option_value > 0 && get_post($option_value))
                return;

        $page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s LIMIT 1;", $slug));
        if ($page_found) {
                if (!$option_value)
                        update_option($option, $page_found);
                return;
        }

        $page_data = array(
                'post_status'           => 'publish',
                'post_type'             => 'page',
                'post_author'           => 1,
                'post_name'             => $slug,
                'post_title'            => $page_title,
                'post_content'          => $page_content,
                'post_parent'           => $post_parent,
                'comment_status'        => 'closed'
        );
        $page_id = wp_insert_post($page_data);

        update_option($option, $page_id);
}

function hss_get_page_id($page)
{
        $page = apply_filters('woocommerce_get_' . $page . '_page_id', get_option('woocommerce_' . $page . '_page_id'));
        return ($page) ? $page : -1;
}


function hss_validate_options($input)
{
        // strip html from textboxes
        $input['api_key'] =  trim(wp_filter_nohtml_kses($input['api_key']));

        if (!isset($input['responsive_player']))
                $input['responsive_player'] = 0;
        if (!isset($input['disable_desc_updates']))
                $input['disable_desc_updates'] = 0;
        if (!isset($input['add_video_on_processing']))
                $input['add_video_on_processing'] = 0;
        if (!isset($input['add_video_on_processing']))
                $input['add_video_on_processing'] = 0;
        if (!isset($input['use_non_loggedin_video_links']))
                $input['use_non_loggedin_video_links'] = 0;

        if (!is_numeric($input['database_id'])) {
                $input['database_id'] = "0";
        } else {
                $input['database_id'] =  trim(wp_filter_nohtml_kses($input['database_id']));
        }
        return $input;
}

add_action('admin_head', 'my_action_javascript');

function my_action_javascript()
{
?>
        <script type="text/javascript">
                jQuery(document).ready(function($) {
                        $('#myajax').click(function() {
                                var data = {
                                        action: 'my_action'
                                };
                                $("#updateprogress").html("Updating... please wait!");

                                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                                $.post(ajaxurl, data, function(response) {
                                        $("#updateprogress").html("");
                                        alert(response);

                                });
                        });

                        $('#hss_ajax_update_new').click(function() {
                                var data = {
                                        action: 'hss_action_update_new'
                                };
                                $("#hss_new_updateprogress").html("Updating... please wait!");

                                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                                $.post(ajaxurl, data, function(response) {
                                        $("#hss_new_updateprogress").html("");
                                        alert(response);

                                });
                        });
                });
        </script>
<?php
}

add_action('wp_ajax_my_action', 'my_action_callback');

function my_action_callback()
{
        $res = update_videos();
        if ($res == True)
                echo "Success";
        else
                echo "Error occurred " . $res;
        die(); // this is required to return a proper result
}


add_action('wp_ajax_hss_action_update_new', 'hss_action_update_new_callback');

function hss_action_update_new_callback()
{
        $res = update_videos(true);
        if ($res == True)
                echo "Success";
        else
                echo "Error occurred " . $res;
        die(); // this is required to return a proper result
}




add_action('wp_head', 'pluginname_ajaxurl');
function pluginname_ajaxurl()
{
?>
        <script type="text/javascript">
                var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
<?php
}

add_action('wp_ajax_get_download_links', 'get_download_links_callback');
add_action('wp_ajax_nopriv_get_download_links', 'get_download_links_callback');
function get_download_links_callback()
{
        _hss_woo_log("get_download_links_callback");
        $purchase_id = $_POST['purchase_id'];
        $videolink = $_POST['videolink'];
        #$video_id = get_post_meta($purchase_id, '_woo_video_id', true);
        $video_id = $purchase_id;
        echo get_video_download_links($video_id, $videolink);

        die(); // this is required to return a proper result
}

add_action('wp_print_footer_scripts', 'get_download_links_javascript');

function get_download_links_javascript()
{
?>
        <script type="text/javascript">
                jQuery(document).ready(function($) {
                        $('.myajaxdownloadlinks').attr("disabled", false);
                        $('.myajaxdownloadlinks').click(function(event) {
                                $('#' + event.target.id).attr("disabled", true);
                                var data = {
                                        action: 'get_download_links',
                                        purchase_id: event.target.id,
                                        <?php if (isset($_GET['videolink'])) { ?>
                                                videolink: '<?php echo $_GET['videolink']; ?>'
                                        <?php        } else { ?>
                                                videolink: ''
                                        <?php        } ?>
                                };

                                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                                $.post(ajaxurl, data, function(response) {
                                        //$('#'+event.target.id).css("visibility", "hidden");
                                        $("#download_links_" + event.target.id).html(response);
                                        setTimeout(function() {
                                                $('#download_links_' + event.target.id).html("");
                                                $('#' + event.target.id).attr("disabled", false);
                                                //$('#'+event.target.id).css("visibility", "visible");
                                        }, 240000);
                                });
                        });
                });
        </script>
<?php
}


add_action('wp_ajax_hss_woo_store_user_setting', 'hss_woo_store_user_setting_callback');
add_action('wp_ajax_nopriv_hss_woo_store_user_setting', 'hss_woo_store_user_setting_callback');
function hss_woo_store_user_setting_callback()
{
        _hss_woo_log("hss_woo_store_user_setting_callback");
        global $user_ID;

        $setting_name = $_POST['setting_name'];
        $setting_value = $_POST['setting_value'];
        _hss_woo_log("hss_woo_store_user_setting_callback " . $user_ID . " " . $setting_name . " = " . $setting_value);

        if ($user_ID != "" and $setting_name != "" and $setting_value != "") {
                #$user_ID = (int)$user_ID;
                if ($user_ID > 0) {
                        _hss_woo_log("hss_woo_store_user_setting_callback user_id=" . $user_ID . " setting..");
                        $options = get_option('hss_woo_options');
                        if (!isset($options['user_settings']))
                                $options['user_settings'] = [];
                        if (!isset($options['user_settings'][$user_ID]))
                                $options['user_settings'][$user_ID] = [];
                        $options['user_settings'][$user_ID][$setting_name] = $setting_value;
                        update_option('hss_woo_options', $options);
                        _hss_woo_log("hss_woo_store_user_setting_callback " . $user_ID . " " . $setting_name . " = " . $options['user_settings'][$user_ID][$setting_name]);
                }
        }



        die(); // this is required to return a proper result
}



// Register style sheet.
add_action('wp_enqueue_scripts', 'register_hss_woo_plugin_styles');
/**
 * Register style sheet.
 */

function register_hss_woo_plugin_styles()
{
        wp_register_style('woocommerce-hss-extension-for-streaming-video', plugins_url('woocommerce-hss-extension-for-streaming-video/css/hss-woo.css'));
        wp_enqueue_style('woocommerce-hss-extension-for-streaming-video');
}



function hss_woo_locate_template($template_name, $template_path = '', $default_path = '')
{

        // Set variable to search in hss-woo-templates folder of theme.
        if (!$template_path) :
                $template_path = 'hss-woo-templates/';
        endif;

        // Set default plugin templates path.
        if (!$default_path) :
                $default_path = plugin_dir_path(dirname(__FILE__)) . 'templates/'; // Path to the template folder
        endif;

        // Search template file in theme folder.
        $template = locate_template(array(
                $template_path . $template_name,
                $template_name
        ));

        // Get plugins template file.
        if (!$template) :
                $template = $default_path . $template_name;
        endif;

        return apply_filters('hss_woo_locate_template', $template, $template_name, $template_path, $default_path);
}


function hss_woo_get_template($template_name, $args = array(), $tempate_path = '', $default_path = '')
{

        if (is_array($args) && isset($args)) :
                extract($args);
        endif;

        $template_file = hss_woo_locate_template($template_name, $tempate_path, $default_path);

        if (!file_exists($template_file)) :
                _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.0.0');
                return;
        endif;

        include $template_file;
}


/**
 * Add a custom product tab.
 */
function custom_product_tabs($tabs)
{

        $tabs['hss_group_videos'] = array(
                'label'                => __('HSS Video', 'woocommerce'),
                'target'        => 'video_options',
                'class'                => array('show_if_simple', 'show_if_variable'),
        );
        return $tabs;
}
add_filter('woocommerce_product_data_tabs', 'custom_product_tabs');



/**
 * Contents of the gift card options product tab.
 */
function giftcard_options_product_tab_content()
{
        global $post;

?><div id='video_options' class='panel woocommerce_options_panel'><?php
                                                                        ?><div class='options_group'><?php


                                                                                                        echo '<h2>HSS Video Options</h2>';
                                                                                                        //echo '<p>Select the video products inluded in this group</p>';

                                                                                                        $video_type = "none";
                                                                                                        if (get_post_meta($post->ID, 'is_streaming_video', true) == 1)
                                                                                                                $video_type = "video";
                                                                                                        elseif (get_post_meta($post->ID, 'is_streaming_video_bundle', true) == 1)
                                                                                                                $video_type = "video group";

                                                                                                        woocommerce_wp_select(array(
                                                                                                                'id' => '_hss_video_type', 'label' => __('HSS Product Type', 'woocommerce'),
                                                                                                                'value' => $video_type,
                                                                                                                'options' => array(
                                                                                                                        'none' => __('Neither', 'woocommerce'), 'video' => __('Video', 'woocommerce'), 'video group' => __('Video Group', 'woocommerce')
                                                                                                                )
                                                                                                        ));

                                                                                                        if ('video group' === $video_type) {

                                                                                                                /*$args = array(
        'post_type' => 'product',                        
        'meta_query' => array(
            array(
                'key' => 'is_streaming_video',
                'value'    => '1',
                ),
        ),
    );
 */



                                                                                                                $selected_videos = get_post_meta($post->ID, '_hss_woo_bundled_products', true);
                                                                                                                _hss_woo_log(print_r($selected_videos, TRUE));
                                                                                                                $args = array(
                                                                                                                        'post_type' => array('product'),
                                                                                                                        'posts_per_page' => -1,
                                                                                                                        'orderby' => 'post__in',
                                                                                                                        'post__in' => $selected_videos
                                                                                                                );


                                                                                                        ?><p class='form-field _video_group_videos'>
                                        <label><?php _e('Included Videos', 'woocommerce'); ?></label>
                                        <select name='_video_group_videos[]' class='wc-enhanced-select' multiple='multiple' style='width: 80%;'>
                                                <?php
                                                                                                                $videos_query = new WP_Query($args);

                                                                                                                if ($videos_query->have_posts()) :

                                                                                                                        while ($videos_query->have_posts()) :

                                                                                                                                $videos_query->the_post();

                                                                                                                                $product = wc_get_product($videos_query->post->ID);
                                                ?>
                                                                <option <?php selected(in_array($videos_query->post->ID, $selected_videos)); ?> value='<?php echo $videos_query->post->ID; ?>'><?php echo $product->get_title(); ?></option>
                                                <?php
                                                                                                                        endwhile;

                                                                                                                endif;

                                                                                                                wp_reset_query();
                                                ?>

                                        </select>
                                </p>
                        <?php
                                                                                                        } elseif ('video' === $video_type) {
                        ?>
                                <p class='form-field _video_group_videos'>
                                        <label><?php _e('Video ID', 'woocommerce'); ?></label>
                                        <input type='text' name='_woo_video_id' value='<?php echo get_post_meta($post->ID, '_woo_video_id', true); ?>'>
                                <div class="form-field__tooltip">
                                        <?php _e('Enter the video ID from the HSS Video Manager', 'woocommerce'); ?>
                                </div>
                                </p>
                                <p class='form-field _video_group_videos'>
                                        <label><?php _e('VR Video', 'woocommerce'); ?></label>
                                        <input type='checkbox' name='_woo_panorama_video' <?php checked(get_post_meta($post->ID, '_woo_panorama_video', true), 'on'); ?>>
                                <div class="form-field__tooltip">
                                        <?php _e('Enable this box to enable video panorama for 180°/360° videos', 'woocommerce'); ?>
                                </div>
                                </p>
                        <?php
                                                                                                        }


                        ?>
                </div>

        </div><?php




        }
        //add_filter( 'woocommerce_product_data_tabs', 'giftcard_options_product_tab_content' ); // WC 2.5 and below
        add_filter('woocommerce_product_data_panels', 'giftcard_options_product_tab_content'); // WC 3.6 and up


        function save_group_videos_option_fields($post_id)
        {

                $select = $_POST['_hss_video_type'];
                if (!empty($select)) {
                        if ($select == "video") {
                                update_post_meta($post_id, 'is_streaming_video', true);
                                delete_post_meta($post_id, 'is_streaming_video_bundle');
                        } elseif ($select == "video group") {
                                update_post_meta($post_id, 'is_streaming_video_bundle', true);
                                delete_post_meta($post_id, 'is_streaming_video');
                        } else {
                                delete_post_meta($post_id, 'is_streaming_video');
                                delete_post_meta($post_id, 'is_streaming_video_bundle');
                        }
                }


                update_post_meta($post_id, '_hss_woo_bundled_products', (array) $_POST['_video_group_videos']);

                if (isset($_POST['_woo_video_id'])) {
                        update_post_meta($post_id, '_woo_video_id', $_POST['_woo_video_id']);
                }
                
                update_post_meta($post_id, '_woo_panorama_video', $_POST['_woo_panorama_video']);

        }
        add_action('woocommerce_process_product_meta_simple', 'save_group_videos_option_fields');
        add_action('woocommerce_process_product_meta_variable', 'save_group_videos_option_fields');


        function hss_woo_options_page()
        {
                ?>
        <div class="wrap">

                <?php
                if (isset($_GET['check_user_video_plays']) && wp_verify_nonce($_GET['_wpnonce'], 'hss_check_user_video_plays')) {

                        $options = get_option('hss_woo_options');
                        $userId = (int)$_GET['user'];
                        $videoId = (int)$_GET['videoid'];
                        $user = get_user_by('id', $userId);
                ?>
                        <div class="icon32" id="icon-options-general"><br></div>
                        <h2>HostStreamSell Video Play Details for <?php echo $user->user_login; ?></h2>

                        <?php
                        $response = wp_remote_post(
                                "https://www.hoststreamsell.com/api/1/xml/user_video_play_events?api_key=" . $options['api_key'] . "&private_user_id=$userId&database_id=" . $options['database_id'] . "&video_id=$videoId",
                                array(
                                        'method' => 'GET',
                                        'timeout' => 15,
                                        'redirection' => 5,
                                        'httpversion' => '1.0',
                                        'blocking' => true,
                                        'headers' => array(),
                                        'body' => $params,
                                        'cookies' => array()
                                )
                        );
                        $res = "";
                        if (is_wp_error($response)) {
                                $return_string .= 'Error occured retieving video information, please try refresh the page';
                        } else {
                                $res = $response['body'];
                        }
                        $xml = new SimpleXMLElement($res);

                        $count = (int)$xml->result->video_play_count;
                        $index = 1;
                        $video_title = (string)$xml->result->video_title[0];
                        echo "<div><h2>" . $video_title . "</h2></div>";
                        while ($index <= $count) {

                                $video_play_event_count = (string)$xml->result[0]->video_plays->{'play' . $index . '_event_count'}[0];
                                $video_play_ip = (string)$xml->result[0]->video_plays->{'play' . $index . '_ip'}[0];
                                $video_play_browser = (string)$xml->result[0]->video_plays->{'play' . $index . '_browser'}[0];
                                echo "<BR>IP address: " . $video_play_ip . "<BR>Browser user-agent: " . $video_play_browser . "<BR>";
                                $event_index = 1;
                                $start_time = "";
                                $end_time = "";


                                while ($event_index <= $video_play_event_count) {
                                        $video_play_event_ts = (string)$xml->result[0]->video_plays->{'play' . $index}[0]->{'event' . $event_index}[0]->ts[0];
                                        if ($event_index == 1)
                                                $start_time = $video_play_event_ts;
                                        $video_play_event_event = (string)$xml->result[0]->video_plays->{'play' . $index}[0]->{'event' . $event_index}[0]->event[0];
                                        if ($video_play_event_event == "seeking") {
                                                echo $video_play_event_ts . " " . $video_play_event_event . " to " . (string)$xml->result[0]->video_plays->{'play' . $index}[0]->{'event' . $event_index}[0]->offset[0] . "s<BR>";
                                        } elseif ($video_play_event_event == "error") {
                                                echo $video_play_event_ts . " " . $video_play_event_event . " " . (string)$xml->result[0]->video_plays->{'play' . $index}[0]->{'event' . $event_index}[0]->message[0] . "<BR>";
                                        } else {
                                                echo $video_play_event_ts . " " . $video_play_event_event . "<BR>";
                                        }
                                        $event_index += 1;
                                        $end_time = $video_play_event_ts;
                                }

                                $index += 1;
                        }
                } elseif (isset($_GET['export_user_video_plays']) && wp_verify_nonce($_GET['_wpnonce'], 'hss_export_user_video_plays')) {

                        function outputCsv($fileName, $assocDataArray)
                        {
                                ob_clean();
                                header('Pragma: public');
                                header('Expires: 0');
                                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                                header('Cache-Control: private', false);
                                header('Content-Type: text/csv');
                                header('Content-Disposition: attachment;filename=' . $fileName);
                                if (isset($assocDataArray['0'])) {
                                        $fp = fopen('php://output', 'w');
                                        fputcsv($fp, array_keys($assocDataArray['0']));
                                        foreach ($assocDataArray as $values) {
                                                fputcsv($fp, $values);
                                        }
                                        fclose($fp);
                                }
                                ob_flush();
                        }

                        $data = array();

                        $options = get_option('hss_woo_options');
                        $userId = (int)$_GET['user'];
                        $user = get_user_by('id', $userId);

                        $response = wp_remote_post(
                                "https://www.hoststreamsell.com/api/1/xml/purchased_videos?api_key=" . $options['api_key'] . "&private_user_id=$userId&database_id=" . $options['database_id'],
                                array(
                                        'method' => 'GET',
                                        'timeout' => 15,
                                        'redirection' => 5,
                                        'httpversion' => '1.0',
                                        'blocking' => true,
                                        'headers' => array(),
                                        'body' => $params,
                                        'cookies' => array()
                                )
                        );
                        $vid_res = "";
                        if (is_wp_error($response)) {
                                $return_string .= 'Error occurred retrieving video information, please try refresh the page';
                        } else {
                                $vid_res = $response['body'];
                        }

                        $vid_xml = new SimpleXMLElement($vid_res);
                        $vid_count = (int)$vid_xml->result->video_count;
                        $vid_index = 1;
                        while ($vid_index <= $vid_count) {
                                $video_id = (string)$vid_xml->result[0]->videos->{'video' . $vid_index}[0]->video_id;
                                $title = (string)$vid_xml->result[0]->videos->{'video' . $vid_index}[0]->title[0];
                                //$description = (string)$xml->result[0]->videos->{'video'.$index}[0]->description[0];
                                $bytes_used = (string)$vid_xml->result[0]->videos->{'video' . $vid_index}[0]->bytes_used[0];
                                $bytes_limit = (string)$vid_xml->result[0]->videos->{'video' . $vid_index}[0]->bytes_limit[0];
                                $expires = (string)$vid_xml->result[0]->videos->{'video' . $vid_index}[0]->expires[0];
                                $expires_on = (string)$vid_xml->result[0]->videos->{'video' . $vid_index}[0]->expires_on[0];
                                $usage = number_format((float)($bytes_used / (1024 * 1024 * 1024)), 2, '.', '') . "GB";
                                if ($bytes_limit != -1) {
                                        $usage = number_format((float)($bytes_used / (1024 * 1024 * 1024)), 2, '.', '') . " / " . number_format((float)($bytes_limit / (1024 * 1024 * 1024)), 2, '.', '') . " GB";
                                }
                                $expires_date = "-";
                                $expires_time = "-";
                                if ($expires == 0)
                                        $expires_on = "never";
                                else {
                                        $expires_tokens = explode(" ", $expires_on);
                                        if (sizeof($expires_tokens) > 1) {
                                                $expires_date = $expires_tokens[0];
                                                $expires_time = $expires_tokens[1];
                                        }
                                }



                                $response = wp_remote_post(
                                        "https://www.hoststreamsell.com/api/1/xml/user_video_play_events?api_key=" . $options['api_key'] . "&private_user_id=$userId&database_id=" . $options['database_id'] . "&video_id=$video_id",
                                        array(
                                                'method' => 'GET',
                                                'timeout' => 15,
                                                'redirection' => 5,
                                                'httpversion' => '1.0',
                                                'blocking' => true,
                                                'headers' => array(),
                                                'body' => $params,
                                                'cookies' => array()
                                        )
                                );
                                $res = "";
                                if (is_wp_error($response)) {
                                        $return_string .= 'Error occured retieving video information, please try refresh the page';
                                } else {
                                        $res = $response['body'];
                                }
                                $xml = new SimpleXMLElement($res);

                                $count = (int)$xml->result->video_play_count;
                                $index = 1;
                                $video_title = (string)$xml->result->video_title[0];
                                //  echo "<div><h2>".$video_title."</h2></div>";
                                while ($index <= $count) {

                                        $video_play_event_count = (string)$xml->result[0]->video_plays->{'play' . $index . '_event_count'}[0];
                                        $video_play_ip = (string)$xml->result[0]->video_plays->{'play' . $index . '_ip'}[0];
                                        $video_play_browser = (string)$xml->result[0]->video_plays->{'play' . $index . '_browser'}[0];
                                        //  echo "<BR>IP address: ".$video_play_ip."<BR>Browser user-agent: ".$video_play_browser."<BR>";
                                        $event_index = 1;
                                        $start_time = "";
                                        $end_time = "";


                                        while ($event_index <= $video_play_event_count) {
                                                $video_play_event_ts = (string)$xml->result[0]->video_plays->{'play' . $index}[0]->{'event' . $event_index}[0]->ts[0];
                                                $date_tokens = explode(" ", $video_play_event_ts);
                                                if ($event_index == 1)
                                                        $start_time = $video_play_event_ts;
                                                $video_play_event_event = (string)$xml->result[0]->video_plays->{'play' . $index}[0]->{'event' . $event_index}[0]->event[0];
                                                $offset = "";
                                                $message = "";
                                                if ($video_play_event_event == "seeking") {
                                                        $offset = (string)$xml->result[0]->video_plays->{'play' . $index}[0]->{'event' . $event_index}[0]->offset[0];
                                                } elseif ($video_play_event_event == "error") {
                                                        $message = (string)$xml->result[0]->video_plays->{'play' . $index}[0]->{'event' . $event_index}[0]->message[0];
                                                }
                                                if (sizeof($date_tokens) > 1)
                                                        array_push($data, array('user' => $user->user_login, 'user id' => $userId, 'video' => $title, 'video hss id' => $video_id, 'event date (UTC)' => $date_tokens[0], 'event time (UTC)' => $date_tokens[1], 'access expires date (UTC)' => $expires_date, 'access expires time (UTC)' => $expires_time, 'player event' => $video_play_event_event, 'offset' => $offset, 'message' => $message, 'ip' => $video_play_ip, 'browser user agent' => $video_play_browser));
                                                $event_index += 1;
                                                $end_time = $video_play_event_ts;
                                        }

                                        $index += 1;
                                }




                                $vid_index += 1;
                        }


                        outputCsv('play-stats-user-' . $userId . '.csv', $data);

                        exit; // This is really important - otherwise it shoves all of your page code into the download





                } elseif (isset($_GET['check_user_video_access']) && wp_verify_nonce($_GET['_wpnonce'], 'hss_check_user_video_access')) {

                        $options = get_option('hss_woo_options');
                        $userId = (int)$_GET['user'];
                        $user = get_user_by('id', $userId);
                        ?>
                        <div class="icon32" id="icon-options-general"><br></div>
                        <h2>HostStreamSell Video Usage for <?php echo $user->user_login; ?></h2>

                        <?php
                        $response = wp_remote_post(
                                "https://www.hoststreamsell.com/api/1/xml/purchased_videos?api_key=" . $options['api_key'] . "&private_user_id=$userId&database_id=" . $options['database_id'],
                                array(
                                        'method' => 'GET',
                                        'timeout' => 15,
                                        'redirection' => 5,
                                        'httpversion' => '1.0',
                                        'blocking' => true,
                                        'headers' => array(),
                                        'body' => $params,
                                        'cookies' => array()
                                )
                        );
                        $res = "";
                        if (is_wp_error($response)) {
                                $return_string .= 'Error occurred retrieving video information, please try refresh the page';
                        } else {
                                $res = $response['body'];
                        }

                        ?>

                        <?php echo '<a class="button-primary" href="' . wp_nonce_url(admin_url('options-general.php?page=hss_admin&export_user_video_plays=yes&user=' . $userId), 'hss_export_user_video_plays', '_wpnonce') . '">' . __('export play details', 'hss-woo') . '</a>'; ?>

                        <table class="form-table">

                                <!-- Textbox Control -->
                                <tr>
                                        <td>Title</td>
                                        <td>
                                                Expiry
                                        </td>
                                        <td>
                                                Streamed Bandwidth
                                        </td>
                                        <td></td>
                                </tr>
                                <?php

                                $xml = new SimpleXMLElement($res);
                                $count = (int)$xml->result->video_count;
                                $index = 1;
                                while ($index <= $count) {
                                        $video_id = (string)$xml->result[0]->videos->{'video' . $index}[0]->video_id;
                                        $title = (string)$xml->result[0]->videos->{'video' . $index}[0]->title[0];
                                        $description = (string)$xml->result[0]->videos->{'video' . $index}[0]->description[0];
                                        $bytes_used = (string)$xml->result[0]->videos->{'video' . $index}[0]->bytes_used[0];
                                        $bytes_limit = (string)$xml->result[0]->videos->{'video' . $index}[0]->bytes_limit[0];
                                        $expires = (string)$xml->result[0]->videos->{'video' . $index}[0]->expires[0];
                                        $expires_on = (string)$xml->result[0]->videos->{'video' . $index}[0]->expires_on[0];
                                        $usage = number_format((float)($bytes_used / (1024 * 1024 * 1024)), 2, '.', '') . "GB";
                                        if ($bytes_limit != -1) {
                                                $usage = number_format((float)($bytes_used / (1024 * 1024 * 1024)), 2, '.', '') . " / " . number_format((float)($bytes_limit / (1024 * 1024 * 1024)), 2, '.', '') . " GB";
                                        }
                                        if ($expires == 0)
                                                $expires_on = "never";
                                ?>

                                        <!-- Textbox Control -->
                                        <tr>
                                                <td><?php echo $title; ?></td>
                                                <td>
                                                        <?php echo $expires_on; ?>
                                                </td>
                                                <td>
                                                        <?php echo $usage; ?>
                                                </td>
                                                <td>
                                                        <?php echo '<a href="' . wp_nonce_url(admin_url('options-general.php?page=hss_admin&check_user_video_plays=yes&user=' . $userId . '&videoid=' . $video_id), 'hss_check_user_video_plays', '_wpnonce') . '">' . __('view play details', 'hss-woo') . '</a>'; ?>
                                                </td>

                                        </tr>

                                <?php
                                        $index += 1;
                                }
                                ?>
                        </table>
                <?php
                } else { ?>
                        <!-- Display Plugin Icon, Header, and Description -->
                        <div class="icon32" id="icon-options-general"><br></div>
                        <h2>HostStreamSell Plugin Settings</h2>
                        <p>Please enter the settings below...</p>

                        <!-- Beginning of the Plugin Options Form -->
                        <form method="post" action="options.php">
                                <?php settings_fields('hss_plugin_options'); ?>
                                <?php $options = get_option('hss_woo_options'); ?>

                                <!-- Table Structure Containing Form Controls -->
                                <!-- Each Plugin Option Defined on a New Table Row -->
                                <table class="form-table">

                                        <!-- Textbox Control -->
                                        <tr>
                                                <th scope="row">HostStreamSell API Key<BR><i>available from your account on www.hoststreamsell.com</i></th>
                                                <td>
                                                        <input type="text" size="40" name="hss_woo_options[api_key]" value="<?php echo $options['api_key']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Website Reference ID<BR><i>leave at 0 unless you sell the same videos from multiple WordPress websites, in which case each website needs a unique reference ID</i></th>
                                                <td>
                                                        <input type="text" size="40" name="hss_woo_options[database_id]" value="<?php echo $options['database_id']; ?>" />
                                                </td>
                                        </tr>

                                        <tr>
                                                <th scope="row">My Account Videos Template<BR><i>Override the default layout of the 'my account' videos screen by specifying a template path - e.g. myaccount/my-videos-endpoint.php</i></th>
                                                <td>
                                                        <input type="text" size="40" name="hss_woo_options[videos-template-endpoint]" value="<?php echo $options['videos-template-endpoint']; ?>" />
                                                </td>
                                        </tr>

                                        <tr>
                                                <th scope="row">My Account View Video Template<BR><i>Override the default layout of the 'my account' view video screen by specifying a template path - e.g. myaccount/my-view-video-endpoint.php</i></th>
                                                <td>
                                                        <input type="text" size="40" name="hss_woo_options[view-video-template-endpoint]" value="<?php echo $options['view-video-template-endpoint']; ?>" />
                                                </td>
                                        </tr>

                                        <tr>
                                                <th scope="row">Video Player Page Location</th>
                                                <td>
                                                        <select name="hss_woo_options[player_location]">
                                                                <?php
                                                                if ($options['player_location'] == "" or $options['player_location'] == "product_tabs") {
                                                                ?><option value="product_tabs" SELECTED>Product Video Tab</option><?php
                                                                                                                                        ?><option value="hook">Specify Hook</option><?php
                                                                                                                                                                                } elseif ($options['player_location'] == "hook") {
                                                                                                                                                                                        ?><option value="product_tabs">Product Video Tab</option><?php
                                                                                                                                                                                                                                                        ?><option value="hook" SELECTED>Specify Hook</option><?php
                                                                                                                                                                                                                                                                                                        } ?>
                                                        </select>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Video Player Hook<BR><i>This is where you set the Hook to be used if Video Player Page Location setting is set to Specify Hook, e.g. woocommerce_single_product_image_thumbnail_html</i></th>
                                                <td>
                                                        <input type="text" size="40" name="hss_woo_options[player_location_hook]" value="<?php echo $options['player_location_hook']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Video Player Size<BR><i>leave blank to use defaults</i></th>
                                                <td>
                                                        Width <input type="text" size="10" name="hss_woo_options[player_width_default]" value="<?php echo $options['player_width_default']; ?>" /> Height <input type="text" size="10" name="hss_woo_options[player_height_default]" value="<?php echo $options['player_height_default']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Mobile Device Video Player Size<BR><i>leave blank to use defaults</i></th>
                                                <td>
                                                        Width <input type="text" size="10" name="hss_woo_options[player_width_mobile]" value="<?php echo $options['player_width_mobile']; ?>" /> Height <input type="text" size="10" name="hss_woo_options[player_height_mobile]" value="<?php echo $options['player_height_mobile']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Make Player Width and Height Responsive</th>
                                                <td>
                                                        <input type="checkbox" name="hss_woo_options[responsive_player]" value="1" <?php checked($options['responsive_player'], 1); ?> />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Reponsive Player Max Width<BR><i>default is 640 if left blank, only used when Reponsive Player checkbox is checked</i></th>
                                                <td>
                                                        Width <input type="text" size="10" name="hss_woo_options[player_responsive_max_width]" value="<?php echo $options['player_responsive_max_width']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Video Player<BR><i>Note: JW Player requires a license file </i></th>
                                                <td>
                                                        <select name="hss_woo_options[jwplayer_version]">
                                                                <?php
                                                                if ($options['jwplayer_version'] == "6") {
                                                                ?><option value="6" SELECTED>JW 6 Free</option><?php
                                                                                                                ?><option value="7">JW 7 Free</option><?php
                                                                                                                                                        ?><option value="7Prem">JW 7 Premium</option><?php
                                                                                                                                                                                                        ?><option value="8">JW 8 (all versions)</option><?php
                                                                                                                                                                                                                                                        ?><option value="videojs7">Videojs 7</option><?php
                                                                                                                                                                                                                                                                                                        ?><option value="videojs5">Videojs 5</option><?php
                                                                                                                                                                                                                                                                                                                                                } elseif ($options['jwplayer_version'] == "7") {
                                                                                                                                                                                                                                                                                                                                                        ?><option value="7" SELECTED>JW 7 Free</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="7Prem">JW 7 Premium</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="6">JW 6 Free</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="8">JW 8 (all versions)</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="videojs7">Videojs 7</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="videojs5">Videojs 5</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                } elseif ($options['jwplayer_version'] == "7Prem") {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="7Prem" SELECTED>JW 7 Premium</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="7">JW 7 Free</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="6">JW 6 Free</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="8">JW 8 (all versions)</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="videojs7">Videojs 7</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="videojs5">Videojs 5</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                } elseif ($options['jwplayer_version'] == "8") {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="8" SELECTED>JW 8 (all versions)</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="7Prem">JW 7 Premium</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="7">JW 7 Free</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="6">JW 6 Free</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="videojs7">Videojs 7</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="videojs5">Videojs 5</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } elseif ($options['jwplayer_version'] == "videojs5") {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="videojs5" SELECTED>Videojs 5</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="videojs7">Videojs 7</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="7Prem">JW 7 Premium</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="7">JW 7 Free</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="8">JW 8 (all versions)</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="6">JW 6 Free</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } elseif ($options['jwplayer_version'] == "videojs7") {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="videojs7" SELECTED>Videojs 7</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="videojs5">Videojs 5</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ?><option value="7Prem">JW 7 Premium</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="7">JW 7 Free</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="8">JW 8 (all versions)</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?><option value="6">JW 6 Free</option><?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } ?>
                                                        </select>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">JW Player License Key<BR><i>available from www.longtailvideo.com</i></th>
                                                <td>
                                                        <input type="text" size="50" name="hss_woo_options[jwplayer_license]" value="<?php echo $options['jwplayer_license']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">JW Player Watermark File URL<BR><i>JW Player only - read more <a href='http://support.jwplayer.com/customer/portal/articles/1406865-branding-your-player' target='_blank'>here</a>)</i></th>
                                                <td>
                                                        <input type="text" size="50" name="hss_woo_options[jwplayer_logo_file]" value="<?php echo $options['jwplayer_logo_file']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">JW Player Watermark Link<BR><i>JW Player only</i></th>
                                                <td>
                                                        <input type="text" size="50" name="hss_woo_options[jwplayer_logo_link]" value="<?php echo $options['jwplayer_logo_link']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">JW Player Watermark Hide<BR><i>JW Player only</i></th>
                                                <td>
                                                        <select name="hss_woo_options[jwplayer_logo_hide]">
                                                                <?php
                                                                if ($options['jwplayer_logo_hide'] == "false" or $options['jwplayer_logo_hide'] == "") {
                                                                ?><option value="false" SELECTED>false</option><?php
                                                                                                                ?><option value="true">true</option><?php
                                                                                                                                                } elseif ($options['jwplayer_logo_hide'] == "true") {
                                                                                                                                                        ?><option value="true" SELECTED>true</option><?php
                                                                                                                                                                                                        ?><option value="false">false</option><?php
                                                                                                                                                                                                                                        } ?>
                                                        </select>
                                                </td>
                                        </tr>

                                        <tr>
                                                <th scope="row">Subtitle Font Size<BR><i>leave blank for default size</i></th>
                                                <td>
                                                        <input type="text" size="5" name="hss_woo_options[subtitle_font_size]" value="<?php echo $options['subtitle_font_size']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Disable updating video title and descriptions</th>
                                                <td>
                                                        <input type="checkbox" name="hss_woo_options[disable_desc_updates]" value="1" <?php checked($options['disable_desc_updates'], 1); ?> />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Add video access when order is in processing state</th>
                                                <td>
                                                        <input type="checkbox" name="hss_woo_options[add_video_on_processing]" value="1" <?php checked($options['add_video_on_processing'], 1); ?> />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Watching Trailer Text<BR><i>leave blank for no message</i></th>
                                                <td>
                                                        <input type="text" size="50" name="hss_woo_options[watching_trailer_text]" value="<?php echo $options['watching_trailer_text']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Watching Full Video Text<BR><i>leave blank for no message</i></th>
                                                <td>
                                                        <input type="text" size="50" name="hss_woo_options[watching_video_text]" value="<?php echo $options['watching_video_text']; ?>" />
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Enable video sharing links for access to purchased videos by URL</th>
                                                <td>
                                                        <input type="checkbox" name="hss_woo_options[use_non_loggedin_video_links]" value="1" <?php checked($options['use_non_loggedin_video_links'], 1); ?> />
                                                </td>
                                        </tr>


                                        <tr>
                                                <th scope="row">Product Type</th>
                                                <td>
                                                        <select name="hss_woo_options[product_virtual]">
                                                                <?php
                                                                if ($options['product_virtual'] == "virtual" or $options['product_virtual'] == "") {
                                                                ?><option value="virtual" SELECTED>virtual</option><?php
                                                                                                                        ?><option value="physical">physical</option><?php
                                                                                                                                                                } elseif ($options['product_virtual'] == "physical") {
                                                                                                                                                                        ?><option value="physical" SELECTED>physical</option><?php
                                                                                                                                                                                                                                ?><option value="virtual">virtual</option><?php
                                                                                                                                                                                                                                                                        } ?>
                                                        </select>
                                                </td>
                                        </tr>

                                        <tr>
                                                <th scope="row">Disable Fast Forward<BR><i>Videojs only - only allow fast forwarding to sections of the video already watched (progress resets on page reload)</i></th>
                                                <td>
                                                        <select name="hss_woo_options[disable_player_fast_forward]">
                                                                <?php
                                                                if ($options['disable_player_fast_forward'] == "false" or $options['disable_player_fast_forward'] == "") {
                                                                ?><option value="false" SELECTED>false</option><?php
                                                                                                                ?><option value="true">true</option><?php
                                                                                                                                                } elseif ($options['disable_player_fast_forward'] == "true") {
                                                                                                                                                        ?><option value="true" SELECTED>true</option><?php
                                                                                                                                                                                                        ?><option value="false">false</option><?php
                                                                                                                                                                                                                                        } ?>
                                                        </select>
                                                </td>
                                        </tr>

                                        <tr>
                                                <th scope="row">Add/Update All Videos<BR><i>Adds new videos setup on your HSS account as well as updates all existing videos to match their config on your HSS account</i></th>
                                                <td>
                                                        <div><input type="button" value="Add/Update" id="myajax" /></div>
                                                        <div id="updateprogress"></div>
                                                </td>
                                        </tr>
                                        <tr>
                                                <th scope="row">Add Only New Videos<BR><i>Only adds new videos setup on your HSS account and will not update existing videos</i></th>
                                                <td>
                                                        <div><input type="button" value="Add" id="hss_ajax_update_new" /></div>
                                                        <div id="hss_new_updateprogress"></div>
                                                </td>
                                        </tr>


                                        <tr>
                                                <th scope="row">Log Video Player Events</th>
                                                <td>
                                                        <select name="hss_woo_options[log_player_events]">
                                                                <?php
                                                                if ($options['log_player_events'] == "false" or $options['log_player_events'] == "") {
                                                                ?><option value="false" SELECTED>false</option><?php
                                                                                                                ?><option value="true">true</option><?php
                                                                                                                                                } elseif ($options['log_player_events'] == "true") {
                                                                                                                                                        ?><option value="true" SELECTED>true</option><?php
                                                                                                                                                                                                        ?><option value="false">false</option><?php
                                                                                                                                                                                                                                        } ?>
                                                        </select>
                                                </td>
                                        </tr>


                                </table>
                                <p class="submit">
                                        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                                </p>
                        </form>
                <?php } ?>
        </div>
        <?php
        }


        function hss_menu()
        {
                add_options_page('HostStreamSell Admin', 'HSS WOO Admin', 'manage_options', 'hss_admin', 'hss_woo_options_page');
        }

        add_action('admin_menu', 'hss_menu');

        function hss_woo_before_download_content($download_id, $custom = False)
        {
                if ($custom == True && !is_array($custom))
                        $post = get_post($download_id);
                else
                        global $post;
                global $is_iphone;
                global $user_ID;
                $video = "";

                if ($post->post_type == 'product' && is_singular() && is_main_query()) {

                        if (get_post_meta($post->ID, 'is_streaming_video', true)) {
                                $guestcheckout = get_option("woocommerce_enable_guest_checkout", "notfound");
                                if ($guestcheckout == "yes")
                                        $video .= "<BR><CENTER><B>WARNING - guest checkout is enabled. Please do not puchase without registering as you will not get access to the video</B></CENTER><BR>";

                                $options = get_option('hss_woo_options');
                                $userId = $user_ID;

                                if (isset($_GET['videolink'])) {
                                        global $wpdb;
                                        $videolink = $_GET['videolink'];
                                        $sql = "
					SELECT order_id FROM {$wpdb->prefix}woocommerce_order_itemmeta oim 
					LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi 
					ON oim.order_item_id = oi.order_item_id 
					WHERE meta_key = '_hss_woo_link' AND meta_value = '%s'
					GROUP BY order_id;
					";
                                        $order_id = $wpdb->get_col($wpdb->prepare($sql, $videolink));
                                        if ($order_id) {
                                                $order = new WC_Order($order_id[0]);
                                                $userId = $order->user_id;
                                        }
                                }

                                if ($userId != 0) {
                                        $hss_errors = get_user_meta($userId, "hss_errors", true);
                                        if (!empty($hss_errors)) {
                                                _hss_woo_log("there are hss_errors");
                                                _hss_woo_log($hss_errors);
                                                foreach ($hss_errors as $key => $ppv_option) {
                                                        $params = array(
                                                                'method' => 'secure_videos.add_user_ppv',
                                                                'api_key' => $options['api_key'],
                                                                'ppv_id' => $ppv_option,
                                                                'private_user_id' => $userId,
                                                                'database_id' => $options['database_id']
                                                        );
                                                        _hss_woo_log($params);
                                                        $response = wp_remote_post(
                                                                "https://www.hoststreamsell.com/services/api/rest/xml/",
                                                                array(
                                                                        'method' => 'POST',
                                                                        'timeout' => 15,
                                                                        'redirection' => 5,
                                                                        'httpversion' => '1.0',
                                                                        'blocking' => true,
                                                                        'headers' => array(),
                                                                        'body' => $params,
                                                                        'cookies' => array()
                                                                )
                                                        );

                                                        // need to add method to record failed rest requests for retry

                                                        if (is_wp_error($response)) {
                                                                _hss_woo_log("error msg: " . $response->get_error_message() . "\n");
                                                        } else if ($response['response']['code'] != "200") {
                                                                _hss_woo_log("request code bad: " . $response['response']['code'] . "\n");
                                                        } else {
                                                                _hss_woo_log("request code good: " . $key . "=>" . $ppv_option . " " . $response['response']['code'] . "\n");
                                                                unset($hss_errors[$key]);
                                                                update_user_meta($userId, "hss_errors", $hss_errors);
                                                                $hss_errors_new = get_user_meta($userId, "hss_errors", true);
                                                                _hss_woo_log($hss_errors_new);
                                                        }
                                                }
                                        }
                                }

                                $hss_video_id = get_post_meta($post->ID, '_woo_video_id', true);
                                $response = wp_remote_post(
                                        "https://www.hoststreamsell.com/api/1/xml/videos?api_key=" . $options['api_key'] . "&video_id=$hss_video_id&private_user_id=$userId&database_id=" . $options['database_id'] . "&expands=playback_details&force_allow=no&limit=1000&offset=0&clientip=" . hss_woo_get_the_user_ip(),
                                        array(
                                                'method' => 'GET',
                                                'timeout' => 15,
                                                'redirection' => 5,
                                                'httpversion' => '1.0',
                                                'blocking' => true,
                                                'headers' => array(),
                                                //'body' => $params,
                                                'cookies' => array()
                                        )
                                );
                                $res = "";
                                if (is_wp_error($response)) {
                                        $return_string .= 'Error occured retieving video information, please try refresh the page';
                                } else {
                                        $res = $response['body'];
                                }

                                $xml = new SimpleXMLElement($res);
                                _hss_woo_log($xml);
                                //$title = $xml->result->title;
                                $title = htmlspecialchars($xml->result->title, ENT_QUOTES);

                                $hss_video_title = $title;
                                $user_has_access = $xml->result->user_has_access;
                                $user_can_download = $xml->result->user_can_download;
                                //$video = "".$user_has_access;
                                if ($user_has_access == "true")
                                        $video .= '<div class="hss_woo_watching_video_text">' . $options['watching_video_text'] . '</div>';
                                else
                                        $video .= '<div class="hss_woo_watching_trailer_text">' . $options['watching_trailer_text'] . '</div>';
                                $description = $xml->result->description;
                                $feature_duration = $xml->result->feature_duration;
                                $trailer_duration = $xml->result->trailer_duration;
                                $video_width = $xml->result->width;
                                $video_height = $xml->result->height;
                                $aspect_ratio = $xml->result->aspect_ratio;
                                if ($video_width > 640) {
                                        $video_width = "640";
                                        $video_height = "370";
                                }
                                $hss_video_user_token = $xml->result->user_token;

                                $suid = $xml->result->suid;

                                $hss_video_mediaserver_ip = $xml->result->wowza_ip;
                                $hss_video_mediaserver_url = $xml->result->wowza_url;

                                $hss_video_smil_token = "?privatetoken=" . $hss_video_user_token;

                                $hss_video_m3u8_token = "";
                                $hss_video_mediaserver_ip = $xml->result->wowza_ip;

                                $hss_video_smil = $xml->result->smil;
                                $hss_video_m3u8 = $xml->result->m3u8;
                                $hss_video_big_thumb_url = $xml->result->big_thumb_url;
                                $hss_rtsp_url = $xml->result->rtsp_url;
                                $referrer = site_url();
                                $playersessionid = substr(md5(rand()), 0, 10);

                                $content_width = $video_width;
                                $content_height = $video_height;


                                if ($is_iphone) {
                                        if ($content_width < 320) {
                                                $content_width = 320;
                                        }
                                }

                                if ($video_width > $content_width) {
                                        $mod = $content_width % 40;
                                        $video_width = $content_width - $mod;
                                        $multiple = $video_width / 40;
                                        $video_height = $multiple * 30;
                                }

                                if ($is_iphone) {
                                        if ($options['player_width_mobile'] != "")
                                                $video_width = $options['player_width_mobile'];
                                        if ($options['player_height_mobile'] != "")
                                                $video_height = $options['player_height_mobile'];
                                } else {
                                        if ($options['player_width_default'] != "")
                                                $video_width = $options['player_width_default'];
                                        if ($options['player_height_default'] != "")
                                                $video_height = $options['player_height_default'];
                                }
                                $httpString = "http";
                                if (is_ssl()) {
                                        $httpString = "https";
                                        $hss_video_big_thumb_url = str_replace("http", "https", $hss_video_big_thumb_url);
                                }

                                $stream_url = "$httpString://$hss_video_mediaserver_url/hss/smil:" . $hss_video_smil . "/playlist.m3u8" . $hss_video_smil_token . "&referer=" . urlencode($referrer . ":playersessionid:" . $playersessionid);

                                if ($hss_video_m3u8 != "") {
                                        $stream_url = $hss_video_m3u8 . "" . $hss_video_m3u8_token . "?referer=" . urlencode($referrer . ":playersessionid:" . $playersessionid);
                                }

                                $subtitle_count = $xml->result->subtitle_count;
                                $subtitle_index = 1;
                                $subtitle_text = "";
                                $default_language = "English";
                                $captions = "";
                                if ($subtitle_count > 0) {
                                        $subtitle_text = ",
						tracks: [{";
                                        while ($subtitle_index <= $subtitle_count) {
                                                $subtitle_label = (string)$xml->result[0]->subtitles->{'subtitle_label_' . $subtitle_index}[0];
                                                $subtitle_file = (string)$xml->result[0]->subtitles->{'subtitle_file_' . $subtitle_index}[0];
                                                $subtitle_text .= "
					            file: \"$httpString://www.hoststreamsell.com/mod/secure_videos/subtitles/$subtitle_file?rand=" . hss_woo_randomString() . "\",
					            label: \"$subtitle_label\",
					            kind: \"captions\",
					            \"default\": true";
                                                $subtitle_index += 1;
                                                if ($subtitle_index <= $subtitle_count) {
                                                        $subtitle_text .= "
                                                },{";
                                                }
                                        }
                                        $subtitle_text .= "
						}]";
                                        $fontSize = "";
                                        if ($options["subtitle_font_size"] != "") {
                                                $fontSize = "
                                                        fontSize: " . $options["subtitle_font_size"] . ",";
                                        }
                                        $captions = "
                                                captions: {
                                                        color: '#FFFFFF'," . $fontSize . "
                                                        backgroundOpacity: 0
                                                },";
                                }


                                if ($options['jwplayer_version'] == "6" or $options['jwplayer_version'] == "7") {
                                        $video .= "
                                <script type=\"text/javascript\" src=\"";
                                        if ($options['jwplayer_version'] == "6") {
                                                $video .= $httpString . "://www.hoststreamsell.com/mod/secure_videos/jwplayer-6.12/jwplayer/jwplayer.js";
                                        } elseif ($options['jwplayer_version'] == "7") {
                                                $video .= $httpString . "://www.hoststreamsell.com/mod/secure_videos/jwplayer7/jwplayer-7.0.2/jwplayer.js";
                                        } else {
                                                $video .= $httpString . "://www.hoststreamsell.com/mod/secure_videos/jwplayer-6.12/jwplayer/jwplayer.js";
                                        }
                                        $video .= "\"></script>
				<script type=\"text/javascript\">jwplayer.key=\"" . $options['jwplayer_license'] . "\";</script>";
                                        if ($options["responsive_player"] == 1) {
                                                $responsive_width = "640";
                                                if ($options["player_responsive_max_width"] != "")
                                                        $responsive_width = $options["player_responsive_max_width"];
                                                $video .= "<div class='hss_video_player' style='max-width:" . $responsive_width . "px;'>";
                                        } else {
                                                $video .= "<div class='hss_video_player'>";
                                        }
                                        $video .= "<div id='videoframe'>An error occurred setting up the video player</div>
                		<SCRIPT type=\"text/javascript\">

		                var viewTrailer = false;
                		var videoFiles = new Array();;
		                var trailerFiles = new Array();;

                		var agent=navigator.userAgent.toLowerCase();
		                var is_iphone = (agent.indexOf('iphone')!=-1);
		                var is_ipad = (agent.indexOf('ipad')!=-1);
		                var is_playstation = (agent.indexOf('playstation')!=-1);
		                var is_safari = (agent.indexOf('safari')!=-1);
		                var is_iemobile = (agent.indexOf('iemobile')!=-1);
		                var is_blackberry = (agent.indexOf('BlackBerry')!=-1);
				var is_silk = (agent.indexOf('silk')!=-1);
		                var is_android = (agent.indexOf('android')!=-1);
		                var is_webos = (agent.indexOf('webos')!=-1);
					
				if (is_iphone) { html5Player();}
                                else if (is_ipad) { html5Player(); }
				else if (is_silk) { newJWPlayer(); }
				else if (is_android) { rtspPlayer(); }
				else if (is_webos) { rtspPlayer(); }
				else if (is_blackberry) { rtspPlayer(); }
				else if (is_playstation) { newJWPlayer(); }
				else { newJWPlayer(); }
		
		                function newJWPlayer()
		                {
					jwplayer('videoframe').setup({
					    playlist: [{
					        image: '$hss_video_big_thumb_url',
				        	sources: [{
				        	    file: '" . $stream_url . "'
					        }]$subtitle_text
					    }],$captions
					 rtmp: {
                                                bufferlength: 10,
                                                proxytype: 'best'
                                        },
                ";
                                        if ($options['jwplayer_version'] == "7" and $options['jwplayer_logo_file'] != "") {
                                                $video .= "                       logo: {
                                file: '" . $options['jwplayer_logo_file'] . "',";
                                                if ($options['jwplayer_logo_link'] != "") {
                                                        $video .= "
                                link: '" . $options['jwplayer_logo_link'] . "',";
                                                }
                                                if ($options['jwplayer_logo_hide'] == "true") {
                                                        $video .= "
                                hide: '" . $options['jwplayer_logo_hide'] . "',";
                                                }
                                                $video .= "
        },
                ";
                                        }
                                        $video .= "
                                primary: 'flash',   ";
                                        if ($options["responsive_player"] == 1) {
                                                $video .= "                  width: '100%',
                                            aspectratio: '" . $aspect_ratio . "'";
                                        } else {
                                                $video .= "                 height: $video_height,
                                          width: $video_width";
                                        }

                                        $video .= "			
				   });
		                }

				function rtspPlayer()
				{
			                var player=document.getElementById(\"videoframe\");
					player.innerHTML='<A HREF=\"rtsp://" . $hss_video_mediaserver_ip . "/hss/mp4:" . $hss_rtsp_url . "" . $hss_video_smil_token . "&referer=" . urlencode($referrer) . "\">'+
					'<IMG SRC=\"" . $hss_video_big_thumb_url . "\" '+
					'ALT=\"Start Mobile Video\" '+
					'BORDER=\"0\" '+
					'HEIGHT=\"$video_height\"'+
					'WIDTH=\"$video_width\">'+
					'</A>';
				}

                                function html5Player()
                                {
                                        var player=document.getElementById(\"videoframe\");
                                        player.innerHTML='<video controls '+
                                        'src=\"http://" . $hss_video_mediaserver_ip . ":1935/hss/smil:" . $hss_video_smil . "/playlist.m3u8" . $hss_video_smil_token . "&referer=" . urlencode($referrer) . "\" '+
                                        'HEIGHT=\"" . $video_height . "\" '+
                                        'WIDTH=\"" . $video_width . "\" '+
                                        'poster=\"" . $hss_video_big_thumb_url . "\" '+
                                        'title=\"" . $hss_video_title . "\">'+
                                        '</video>';
                                }
			        </script>
	</div>
				";
                                } elseif ($options['jwplayer_version'] == "7Prem" or $options['jwplayer_version'] == "8") {


                                        if ($options['jwplayer_version'] == "7Prem") {
                                                $video .= "
                                        <script type=\"text/javascript\" src=\"https://www.hoststreamsell.com/mod/secure_videos/jwplayer-7.10.7/jwplayer.js\"></script>
                                        <script type=\"text/javascript\">jwplayer.key=\"" . $options['jwplayer_license'] . "\";</script>";
                                        } elseif ($options['jwplayer_version'] == "8") {
                                                $video .= "
                                        <script type=\"text/javascript\" src=\"https://www.hoststreamsell.com/mod/secure_videos/jwplayer-8.1.8/jwplayer.js\"></script>
                                        <script type=\"text/javascript\">jwplayer.key=\"" . $options['jwplayer_license'] . "\";</script>";
                                        }

                                        if ($options["responsive_player"] == 1) {
                                                $responsive_width = "640";
                                                if ($options["player_responsive_max_width"] != "")
                                                        $responsive_width = $options["player_responsive_max_width"];
                                                $video .= "<div class='hss_video_player' style='max-width:" . $responsive_width . "px; width:100%;'>";
                                        } else {
                                                $video .= "<div class='hss_video_player'>";
                                        }
                                        if ($options['jwplayer_version'] == "7Prem") {
                                                $video .= "<div id='videoframe'>An error occurred setting up the video player</div>
                                <SCRIPT type=\"text/javascript\">

                                newJWPlayer();

                                function newJWPlayer()
                                {
                                        jwplayer('videoframe').setup({
                                            playlist: [{
                                                image: '$hss_video_big_thumb_url',
                                                sources: [{
                                                        file: '" . $stream_url . "',
                                                }]$subtitle_text
                                            }],$captions
                                            dash: 'shaka',";
                                        } elseif ($options['jwplayer_version'] == "8") {
                                                $video .= "<div id='videoframe'>An error occurred setting up the video player</div>
                                <SCRIPT type=\"text/javascript\">

                                newJWPlayer();

                                function newJWPlayer()
                                {
                                        jwplayer('videoframe').setup({
                                            playlist: [{
                                                image: '$hss_video_big_thumb_url',
                                                sources: [{
                                                        file: '" . $stream_url . "'
                                                }]$subtitle_text
                                            }],$captions
                                            ";
                                        }

                                        if (($options['jwplayer_version'] == "7" or $options['jwplayer_version'] == "7Prem" or $options['jwplayer_version'] == "8") and $options['jwplayer_logo_file'] != "") {
                                                $video .= "                       logo: {
                                file: '" . $options['jwplayer_logo_file'] . "',";
                                                if ($options['jwplayer_logo_link'] != "") {
                                                        $video .= "
                                link: '" . $options['jwplayer_logo_link'] . "',";
                                                }
                                                if ($options['jwplayer_logo_hide'] == "true") {
                                                        $video .= "
                                hide: '" . $options['jwplayer_logo_hide'] . "',";
                                                }
                                                $video .= "
        },
                ";
                                        }
                                        $video .= "
        cast:{},
                ";

                                        if ($options["responsive_player"] == 1) {
                                                $video .= "                  width: '100%',
                                            aspectratio: '" . $aspect_ratio . "'";
                                        } else {
                                                $video .= "                 height: $video_height,
                                          width: $video_width";
                                        }
                                        $video .= "
                                        });
                                }

			        </script>
	</div>

                                ";
                                } elseif ($options['jwplayer_version'] == "videojs5") {

                                        $responsiveText = "";

                                        if ($options["responsive_player"] == 1) {
                                                $responsive_width = "640";
                                                if ($options["player_responsive_max_width"] != "")
                                                        $responsive_width = $options["player_responsive_max_width"];
                                                $video .= "<div class='hss_video_player' style='max-width:" . $responsive_width . "px; width:100%;'>";
                                                //$responsiveText="vjs-16-9";
                                                $responsiveText = "vjs-fluid";
                                                //if($aspect_ratio=="4:3")
                                                //	$responsiveText="vjs-4-3";
                                        } else {
                                                $video .= "<div class='hss_video_player'>";
                                        }


                                        if ($options["disable_player_fast_forward"] == "true") {
                                                $video .= "


 <link href=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/new/video-js.6.11.0.css\" rel=\"stylesheet\">
  <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/new/video.6.11.0.min.js\"></script>
    <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/new/videojs-flash.2.1.1.min.js\"></script>
    <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/new/videojs-contrib-hls.5.14.1.min.js\"></script>
    <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/new/videojs-contrib-quality-levels.2.0.3.min.js\"></script>

  <video poster=\"" . $hss_video_big_thumb_url . "\" id=\"my_video_1\" class=\"video-js vjs-default-skin " . $responsiveText . " vjs-big-play-centered\" controls preload=\"auto\" width=\"" . $video_width . "\" height=\"" . $video_height . "\"
   crossorigin=\"anonymous\">
  <source src=\"" . $stream_url . "\" type='application/x-mpegURL'>";
                                        } else {
                                                $video .= "
 <link href=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/video-js.css\" rel=\"stylesheet\">
  <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/video.v5.19.2.js\"></script>
    <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/hls.min.js\"></script>
    <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/vjs-hls.min.js\"></script>
    ";
                if(get_post_meta($post->ID, '_woo_panorama_video', true) == 'on'){
                        $video .= "
                        <script src=\"https://cdnjs.cloudflare.com/ajax/libs/three.js/r76/three.js\"></script>
        <script src=\"https://rawgit.com/yanwsh/videojs-panorama/master/dist/videojs-panorama.v5.js\"></script>
                        ";
                }
                                                $video .= "
  <video poster=\"" . $hss_video_big_thumb_url . "\" id=\"my_video_1\" class=\"video-js vjs-default-skin " . $responsiveText . " vjs-big-play-centered\" controls preload=\"auto\" width=\"" . $video_width . "\" height=\"" . $video_height . "\"
  data-setup='{}' crossorigin=\"anonymous\">
  <source src=\"" . $stream_url . "\" type='application/x-mpegURL'>";
                                        }
                                        $subtitle_count = $xml->result->subtitle_count;
                                        $subtitle_index = 1;
                                        $subtitle_text = "";
                                        $captions = "";
                                        if ($subtitle_count > 0) {
                                                while ($subtitle_index <= $subtitle_count) {
                                                        $subtitle_label = (string)$xml->result[0]->subtitles->{'subtitle_label_' . $subtitle_index}[0];
                                                        $subtitle_file = (string)$xml->result[0]->subtitles->{'subtitle_file_' . $subtitle_index}[0];
                                                        $subtitle_index += 1;
                                                        if (hss_woo_endsWith($subtitle_file, "vtt")) {
                                                                $video .= "
<track kind=\"captions\" src=\"$httpString://www.hoststreamsell.com/mod/secure_videos/subtitles/" . $subtitle_file . "?rand=" . hss_woo_randomString() . "\" srclang=\"en\" label=\"" . $subtitle_label . "\">";
                                                        }
                                                }
                                        }


                                        if ($options["disable_player_fast_forward"] == "true") {
                                                $video .= "
</video>

<script>
                                var player = videojs('my_video_1', {techOrder: ['html5', 'flash']});
                                if (typeof player.panorama !== 'undefined') {

                                        player.panorama({
                                                clickAndDrag: true,
                                                clickToToggle: true,
                                                autoMobileOrientation: true,
                                                backToVerticalCenter: false,
                                                backToHorizonCenter: false
                                              }); 
        
                                        }
  </script>


<script type=\"text/javascript\">
        var max = 0;
  var disableForwardScrubbing = function(player) {
    return {
      setSource: function setSource(srcObj, next) {
        next(null, srcObj);
      },
      setCurrentTime: function setCurrentTime(ct) {
	if(player.currentTime()>max){
		max = player.currentTime();
	}
        if ( ct < max ) {
          return ct;
        }
        return player.currentTime();
      }
    }
  };

  // Register the middleware with the player
  videojs.use('*', disableForwardScrubbing);

</script>
";
                                        } else {
                                                $video .= "
</video>
                                <script>
                                var options = {
                                        html5: {
                                            hlsjsConfig: {
                                                debug: true
                                            }
                                        }
                                };
                                function isIE()
                                {
                                            var isIE11 = navigator.userAgent.indexOf(\".NET CLR\") > -1;
                                                var isIE11orLess = isIE11 || navigator.appVersion.indexOf(\"MSIE\") != -1;
                                                return isIE11orLess;
                                }

                                container = document.getElementById('my_video_1');
                                if(isIE()){
                                        player = videojs('my_video_1', {techOrder: ['flash']});
                                }else{
                                        player = videojs('my_video_1');
                                }

                                if (typeof player.panorama !== 'undefined') {

                                        player.panorama({
                                                clickAndDrag: true,
                                                clickToToggle: true,
                                                autoMobileOrientation: true,
                                                backToVerticalCenter: false,
                                                backToHorizonCenter: false
                                              }); 
        
                                        }

                                player.qualityPickerPlugin();
  </script>
";
                                        }

                                        $video .= "
	</div>
";
                                } elseif ($options['jwplayer_version'] == "videojs7") {

                                        $responsiveText = "";

                                        if ($options["responsive_player"] == 1) {
                                                $responsive_width = "640";
                                                if ($options["player_responsive_max_width"] != "")
                                                        $responsive_width = $options["player_responsive_max_width"];
                                                $video .= "<div class='hss_video_player' style='max-width:" . $responsive_width . "px; width:100%;'>";
                                                //$responsiveText="vjs-16-9";
                                                $responsiveText = "vjs-fluid";
                                                //if($aspect_ratio=="4:3")
                                                //      $responsiveText="vjs-4-3";
                                        } else {
                                                $video .= "<div class='hss_video_player'>";
                                        }

                                        $video .= "

 <link href=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/7/video-js.css\" rel=\"stylesheet\">
  <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/7/video.js\"></script>
<script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/new/videojs-flash.2.1.1.min.js\"></script>
    <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/videojs-contrib-quality-levels.2.0.3.js\"></script>
    <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/7/videojs-hls-quality-selector.min.js\"></script>
 <script src=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/7/silvermine-videojs-chromecast.min.js\"></script>
<link href=\"$httpString://hoststreamsell.com/mod/secure_videos/videojs/7/silvermine-videojs-chromecast.css\" rel=\"stylesheet\">
        <script type=\"text/javascript\" src=\"https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1\"></script>
        ";
        if(get_post_meta($post->ID, '_woo_panorama_video', true) == 'on'){
                $video .= "
                <script src=\"https://cdnjs.cloudflare.com/ajax/libs/three.js/r76/three.js\"></script>
<script src=\"https://rawgit.com/yanwsh/videojs-panorama/master/dist/videojs-panorama.v5.js\"></script>
                ";
        }
        $video .= "
  <video poster=\"" . $hss_video_big_thumb_url . "\" id=\"my_video_1\" class=\"video-js vjs-default-skin " . $responsiveText . " vjs-big-play-centered\" controls preload=\"auto\" width=\"" . $video_width . "\" height=\"" . $video_height . "\"
  data-setup='{}' crossorigin=\"anonymous\">
";

                                        $subtitle_count = $xml->result->subtitle_count;
                                        $subtitle_index = 1;
                                        $subtitle_text = "";
                                        $captions = "";
                                        if ($subtitle_count > 0) {
                                                while ($subtitle_index <= $subtitle_count) {
                                                        $subtitle_label = (string)$xml->result[0]->subtitles->{'subtitle_label_' . $subtitle_index}[0];
                                                        $subtitle_file = (string)$xml->result[0]->subtitles->{'subtitle_file_' . $subtitle_index}[0];
                                                        $subtitle_index += 1;
                                                        if (hss_woo_endsWith($subtitle_file, "vtt")) {
                                                                $video .= "
<track kind=\"captions\" src=\"$httpString://www.hoststreamsell.com/mod/secure_videos/subtitles/" . $subtitle_file . "?rand=" . hss_woo_randomString() . "\" srclang=\"en\" label=\"" . $subtitle_label . "\">";
                                                        }
                                                }
                                        }


                                        if ($options["disable_player_fast_forward"] == "true") {
                                                $video .= "
</video>

<script>
				function isIE()
                                {
                                            var isIE11 = navigator.userAgent.indexOf(\".NET CLR\") > -1;
                                                var isIE11orLess = isIE11 || navigator.appVersion.indexOf(\"MSIE\") != -1;
                                                return isIE11orLess;
                                }

                                if(isIE()){
                                        player = videojs('my_video_1', { techOrder: ['flash'], });
                                }else{
                                        player = videojs('my_video_1', { controls: true, techOrder: ['chromecast', 'html5'],  plugins: { chromecast: {preloadWebComponents: true, addButtonToControlBar: true}  },
                                        html5: {
                                                hls: {
                                                  overrideNative: !videojs.browser.IS_ANY_SAFARI
                                                }
                                              }
                                        }
                                        );

                                }
                                player.src({
                                        src: \"" . $stream_url . "\",
                                        type: 'application/x-mpegURL',
                                });
                                player.hlsQualitySelector();
				player.chromecast();

                                if (typeof player.panorama !== 'undefined') {

                                player.panorama({
                                        clickAndDrag: true,
                                        clickToToggle: true,
                                        autoMobileOrientation: true,
                                        backToVerticalCenter: false,
                                        backToHorizonCenter: false
                                      }); 

                                }

        var max = 0;
  var disableForwardScrubbing = function(player) {
    return {
      setSource: function setSource(srcObj, next) {
        next(null, srcObj);
      },
      setCurrentTime: function setCurrentTime(ct) {
        if(player.currentTime()>max){
                max = player.currentTime();
        }
        if ( ct < max ) {
          return ct;
        }
        return player.currentTime();
      }
    }
  };

  // Register the middleware with the player
  videojs.use('*', disableForwardScrubbing);

</script>
";
                                        } else {


                                                $video .= "
</video>
                                <script>

                                function isIE()
                                {
                                            var isIE11 = navigator.userAgent.indexOf(\".NET CLR\") > -1;
                                                var isIE11orLess = isIE11 || navigator.appVersion.indexOf(\"MSIE\") != -1;
                                                return isIE11orLess;
                                }

                                if(isIE()){
                                        player = videojs('my_video_1', { techOrder: ['flash'] });
                                }else{
                                        player = videojs('my_video_1', { controls: true, techOrder: ['chromecast', 'html5'],  plugins: { chromecast: {preloadWebComponents: true, addButtonToControlBar: true}  },
                                        html5: {
                                                hls: {
                                                  overrideNative: !videojs.browser.IS_ANY_SAFARI
                                                }
                                              }
                                        }
                                        );

                                }
                                player.src({
                                        src: \"" . $stream_url . "\",
                                        type: 'application/x-mpegURL',
                                });
                                player.hlsQualitySelector();
				player.chromecast();
                                if(typeof player.panorama !== 'undefined'){
                                player.panorama({
                                        clickAndDrag: true,
                                        clickToToggle: true,
                                        autoMobileOrientation: true,
                                        backToVerticalCenter: false,
                                        backToHorizonCenter: false
                                      }); 
                                }

                                

 </script>
";
                                        }

                                        $video .= "
        </div>
";
                                }

                                if ($options['log_player_events'] == "true") {

                                        if ($options['jwplayer_version'] == "7" or $options['jwplayer_version'] == "7Prem" or $options['jwplayer_version'] == "8") {

                                                $video .= "

                		<SCRIPT type=\"text/javascript\">
				var agent=navigator.userAgent.toLowerCase();
             var videoreferrer=encodeURI(window.location.href);
jwplayer().on('ready',function(e) {
	jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=ready&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&setupTime='+e.setupTime+'&userid=" . $userId . "&suid=" . $suid . "&file='+jwplayer().getPlaylistItem(0).file,
                        dataType: 'jsonp',
                });
});
jwplayer().on('setupError',function(e) {
	jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=setupError&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&message='+e.message+'&userid=" . $userId . "&suid=" . $suid . "',
                        dataType: 'jsonp',
                });
});
jwplayer().on('error',function(e) {
	jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=error&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&message='+e.message,
                        dataType: 'jsonp',
                });
	jwplayer().load(jwplayer().getPlaylist());
});
jwplayer().on('buffer',function(e) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=buffer&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&oldstate='+e.oldstate+'&newstate='+e.newstate+'&reason='+e.reason,
                        dataType: 'jsonp',
                });
});
jwplayer().on('play',function(e) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=play&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&oldstate='+e.oldstate,
                        dataType: 'jsonp',
                });
});
jwplayer().on('pause',function(e) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=pause&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&oldstate='+e.oldstate,
                        dataType: 'jsonp',
                });
});
jwplayer().on('seek',function(e) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=seek&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&position='+e.position+'&offset='+e.offset,
                        dataType: 'jsonp',
                });
});
jwplayer().on('idle',function(e) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=idle&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&oldstate='+e.oldstate,
                        dataType: 'jsonp',
                });
});
jwplayer().on('complete',function(e) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=complete&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "',
                        dataType: 'jsonp',
                });
});
jwplayer().on('firstFrame',function(e) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=firstFrame&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&loadTime='+e.loadTime,
                        dataType: 'jsonp',
                });
});
jwplayer().on('levelsChanged',function(e) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=levelsChanged&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&currentQuality='+e.currentQuality,
                        dataType: 'jsonp',
                });
});
jwplayer().on('fullscreen',function(e) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=fullscreen&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&fullscreen='+e.fullscreen,
                        dataType: 'jsonp',
                });
});
jwplayer().on('resize',function(e) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=resize&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&width='+e.width+'&height='+e.height,
                        dataType: 'jsonp',
                });
});
  </script>
";
                                        } elseif ($options['jwplayer_version'] == "6") {

                                                $video .= "

                		<SCRIPT type=\"text/javascript\">
                                var agent=navigator.userAgent.toLowerCase();

jwplayer().onReady(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=ready&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&file='+jwplayer().getPlaylistItem(0).file,
                        dataType: 'jsonp',
                });
});
jwplayer().onSetupError(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=setupError&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&message='+event.message,
                        dataType: 'jsonp',
                });
});
jwplayer().onError(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=error&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&message='+event.message,
                        dataType: 'jsonp',
                });
});
jwplayer().onBuffer(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=buffer&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&oldstate='+event.oldstate,
                        dataType: 'jsonp',
                });
});
jwplayer().onPlay(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=play&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&oldstate='+event.oldstate,
                        dataType: 'jsonp',
                });
});
jwplayer().onPause(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=pause&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&oldstate='+event.oldstate,
                        dataType: 'jsonp',
                });
});
jwplayer().onSeek(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=seek&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&position='+event.position+'&offset='+event.offset,
                        dataType: 'jsonp',
                });
});
jwplayer().onIdle(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=idle&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&oldstate='+event.oldstate,
                        dataType: 'jsonp',
                });
});
jwplayer().onComplete(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=complete&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "',
                        dataType: 'jsonp',
                });
});
jwplayer().onFullscreen(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=fullscreen&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&fullscreen='+event.fullscreen,
                        dataType: 'jsonp',
                });
});
jwplayer().onResize(function(event) {
        jQuery.ajax({
                        url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=jw6&videoid=" . $hss_video_id . "&event=resize&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&width='+event.width+'&height='+event.height,
                        dataType: 'jsonp',
                });
});
  </script>


    ";
                                        } elseif ($options['jwplayer_version'] == "videojs5" or $options['jwplayer_version'] == "videojs7") {

                                                $subtitle_language = "";
                                                if (isset($options['user_settings']))
                                                        if (isset($options['user_settings'][$user_ID]))
                                                                $subtitle_language = $options['user_settings'][$user_ID]["subtitle_language"];

                                                $audio_language = "";
                                                if (isset($options['user_settings']))
                                                        if (isset($options['user_settings'][$user_ID]))
                                                                $audio_language = $options['user_settings'][$user_ID]["audio_language"];


                                                $video .= "

                                <SCRIPT type=\"text/javascript\">

                                var agent=navigator.userAgent.toLowerCase();
             var videoreferrer=encodeURI(window.location.href);
                var video = videojs('my_video_1').ready(function(){
                var player = this;


		player.textTracks().on('change', function action(event) {

let tracks = player.textTracks();

for (let i = 0; i < tracks.length; i++) {
  let track = tracks[i];

  if (track.kind === 'captions' && track.mode === 'showing') {
    console.log('subtitle changed '+track.label);

        var data = {
            action: 'hss_woo_store_user_setting',
	    setting_name: 'subtitle_language',
	    setting_value: track.label,
        };

        jQuery.post(ajaxurl, data);

  }
}
                });

                player.audioTracks().on('change', function action(event) {

let audioTracks = player.audioTracks();

for (let i = 0; i < audioTracks.length; i++) {
  let track = audioTracks[i];

  if (track.enabled) {
    console.log('audio changed '+track.label);

        var data = {
            action: 'hss_woo_store_user_setting',
            setting_name: 'audio_language',
            setting_value: track.label,
        };

        jQuery.post(ajaxurl, data);

  }
}



		});




                 player.on('loadedmetadata', function() {
                        jQuery.ajax({
                                dataType: 'jsonp',
                                url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=loadedmetadata&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&file='+player.currentSrc(),
                        });

	";

                                                if ($subtitle_language != "") {
                                                        $video .= "

let tracks = player.textTracks();

for (let i = 0; i < tracks.length; i++) {
  let track = tracks[i];
  if (track.kind === 'captions' && track.label === '" . $subtitle_language . "') {
    track.mode = 'showing';
        console.log('auto setting subtitles to '+track.label);
  }
}
	";
                                                }

                                                if ($audio_language != "") {
                                                        $video .= "

let audioTracks = player.audioTracks();

for (let i = 0; i < audioTracks.length; i++) {
  let track = audioTracks[i];
  if (track.label === '" . $audio_language . "') {
    track.enabled = true;
        console.log('auto setting audio to '+track.label);
  }
}
        ";
                                                }


                                                $video .= "
                  });
                 player.on('play', function() {
                        jQuery.ajax({
                                dataType: 'jsonp',
                                url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=play&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&file='+player.currentSrc(),
                        });
		  });
                 player.on('ended', function() {
                        jQuery.ajax({
                                dataType: 'jsonp',
                                url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=ended&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&file='+player.currentSrc(),
                        });
                  });
                 player.on('pause', function() {
                        jQuery.ajax({
                                dataType: 'jsonp',
                                url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=pause&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&file='+player.currentSrc(),
                        });
                  });
                 player.on('waiting', function() {
                        console.log('Video waiting: ' + player.currentTime());
                        jQuery.ajax({
                                dataType: 'jsonp',
                                url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=waiting&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&file='+player.currentSrc()+'&offset='+player.currentTime().toFixed(2),
                        });
                 });
                 player.on('seeking', function() {
                        console.log('Video seeking: ' + player.currentTime());
                        jQuery.ajax({
                                dataType: 'jsonp',
                                url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=seeking&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&file='+player.currentSrc()+'&offset='+player.currentTime().toFixed(2),
                        });
                 });
                 player.on('seeked', function() {
                        console.log('Video seek ended: ' + player.currentTime());
                  });
                 player.on('error', function(event) {
                        console.log(event);
                        event.stopImmediatePropagation();
                        var error = this.player().error();
                        console.log('error!', error.code, error.type , error.message);
                        jQuery.ajax({
                                dataType: 'jsonp',
                                url: '$httpString://eventsapi.hoststreamsell.com/v1/logevent/ping.gif?player=" . $options['jwplayer_version'] . "&videoid=" . $hss_video_id . "&event=error&referrer='+videoreferrer+'&playersessionid=" . $playersessionid . "&browser='+agent+'&userid=" . $userId . "&suid=" . $suid . "&file='+player.currentSrc()+'&message='+error.message+'&code='+error.code+'&offset='+player.currentTime().toFixed(2),
                        });
                  });



                });

</script>
";
                                        }
                                }

                                if ($user_can_download == "true") {
                                        $video .= "<div class='hss_download_button'><input type='button' id='$hss_video_id' class='myajaxdownloadlinks' value='Get Download Links'></div>
					<div class='hss_download_links' id='download_links_$hss_video_id'></div>";
                                }
                        }
                }
                if ($user_has_access == "true") {
                        echo $video;
                        do_action('hss_woo_show_video_purchased_extra_content', $post->ID);
                } else {
                        echo $video;
                }
        }


        function my_tab($tabs)
        {
                global $post;
                $options = get_option('hss_woo_options');
                if ($options['player_location'] == "" or $options['player_location'] == "product_tabs") {
                        if (get_post_meta($post->ID, 'is_streaming_video', true)) {
                                $my_tab = array('my_tab' =>  array('title' => 'Video', 'priority' => 9, 'callback' => 'hss_woo_before_download_content'));
                                return array_merge($my_tab, $tabs);
                        }
                }
                return $tabs;
        }

        add_filter('woocommerce_product_tabs', 'my_tab');


        $options = get_option('hss_woo_options');
        if ($options['player_location'] == "hook") {
                //add_filter('woocommerce_single_product_image_html', 'hss_woo_before_download_content' );
                // later than 2.6 use below to replace product thumbnail
                add_filter($options['player_location_hook'], 'hss_woo_before_download_content');
                //add_filter('woocommerce_single_product_image_thumbnail_html', 'hss_woo_before_download_content' );
        }

        function woo_complete_purchase_add_video($order_id)
        {

                _hss_woo_log("woo_complete_purchase_add_video");
                // order object (optional but handy)
                $order = new WC_Order($order_id);
                $options = get_option('hss_woo_options');

                if (get_post_meta($order_id, '_hss_woo_processed', true)) {
                        _hss_woo_log("access already added, skipping...");
                } else {
                        if (count($order->get_items()) > 0) {
                                foreach ($order->get_items() as $item_id => $item) {
                                        $product_obj = $order->get_product_from_item($item);
                                        $product = $product_obj->get_post_data();

                                        /*if((get_post_meta($product->ID, '_force_sell_synced_ids', true))){
					_hss_woo_log("_force_sell_synced_ids");
					$forced_sells = get_post_meta($product->ID, '_force_sell_synced_ids', true);
					foreach( $forced_sells as $forced_sell ) {
						_hss_woo_log("_force_sell_synced_id = ".$forced_sell);
						hss_add_video_access(get_post($forced_sell),$order);
					}
				}
                                if((get_post_meta($product->ID, '_force_sell_ids', true))){
                                        _hss_woo_log("_force_sell_ids");
                                        $forced_sells = get_post_meta($product->ID, '_force_sell_ids', true);
                                        foreach( $forced_sells as $forced_sell ) {
                                                _hss_woo_log("_force_sell_id = ".$forced_sell);
                                                hss_add_video_access(get_post($forced_sell),$order);
                                        }
                                }*/

                                        _hss_woo_log("product id = " . $product->ID);
                                        if ((get_post_meta($product->ID, 'is_streaming_video', true)) or (get_post_meta($product->ID, 'is_streaming_video_bundle', true))) {
                                                $userId = $order->user_id;

                                                $ppv_option = null;
                                                //if(empty($download['options']))
                                                $ppv_option = get_post_meta($product->ID, '_woo_ppv_id', true);

                                                if (!empty($item['variation_id']) && 'product_variation' === get_post_type($item['variation_id'])) {
                                                        $var_product = get_post($item['variation_id']);
                                                        _hss_woo_log("variation id = " . $var_product->ID);
                                                        $ppv_option = get_post_meta($var_product->ID, '_woo_ppv_id', true);
                                                }

                                                //else
                                                //	$ppv_option = $download['options']['price_id'];
                                                _hss_woo_log("ppv option = " . $ppv_option);
                                                $params = array(
                                                        'method' => 'secure_videos.add_user_ppv',
                                                        'api_key' => $options['api_key'],
                                                        'ppv_id' => $ppv_option,
                                                        'private_user_id' => $userId,
                                                        'database_id' => $options['database_id']
                                                );
                                                _hss_woo_log($params);
                                                $response = wp_remote_post(
                                                        "https://www.hoststreamsell.com/services/api/rest/xml/",
                                                        array(
                                                                'method' => 'POST',
                                                                'timeout' => 15,
                                                                'redirection' => 5,
                                                                'httpversion' => '1.0',
                                                                'blocking' => true,
                                                                'headers' => array(),
                                                                'body' => $params,
                                                                'cookies' => array()
                                                        )
                                                );


                                                //$video_id = get_post_meta($product->ID, '_woo_video_id', true);
                                                //update_post_meta($item->ID, '_woo_video_id', $video_id);

                                                if ($options['use_non_loggedin_video_links'] == 1) {
                                                        $randString = md5(uniqid(rand(), true));
                                                        wc_add_order_item_meta($item_id, '_hss_woo_link', $randString);
                                                }

                                                if (is_wp_error($response)) {
                                                        _hss_woo_log("error msg: " . $response->get_error_message() . "\n");
                                                        $hss_errors = get_user_meta($userId, "hss_errors", true);
                                                        $hss_errors[] = $ppv_option;
                                                        update_user_meta($userId, "hss_errors", $hss_errors);
                                                } else if ($response['response']['code'] != "200") {
                                                        _hss_woo_log("request code bad: " . $response['response']['code'] . "\n");
                                                        $hss_errors = get_user_meta($userId, "hss_errors", true);
                                                        $hss_errors[] = $ppv_option;
                                                        update_user_meta($userId, "hss_errors", $hss_errors);
                                                } else {
                                                        _hss_woo_log("request code good: " . $response['response']['code'] . "\n");
                                                }

                                                $res = $response['body'];
                                                _hss_woo_log($res);

                                                $xml = new SimpleXMLElement($res);
                                                _hss_woo_log($xml);
                                                $ppv_type_id = (int)$xml->result;
                                                _hss_woo_log($ppv_type_id);
                                                wc_add_order_item_meta($item_id, '_hss_woo_ppv_type_id', $ppv_type_id);
                                        }
                                }
                        }
                }
                update_post_meta($order_id, '_hss_woo_processed', true);
                #return $order_status;
                #return ‘completed’;
        }
        #add_action( 'woocommerce_payment_complete_order_status', 'woo_complete_purchase_add_video', 10, 2 );
        add_action('woocommerce_order_status_completed', 'woo_complete_purchase_add_video');

        function hss_add_video_access($product, $order)
        {
                $options = get_option('hss_woo_options');
                $userId = $order->user_id;

                $ppv_option = null;
                //if(empty($download['options']))
                $ppv_option = get_post_meta($product->ID, '_woo_ppv_id', true);
                //else
                //      $ppv_option = $download['options']['price_id'];
                _hss_woo_log("ppv option = " . $ppv_option);
                $params = array(
                        'method' => 'secure_videos.add_user_ppv',
                        'api_key' => $options['api_key'],
                        'ppv_id' => $ppv_option,
                        'private_user_id' => $userId,
                        'database_id' => $options['database_id']
                );
                _hss_woo_log($params);
                $response = wp_remote_post(
                        "https://www.hoststreamsell.com/services/api/rest/xml/",
                        array(
                                'method' => 'POST',
                                'timeout' => 15,
                                'redirection' => 5,
                                'httpversion' => '1.0',
                                'blocking' => true,
                                'headers' => array(),
                                'body' => $params,
                                'cookies' => array()
                        )
                );

                //$video_id = get_post_meta($product->ID, '_woo_video_id', true);
                //update_post_meta($item->order_item_id, '_woo_video_id', $video_id);

                $randString = md5(uniqid(rand(), true));
                wc_add_order_item_meta($item->order_item_id, '_hss_woo_link', $randString);

                if (is_wp_error($response)) {
                        _hss_woo_log("error msg: " . $response->get_error_message() . "\n");
                        $hss_errors = get_user_meta($userId, "hss_errors", true);
                        $hss_errors[] = $ppv_option;
                        update_user_meta($userId, "hss_errors", $hss_errors);
                } else if ($response['response']['code'] != "200") {
                        _hss_woo_log("request code bad: " . $response['response']['code'] . "\n");
                        $hss_errors = get_user_meta($userId, "hss_errors", true);
                        $hss_errors[] = $ppv_option;
                        update_user_meta($userId, "hss_errors", $hss_errors);
                } else {
                        _hss_woo_log("request code good: " . $response['response']['code'] . "\n");
                }

                $res = $response['body'];
                _hss_woo_log($res);

                $xml = new SimpleXMLElement($res);
                _hss_woo_log($xml);
                $ppv_type_id = (int)$xml->result;
                _hss_woo_log($ppv_type_id);
                wc_add_order_item_meta($item_id, '_hss_woo_ppv_type_id', $ppv_type_id);
        }


        function woo_complete_purchase_add_video_processing($order_id)
        {

                _hss_woo_log("woo_complete_purchase_add_video_processing");
                // order object (optional but handy)
                $order = new WC_Order($order_id);
                $options = get_option('hss_woo_options');

                if ($options['add_video_on_processing'] == 1) {
                        if (count($order->get_items()) > 0) {
                                foreach ($order->get_items() as $item_id => $item) {

                                        $product_obj = $order->get_product_from_item($item);
                                        $product = $product_obj->get_post_data();
                                        if ((get_post_meta($product->ID, 'is_streaming_video', true)) or (get_post_meta($product->ID, 'is_streaming_video_bundle', true))) {
                                                $userId = $order->user_id;

                                                $ppv_option = null;
                                                //if(empty($download['options']))
                                                _hss_woo_log("product id = " . $product->ID);
                                                $ppv_option = get_post_meta($product->ID, '_woo_ppv_id', true);
                                                //else
                                                //      $ppv_option = $download['options']['price_id'];

                                                if (!empty($item['variation_id']) && 'product_variation' === get_post_type($item['variation_id'])) {
                                                        $var_product = get_post($item['variation_id']);
                                                        _hss_woo_log("variation id = " . $var_product->ID);
                                                        $ppv_option = get_post_meta($var_product->ID, '_woo_ppv_id', true);
                                                }

                                                _hss_woo_log("ppv option = " . $ppv_option);
                                                $params = array(
                                                        'method' => 'secure_videos.add_user_ppv',
                                                        'api_key' => $options['api_key'],
                                                        'ppv_id' => $ppv_option,
                                                        'private_user_id' => $userId,
                                                        'database_id' => $options['database_id']
                                                );
                                                _hss_woo_log($params);
                                                $response = wp_remote_post(
                                                        "https://www.hoststreamsell.com/services/api/rest/xml/",
                                                        array(
                                                                'method' => 'POST',
                                                                'timeout' => 15,
                                                                'redirection' => 5,
                                                                'httpversion' => '1.0',
                                                                'blocking' => true,
                                                                'headers' => array(),
                                                                'body' => $params,
                                                                'cookies' => array()
                                                        )
                                                );

                                                $video_id = get_post_meta($product->ID, '_woo_video_id', true);
                                                //update_post_meta($item->ID, '_woo_video_id', $video_id);

                                                if ($options['use_non_loggedin_video_links'] == 1) {
                                                        $randString = md5(uniqid(rand(), true));
                                                        wc_add_order_item_meta($item_id, '_hss_woo_link', $randString);
                                                }

                                                if (is_wp_error($response)) {
                                                        _hss_woo_log("error msg: " . $response->get_error_message() . "\n");
                                                        $hss_errors = get_user_meta($userId, "hss_errors", true);
                                                        $hss_errors[] = $ppv_option;
                                                        update_user_meta($userId, "hss_errors", $hss_errors);
                                                } else if ($response['response']['code'] != "200") {
                                                        _hss_woo_log("request code bad: " . $response['response']['code'] . "\n");
                                                        $hss_errors = get_user_meta($userId, "hss_errors", true);
                                                        $hss_errors[] = $ppv_option;
                                                        update_user_meta($userId, "hss_errors", $hss_errors);
                                                } else {
                                                        _hss_woo_log("request code good: " . $response['response']['code'] . "\n");
                                                }

                                                $res = $response['body'];

                                                $xml = new SimpleXMLElement($res);
                                                _hss_woo_log($xml);
                                                $ppv_type_id = (int)$xml->result;
                                                _hss_woo_log($ppv_type_id);
                                                wc_add_order_item_meta($item_id, '_hss_woo_ppv_type_id', $ppv_type_id);
                                        }
                                }
                        }
                        update_post_meta($order_id, '_hss_woo_processed', true);
                }
                #return $order_status;
                #return ‘completed’;
        }

        add_action('woocommerce_order_status_processing', 'woo_complete_purchase_add_video_processing');

        function woo_delete_video_access($order_id)
        {

                _hss_woo_log("woo_delete_video_access");
                // order object (optional but handy)
                $order = new WC_Order($order_id);
                $options = get_option('hss_woo_options');

                if (count($order->get_items()) > 0) {
                        foreach ($order->get_items() as $item_id => $item) {

                                $product_obj = $order->get_product_from_item($item);
                                $product = $product_obj->get_post_data();
                                if ((get_post_meta($product->ID, 'is_streaming_video', true)) or (get_post_meta($product->ID, 'is_streaming_video_bundle', true))) {
                                        $userId = $order->user_id;
                                        $ppv_type = "video";
                                        if (get_post_meta($product->ID, 'is_streaming_video_bundle', true)) {
                                                $ppv_type = "group";
                                        }
                                        $ppv_type_id = wc_get_order_item_meta($item_id, '_hss_woo_ppv_type_id', true);
                                        if (wc_get_order_item_meta($item_id, '_hss_woo_ppv_type_id', true)) {
                                                $ppv_type_id = wc_get_order_item_meta($item_id, '_hss_woo_ppv_type_id', true);

                                                _hss_woo_log("ppv option = " . $ppv_option);
                                                $params = array(
                                                        'method' => 'secure_videos.delete_user_ppv',
                                                        'api_key' => $options['api_key'],
                                                        'ppv_type' => $ppv_type,
                                                        'ppv_type_id' => $ppv_type_id,
                                                        'private_user_id' => $userId,
                                                        'database_id' => $options['database_id']
                                                );
                                                _hss_woo_log($params);
                                                $response = wp_remote_post(
                                                        "https://www.hoststreamsell.com/services/api/rest/xml/",
                                                        array(
                                                                'method' => 'POST',
                                                                'timeout' => 15,
                                                                'redirection' => 5,
                                                                'httpversion' => '1.0',
                                                                'blocking' => true,
                                                                'headers' => array(),
                                                                'body' => $params,
                                                                'cookies' => array()
                                                        )
                                                );
                                                $res = $response['body'];

                                                $xml = new SimpleXMLElement($res);
                                                _hss_woo_log($xml);
                                        } else {
                                                _hss_woo_log("woo_delete_video_access - _hss_woo_ppv_type_id not found for order item");
                                        }
                                }
                        }
                }
        }



        add_action('woocommerce_order_status_cancelled', 'woo_delete_video_access');
        add_action('woocommerce_order_status_refunded', 'woo_delete_video_access');

        function update_videos($only_new = false)
        {
                #global $post;
                $options = get_option('hss_woo_options');

                $params = array(
                        'method' => 'secure_videos.get_user_video_groups',
                        'api_key' => $options['api_key']
                );

                $group_response = wp_remote_post(
                        "https://www.hoststreamsell.com/services/api/rest/xml/",
                        array(
                                'method' => 'POST',
                                'timeout' => 15,
                                'redirection' => 5,
                                'httpversion' => '1.0',
                                'blocking' => true,
                                'headers' => array(),
                                'body' => $params,
                                'cookies' => array()
                        )
                );
                $group_res = "";
                if (is_wp_error($group_response)) {
                        _hss_woo_log("ERROR");
                        $error_string = $group_response->get_error_message();
                        _hss_woo_log($error_string);
                } else {
                        $group_res = $group_response['body'];
                }


                $group_xml = new SimpleXMLElement($group_res);
                _hss_woo_log($group_xml);

                $status = $group_xml->status;
                _hss_woo_log("STATUS: " . $status . " ONLY NEW=" . $only_new);
                if ($status == "0") {
                        $seen_videos = array();

                        $group_count = $group_xml->result->video_group_count;
                        $group_index = 1;
                        while ($group_index <= $group_count) {
                                $group_video_count = (int)$group_xml->result[0]->{'video_group' . $group_index}[0]->video_count;
                                _hss_woo_log("video count = " . $group_video_count);
                                if ($group_video_count > 0) {
                                        $group_video_post_ids = array();
                                        $group_video_post_index = 0;
                                        $group_id = (string)$group_xml->result[0]->{'video_group' . $group_index}[0]->video_group_id;
                                        $group_title = (string)$group_xml->result[0]->{'video_group' . $group_index}[0]->title[0];
                                        $group_description = (string)$group_xml->result[0]->{'video_group' . $group_index}[0]->description[0];
                                        //$group_thumbnail = (string)$group_xml->result[0]->{'video_group'.$group_index}[0]->thumbnail[0];
                                        $group_thumbnail = (string)$group_xml->result[0]->{'video_group' . $group_index}[0]->thumbnail_big[0];
                                        _hss_woo_log("Group id=" . $group_id);
                                        _hss_woo_log(get_cat_ID($group_title));
                                        if (!term_exists($group_title, 'product_cat') and $only_new != "true") {
                                                _hss_woo_log("Creating category " . $group_title);
                                                wp_insert_term(
                                                        $group_title, // the term 
                                                        'product_cat' // the taxonomy
                                                );
                                        }
                                        $params = array(
                                                'method' => 'secure_videos.get_user_video_list_by_group_with_purchase_options',
                                                'api_key' => $options['api_key'],
                                                'group_id' => $group_id,
                                        );
                                        _hss_woo_log("group_id=" . $params['group_id']);
                                        $response = wp_remote_post(
                                                "https://www.hoststreamsell.com/services/api/rest/xml/",
                                                array(
                                                        'method' => 'POST',
                                                        'timeout' => 15,
                                                        'redirection' => 5,
                                                        'httpversion' => '1.0',
                                                        'blocking' => true,
                                                        'headers' => array(),
                                                        'body' => $params,
                                                        'cookies' => array()
                                                )
                                        );
                                        $res = "";
                                        if (is_wp_error($response)) {
                                                _hss_woo_log("ERROR");
                                        } else {
                                                $res = $response['body'];
                                        }

                                        $xml = new SimpleXMLElement($res);
                                        _hss_woo_log($xml);
                                        _hss_woo_log("STATUS: " . $status);
                                        if ($status == "0") {
                                                $count = (int)$xml->result->video_count;
                                                _hss_woo_log("Video count=" . $count);
                                                $index = 1;
                                                while ($index <= $count) {
                                                        _hss_woo_log("checking video");
                                                        $video_id = (string)$xml->result[0]->{'video' . $index}[0]->video_id;
                                                        $title = (string)$xml->result[0]->{'video' . $index}[0]->title[0];
                                                        $description = (string)$xml->result[0]->{'video' . $index}[0]->description[0];
                                                        //$thumbnail = (string)$xml->result[0]->{'video'.$index}[0]->thumbnail[0];
                                                        $thumbnail = (string)$xml->result[0]->{'video' . $index}[0]->thumbnail_big[0];
                                                        $args = array(
                                                                'meta_key' => '_woo_video_id',
                                                                'meta_value' => $video_id,
                                                                'post_type' => 'product',
                                                        );
                                                        _hss_woo_log($args);
                                                        $my_query = null;
                                                        $my_query = new WP_Query($args);
                                                        $post_ID = -1;
                                                        $video_existed = false;
                                                        if ($my_query->have_posts()) {
                                                                _hss_woo_log("Video already a post");
                                                                $video_post = $my_query->next_post();
                                                                if ($only_new) {
                                                                        $group_video_post_ids[$group_video_post_index] = $video_post->ID;
                                                                        $group_video_post_index += 1;
                                                                        _hss_woo_log("...skipping");
                                                                        $index += 1;
                                                                        continue;
                                                                }
                                                                $video_existed = true;
                                                                _hss_woo_log("video_post ID=" . $video_post->ID);
                                                                if ($options['disable_desc_updates'] == 1) {
                                                                        $my_post = array(
                                                                                'ID' => $video_post->ID,
                                                                                'post_title' => $video_post->post_title,
                                                                        );
                                                                } else {
                                                                        $my_post = array(
                                                                                'ID' => $video_post->ID,
                                                                                'post_title' => $title,
                                                                                'post_content' => $description,
                                                                        );
                                                                }
                                                                // Update the post into the database
                                                                #remove_action('save_post', 'wpse51363_save_post');
                                                                $post_ID = wp_update_post($my_post);
                                                                _hss_woo_log("RESULT FROM UPDATE: " . $post_ID);
                                                                #add_action('save_post', 'wpse51363_save_post');
                                                        } else {
                                                                // Create post object
                                                                _hss_woo_log("Create video post");
                                                                $my_post = array(
                                                                        'post_title' => $title,
                                                                        'post_content' => $description,
                                                                        'post_status' => 'publish',
                                                                        'post_author' => 1,
                                                                        'post_type' => 'product',
                                                                );

                                                                // Insert the post into the database
                                                                $post_ID = wp_insert_post($my_post);

                                                                $url = $thumbnail;
                                                                if ($url != "") {
                                                                        $tmp = download_url($url);
                                                                        $file_array = array(
                                                                                'name' => basename($url),
                                                                                'tmp_name' => $tmp
                                                                        );

                                                                        // Check for download errors
                                                                        if (is_wp_error($tmp)) {
                                                                                _hss_woo_log($tmp);
                                                                                @unlink($file_array['tmp_name']);
                                                                                #return $tmp;
                                                                        }

                                                                        $thumb_id = media_handle_sideload($file_array, 0);
                                                                        // Check for handle sideload errors.
                                                                        if (is_wp_error($thumb_id)) {
                                                                                _hss_woo_log($thumb_id);
                                                                                @unlink($file_array['tmp_name']);
                                                                                #return $thumb_id;
                                                                        }

                                                                        $attachment_url = wp_get_attachment_url($thumb_id);
                                                                        #_hss_woo_log("Attachment URL (".$thumb_id."): ".$attachment_url);
                                                                        // Do whatever you have to here
                                                                        set_post_thumbnail($post_ID, $thumb_id);
                                                                }
                                                        }
                                                        if ($only_new != "true") {
                                                                $terms = array();
                                                                if (!in_array($video_id, $seen_videos))
                                                                        array_push($seen_videos, $video_id);
                                                                else
                                                                        $terms = wp_get_object_terms($post_ID, 'product_cat');
                                                                $vid_cats = array();
                                                                if (!empty($terms)) {
                                                                        if (!is_wp_error($terms)) {
                                                                                foreach ($terms as $term) {
                                                                                        array_push($vid_cats, $term->name);
                                                                                }
                                                                        }
                                                                }
                                                                _hss_woo_log($vid_cats);
                                                                if (!in_array($group_title, $vid_cats)) {
                                                                        _hss_woo_log("adding category terms");
                                                                        array_push($vid_cats, $group_title);
                                                                        _hss_woo_log($vid_cats);
                                                                        wp_set_object_terms($post_ID, $vid_cats, 'product_cat');
                                                                }
                                                                $term = get_term_by('name', $group_title, 'product_cat');
                                                                wp_update_term($term->term_id, 'product_cat', array('description' => $group_description));
                                                        }
                                                        update_post_meta($post_ID, '_woo_video_id', $video_id);

                                                        $group_video_post_ids[$group_video_post_index] = $post_ID;

                                                        $group_video_post_index += 1;
                                                        $purchase_option_count = (int)$xml->result[0]->{'video' . $index}[0]->option_count;
                                                        $prices = array();
                                                        $option_index = 1;
                                                        $option_price = "";
                                                        $lowest_price = 0;
                                                        $option_name = "";
                                                        $use_long_option_names = False;
                                                        if ($purchase_option_count > 0) {
                                                                $time_limits = array();
                                                                while ($option_index <= $purchase_option_count) {
                                                                        $time_limit = (string)$xml->result[0]->{'video' . $index}[0]->{'option' . $option_index}[0]->time_limit;
                                                                        if (array_key_exists($time_limit, $time_limits))
                                                                                $use_long_option_names = True;
                                                                        else
                                                                                $time_limits[$time_limit] = $time_limit;
                                                                        $option_index += 1;
                                                                }
                                                                $option_index = 1;
                                                                $purchase_option_details = array();
                                                                while ($option_index <= $purchase_option_count) {
                                                                        $option_id = (int)$xml->result[0]->{'video' . $index}[0]->{'option' . $option_index}[0]->option_id;
                                                                        $option_type = (string)$xml->result[0]->{'video' . $index}[0]->{'option' . $option_index}[0]->type;
                                                                        $option_price = (string)$xml->result[0]->{'video' . $index}[0]->{'option' . $option_index}[0]->price;
                                                                        if ((((float)$option_price) < $lowest_price) or ($lowest_price == 0))
                                                                                $lowest_price = (float)$option_price;

                                                                        $bandwidth_cap = (string)$xml->result[0]->{'video' . $index}[0]->{'option' . $option_index}[0]->bandwidth_cap;
                                                                        $time_limit = (string)$xml->result[0]->{'video' . $index}[0]->{'option' . $option_index}[0]->time_limit;
                                                                        $rate_limit = (string)$xml->result[0]->{'video' . $index}[0]->{'option' . $option_index}[0]->rate_limit;
                                                                        $download_limit = (string)$xml->result[0]->{'video' . $index}[0]->{'option' . $option_index}[0]->download_limit;
                                                                        $option_name = __($time_limit . ' access', 'my_text_domain');
                                                                        if ($use_long_option_names == True) {
                                                                                if ($bandwidth_cap != "Unlimited")
                                                                                        $option_name = $option_name . ' ' . $bandwidth_cap . ' Data Cap';
                                                                                if ($rate_limit != "No limit")
                                                                                        $option_name = $option_name . ' rate limited to ' . $rate_limit . ' kbps';
                                                                                if ($download_limit == "No Downloads")
                                                                                        $option_name = $option_name . ' (no download access)';
                                                                                elseif ($download_limit == "Any Bitrate Available")
                                                                                        $option_name = $option_name . ' (includes download access)';
                                                                                else
                                                                                        $option_name = $option_name . ' (download accesss ' . $download_limit . ')';
                                                                        }

                                                                        $prices[$option_id] = array('name' => $option_name, 'amount' => $option_price);
                                                                        _hss_woo_log("option id=" . $option_id);
                                                                        _hss_woo_log($prices[$option_id]["name"]);
                                                                        $option_index += 1;
                                                                }
                                                        }

                                                        update_post_meta($post_ID, '_visibility', 'visible');
                                                        update_post_meta($post_ID, '_downloadable', 'yes');
                                                        if ($options['product_virtual'] == "virtual" or $options['product_virtual'] == "")
                                                                update_post_meta($post_ID, '_virtual', 'yes');
                                                        _hss_woo_log("PostID=" . $post_ID);
                                                        if ($option_index == 1) {
                                                                //add no selling options
                                                                _hss_woo_log("1");
                                                                $variations = get_posts(array(
                                                                        'post_parent'    => $post_ID,
                                                                        'post_type'    => 'product_variation',
                                                                ));

                                                                if ($variations) {
                                                                        foreach ($variations as $variation_post)
                                                                                if (strpos($variation_post->post_title, 'Access Period') !== false)
                                                                                        wp_delete_post($variation_post->ID);
                                                                }
                                                                delete_post_meta($post_ID, '_product_attributes');
                                                                delete_post_meta($post_ID, '_regular_price');
                                                                delete_post_meta($post_ID, '_price');
                                                                delete_post_meta($post_ID, '_woo_ppv_id');
                                                                wp_set_object_terms($post_ID, 'standard', 'product_type');
                                                        } elseif ($option_index == 2) {
                                                                _hss_woo_log("2 setting _woo_ppv_id to " . $option_id);
                                                                $variations = get_posts(array(
                                                                        'post_parent'    => $post_ID,
                                                                        'post_type'    => 'product_variation',
                                                                ));

                                                                if ($variations) {
                                                                        foreach ($variations as $variation_post)
                                                                                if (strpos($variation_post->post_title, 'Access Period') !== false)
                                                                                        wp_delete_post($variation_post->ID);
                                                                }
                                                                delete_post_meta($post_ID, '_product_attributes');
                                                                update_post_meta($post_ID, '_regular_price', $option_price);
                                                                update_post_meta($post_ID, '_price', $option_price);
                                                                update_post_meta($post_ID, '_woo_ppv_id', $option_id);
                                                                wp_set_object_terms($post_ID, 'standard', 'product_type');
                                                        } else {
                                                                _hss_woo_log("3 post id=" . $post_ID . " setting _woo_ppv_id to " . $option_id);
                                                                update_post_meta($post_ID, '_regular_price', '');
                                                                update_post_meta($post_ID, '_sale_price', '');
                                                                update_post_meta($post_ID, '_sale_price_dates_from', '');
                                                                update_post_meta($post_ID, '_sale_price_dates_to', '');
                                                                update_post_meta($post_ID, '_price', '');
                                                                delete_post_meta($post_ID, '_woo_ppv_id');

                                                                //Sets the attributes up to be used as variations but doesnt actually set them up as variations
                                                                wp_set_object_terms($post_ID, 'variable', 'product_type');

                                                                $attribute_options = "";
                                                                foreach ($prices as $option_id => $option_values) {
                                                                        $attribute_options .= $option_values['name'] . "|";
                                                                }

                                                                $thedata = array(
                                                                        'access-period' => array(
                                                                                'name' => 'Access Period',
                                                                                'value' => $attribute_options,
                                                                                'is_visible' => '1',
                                                                                'is_variation' => '1',
                                                                                'is_taxonomy' => '0'
                                                                        )
                                                                );
                                                                update_post_meta($post_ID, '_product_attributes', $thedata);

                                                                $variations = get_posts(array(
                                                                        'post_parent'    => $post_ID,
                                                                        'post_type'    => 'product_variation',
                                                                ));

                                                                if ($variations) {
                                                                        foreach ($variations as $variation_post)
                                                                                //_hss_woo_log("variation post_title=".$variation_post->post_title);
                                                                                //if (strpos($variation_post->post_title,'Access Period') !== false){
                                                                                wp_delete_post($variation_post->ID);
                                                                        //_hss_woo_log("variation post_title=".$variation_post->post_title. "DELETED");
                                                                        //}
                                                                }



                                                                $variations = get_posts(array(
                                                                        'post_parent'    => $post_ID,
                                                                        'post_type'    => 'product_variation',
                                                                ));

                                                                if ($variations) {
                                                                        foreach ($variations as $variation_post)
                                                                                _hss_woo_log("AFTER DELETES variation post_title=" . $variation_post->post_title);
                                                                }

                                                                foreach ($prices as $option_id => $option_values) {
                                                                        $new_variation = array(
                                                                                'post_title'   => 'Variation # ' . $option_id . ' of Access Period',
                                                                                'post_content' => '',
                                                                                'post_name'   => 'product-' . $post_ID . '-' . $option_id . '-variation',
                                                                                'post_status'  => 'publish',
                                                                                'post_parent'  => $post_ID,
                                                                                'post_type'    => 'product_variation',
                                                                        );
                                                                        $variation_id = wp_insert_post($new_variation);
                                                                        do_action('woocommerce_create_product_variation', $variation_id);
                                                                        _hss_woo_log('ADDING variation : Variation # ' . $option_id . ' of Access Period');
                                                                        if ($options['product_virtual'] == "virtual" or $options['product_virtual'] == "")
                                                                                update_post_meta($variation_id, '_virtual', 'yes');
                                                                        update_post_meta($variation_id, '_downloadable', 'no');
                                                                        update_post_meta($variation_id, '_manage_stock', 'no');
                                                                        update_post_meta($variation_id, '_stock_status', 'instock');
                                                                        update_post_meta($variation_id, '_price', $option_values['amount']);
                                                                        update_post_meta($variation_id, '_sale_price', $option_values['amount']);
                                                                        update_post_meta($variation_id, '_regular_price', $option_values['amount']);
                                                                        update_post_meta($variation_id, '_sale_price_dates_from', '');
                                                                        update_post_meta($variation_id, '_sale_price_dates_to', '');
                                                                        //update_post_meta( $variation_id, '_price', $option_values['amount'] );
                                                                        update_post_meta($variation_id, 'attribute_access-period', sanitize_text_field($option_values['name']));

                                                                        WC_Product_Variable::sync($post_ID);
                                                                        update_post_meta($variation_id, '_woo_ppv_id', $option_id);
                                                                        update_post_meta($variation_id, 'is_streaming_video', true);
                                                                }
                                                        }
                                                        update_post_meta($post_ID, 'is_streaming_video', true);

                                                        $index += 1;
                                                }
                                        }
                                        $prices = array();
                                        $option_index = 1;
                                        $purchase_option_count = (int)$xml->result->group_option_count;
                                        $option_name = "";
                                        if ($purchase_option_count > 0) {
                                                $args = array(
                                                        'meta_key' => '_hss_woo_group_id',
                                                        'meta_value' => $group_id,
                                                        'post_type' => 'product',
                                                );
                                                _hss_woo_log($args);
                                                $my_query = null;
                                                $my_query = new WP_Query($args);
                                                $post_ID = -1;
                                                if ($my_query->have_posts()) {
                                                        _hss_woo_log("Video group already a post");
                                                        $video_group_post = $my_query->next_post();
                                                        _hss_woo_log("video_group_post ID=" . $video_group_post->ID);
                                                        if ($only_new != "true") {
                                                                if ($options['disable_desc_updates'] == 1) {
                                                                        $my_post = array(
                                                                                'ID' => $video_group_post->ID,
                                                                                'post_title' => $video_group_post->post_title,
                                                                        );
                                                                } else {
                                                                        $my_post = array(
                                                                                'ID' => $video_group_post->ID,
                                                                                'post_title' => $group_title,
                                                                                'post_content' => $group_description,
                                                                        );
                                                                }
                                                                // Update the post into the database
                                                                $post_ID = wp_update_post($my_post);
                                                                _hss_woo_log("RESULT FROM UPDATE: " . $post_ID);
                                                        } else {
                                                                $post_ID = $video_group_post->ID;
                                                        }
                                                } else {
                                                        // Create post object
                                                        _hss_woo_log("Create video group post");
                                                        $my_post = array(
                                                                'post_title' => $group_title,
                                                                'post_content' => $group_description,
                                                                'post_status' => 'publish',
                                                                'post_author' => 1,
                                                                'post_type' => 'product',

                                                        );

                                                        // Insert the post into the database
                                                        $post_ID = wp_insert_post($my_post);
                                                        $url = $group_thumbnail;
                                                        if ($url != "") {
                                                                $tmp = download_url($url);
                                                                $file_array = array(
                                                                        'name' => basename($url),
                                                                        'tmp_name' => $tmp
                                                                );
                                                                // Check for download errors
                                                                if (is_wp_error($tmp)) {
                                                                        _hss_woo_log($tmp);
                                                                        @unlink($file_array['tmp_name']);
                                                                        return $tmp;
                                                                }

                                                                $thumb_id = media_handle_sideload($file_array, 0);
                                                                // Check for handle sideload errors.
                                                                if (is_wp_error($thumb_id)) {
                                                                        _hss_woo_log($thumb_id);
                                                                        @unlink($file_array['tmp_name']);
                                                                        return $thumb_id;
                                                                }

                                                                $attachment_url = wp_get_attachment_url($thumb_id);
                                                                _hss_woo_log("Attachment URL (" . $thumb_id . "): " . $attachment_url);
                                                                // Do whatever you have to here
                                                                set_post_thumbnail($post_ID, $thumb_id);
                                                        }
                                                }
                                                if ($only_new != "true") {
                                                        update_post_meta($post_ID, '_hss_woo_group_id', $group_id);

                                                        $terms = wp_get_object_terms($post_ID, 'product_cat');

                                                        $vid_cats = array();
                                                        _hss_woo_log($vid_cats);

                                                        if (!empty($terms)) {
                                                                if (!is_wp_error($terms)) {
                                                                        foreach ($terms as $term) {
                                                                                array_push($vid_cats, $term->name);
                                                                        }
                                                                }
                                                        }

                                                        if (!in_array($group_title, $vid_cats)) {
                                                                _hss_woo_log("adding term");
                                                                array_push($vid_cats, $group_title);
                                                                _hss_woo_log($vid_cats);
                                                                wp_set_object_terms($post_ID, $vid_cats, 'product_cat');
                                                        }


                                                        $purchase_option_details = array();
                                                        while ($option_index <= $purchase_option_count) {
                                                                $option_id = (int)$xml->result[0]->{'group_option' . $option_index}[0]->option_id;
                                                                $option_type = (string)$xml->result[0]->{'group_option' . $option_index}[0]->type;
                                                                $option_price = (string)$xml->result[0]->{'group_option' . $option_index}[0]->price;
                                                                if ((((float)$option_price) < $lowest_price) or ($lowest_price == 0))
                                                                        $lowest_price = (float)$option_price;
                                                                $bandwidth_cap = (string)$xml->result[0]->{'group_option' . $option_index}[0]->bandwidth_cap;
                                                                $time_limit = (string)$xml->result[0]->{'group_option' . $option_index}[0]->time_limit;
                                                                $rate_limit = (string)$xml->result[0]->{'group_option' . $option_index}[0]->rate_limit;
                                                                //$download_limit = (string)$group_xml->result[0]->{'group_option'.$option_index}[0]->download_limit;
                                                                $option_name = $time_limit . ' access';
                                                                /*if($bandwidth_cap!="Unlimited")
                                                        $option_name = $option_name.' '.$bandwidth_cap.' Data Cap';
                                                if($rate_limit!="No limit")
                                                         $option_name = $option_name.' rate limited to '.$rate_limit.' kbps';
                                                if($download_limit=="No Downloads")
                                                         $option_name = $option_name.' (no download access)';
                                                elseif($download_limit=="Any Bitrate Available")
                                                         $option_name = $option_name.' (includes download access)';
                                                else
                                                         $option_name = $option_name.' (download accesss '.$download_limit.')';*/
                                                                $prices[$option_id] = array('name' => $option_name, 'amount' => $option_price);
                                                                _hss_woo_log("group option id=" . $option_id);
                                                                _hss_woo_log($prices[$option_id]["name"]);
                                                                $option_index += 1;
                                                        }

                                                        update_post_meta($post_ID, '_visibility', 'visible');
                                                        update_post_meta($post_ID, '_downloadable', 'yes');
                                                        if ($options['product_virtual'] == "virtual" or $options['product_virtual'] == "")
                                                                update_post_meta($post_ID, '_virtual', 'yes');

                                                        _hss_woo_log("PostID=" . $post_ID);
                                                        if ($option_index == 1) {
                                                                _hss_woo_log("1");
                                                                $variations = get_posts(array(
                                                                        'post_parent'    => $post_ID,
                                                                        'post_type'    => 'product_variation',
                                                                ));

                                                                if ($variations) {
                                                                        foreach ($variations as $variation_post)
                                                                                if (strpos($variation_post->post_title, 'Access Period') !== false)
                                                                                        wp_delete_post($variation_post->ID);
                                                                }
                                                                delete_post_meta($post_ID, '_product_attributes');
                                                                delete_post_meta($post_ID, '_regular_price');
                                                                delete_post_meta($post_ID, '_price');
                                                                delete_post_meta($post_ID, '_woo_ppv_id');
                                                                wp_set_object_terms($post_ID, 'standard', 'product_type');
                                                        } elseif ($option_index == 2) {
                                                                _hss_woo_log("2 setting _woo_ppv_id to " . $option_id);
                                                                $variations = get_posts(array(
                                                                        'post_parent'    => $post_ID,
                                                                        'post_type'    => 'product_variation',
                                                                ));

                                                                if ($variations) {
                                                                        foreach ($variations as $variation_post)
                                                                                if (strpos($variation_post->post_title, 'Access Period') !== false)
                                                                                        wp_delete_post($variation_post->ID);
                                                                }
                                                                delete_post_meta($post_ID, '_product_attributes');
                                                                update_post_meta($post_ID, '_regular_price', $option_price);
                                                                update_post_meta($post_ID, '_price', $option_price);
                                                                update_post_meta($post_ID, '_woo_ppv_id', $option_id);
                                                                wp_set_object_terms($post_ID, 'standard', 'product_type');
                                                        } else {
                                                                _hss_woo_log("3 post id=" . $post_ID . " setting _woo_ppv_id to " . $option_id);
                                                                update_post_meta($post_ID, '_regular_price', '');
                                                                update_post_meta($post_ID, '_sale_price', '');
                                                                update_post_meta($post_ID, '_sale_price_dates_from', '');
                                                                update_post_meta($post_ID, '_sale_price_dates_to', '');
                                                                update_post_meta($post_ID, '_price', '');
                                                                delete_post_meta($post_ID, '_woo_ppv_id');

                                                                //Sets the attributes up to be used as variations but doesnt actually set them up as variations
                                                                wp_set_object_terms($post_ID, 'variable', 'product_type');

                                                                $attribute_options = "";
                                                                foreach ($prices as $option_id => $option_values) {
                                                                        $attribute_options .= $option_values['name'] . "|";
                                                                }

                                                                $thedata = array(
                                                                        'access-period' => array(
                                                                                'name' => 'Access Period',
                                                                                'post_content' => '',
                                                                                'value' => $attribute_options,
                                                                                'is_visible' => '1',
                                                                                'is_variation' => '1',
                                                                                'is_taxonomy' => '0'
                                                                        )
                                                                );
                                                                update_post_meta($post_ID, '_product_attributes', $thedata);

                                                                $variations = get_posts(array(
                                                                        'post_parent'    => $post_ID,
                                                                        'post_type'    => 'product_variation',
                                                                ));

                                                                if ($variations) {
                                                                        foreach ($variations as $variation_post)
                                                                                _hss_woo_log("4 delete group variation id=" . $variation_post->ID);
                                                                        if (strpos($variation_post->post_title, 'Access Period') !== false)
                                                                                wp_delete_post($variation_post->ID);
                                                                }

                                                                foreach ($prices as $option_id => $option_values) {
                                                                        $new_variation = array(
                                                                                'post_title'   => 'Variation # ' . $option_id . ' of Access Period',
                                                                                'post_name'   => 'product-' . $post_ID . '-' . $option_id . '-variation',
                                                                                'post_status'  => 'publish',
                                                                                'post_parent'  => $post_ID,
                                                                                'post_type'    => 'product_variation',
                                                                        );
                                                                        $variation_id = wp_insert_post($new_variation);
                                                                        do_action('woocommerce_create_product_variation', $variation_id);
                                                                        _hss_woo_log('ADDING variation id ' . $variation_id . ': Variation # ' . $option_id . ' of Access Period');
                                                                        if ($options['product_virtual'] == "virtual" or $options['product_virtual'] == "")
                                                                                update_post_meta($variation_id, '_virtual', 'yes');
                                                                        update_post_meta($variation_id, '_downloadable', 'no');
                                                                        update_post_meta($variation_id, '_manage_stock', 'no');
                                                                        update_post_meta($variation_id, '_stock_status', 'instock');
                                                                        update_post_meta($variation_id, '_price', $option_values['amount']);
                                                                        update_post_meta($variation_id, '_sale_price', $option_values['amount']);
                                                                        update_post_meta($variation_id, '_regular_price', $option_values['amount']);
                                                                        update_post_meta($variation_id, '_sale_price_dates_from', '');
                                                                        update_post_meta($variation_id, '_sale_price_dates_to', '');
                                                                        //update_post_meta( $variation_id, '_price', $option_values['amount'] );
                                                                        update_post_meta($variation_id, 'attribute_access-period', sanitize_text_field($option_values['name']));

                                                                        WC_Product_Variable::sync($post_ID);
                                                                        update_post_meta($variation_id, '_woo_ppv_id', $option_id);
                                                                        update_post_meta($variation_id, 'is_streaming_video', true);
                                                                }
                                                        }
                                                        update_post_meta($post_ID, 'is_streaming_video_bundle', true);
                                                        update_post_meta($post_ID, '_hss_woo_bundled_products', $group_video_post_ids);
                                                } else {
                                                        _hss_woo_log("Adding new videos to group");
                                                        update_post_meta($post_ID, '_hss_woo_bundled_products', $group_video_post_ids);
                                                }
                                        }
                                }
                                $group_index += 1;
                        }
                }
                return True;
        }

        function get_video_download_links($hss_video_id, $videolink)
        {

                global $user_ID;
                $options = get_option('hss_woo_options');
                $userId = $user_ID;

                //$encode_id = 162;
                _hss_woo_log("get_video_download_links " . $hss_video_id . " " . $videolink);
                if ($videolink != "") {
                        global $wpdb;
                        $sql = "
                                        SELECT order_id FROM {$wpdb->prefix}woocommerce_order_itemmeta oim
                                        LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi
                                        ON oim.order_item_id = oi.order_item_id
                                        WHERE meta_key = '_hss_woo_link' AND meta_value = '%s'
                                        GROUP BY order_id;
                                        ";
                        $order_id = $wpdb->get_col($wpdb->prepare($sql, $videolink));
                        if ($order_id) {
                                $order = new WC_Order($order_id[0]);
                                $userId = $order->user_id;
                        }
                }

                $params = array(
                        'method' => 'secure_videos.get_all_video_download_links',
                        'api_key' => $options['api_key'],
                        'video_id' => $hss_video_id,
                        'private_user_id' => $userId,
                        'database_id' => $options['database_id']
                );
                _hss_woo_log($params);
                $response = wp_remote_post(
                        "https://www.hoststreamsell.com/services/api/rest/xml/",
                        array(
                                'method' => 'POST',
                                'timeout' => 15,
                                'redirection' => 5,
                                'httpversion' => '1.0',
                                'blocking' => true,
                                'headers' => array(),
                                'body' => $params,
                                'cookies' => array()
                        )
                );
                $res = "";
                if (is_wp_error($response)) {
                        $return_string .= 'Error occured retieving video information, please try refresh the page';
                } else {
                        $res = $response['body'];
                }

                $xml = new SimpleXMLElement($res);
                _hss_woo_log($xml);

                $purchase_option_count = (int)$xml->result[0]->download_option_count;
                $option_index = 1;
                $return_string = "";
                if ($purchase_option_count > 0) {
                        $return_string = "<div>Video file downloads:</div>";
                        while ($option_index <= $purchase_option_count) {
                                $url = $xml->result[0]->{'download_option' . $option_index}[0]->url;
                                $name = $xml->result[0]->{'download_option' . $option_index}[0]->name;
                                #$return_string = $return_string.'<LI><a href="'.$url.'">'.$name.'</a></LI>';
                                $return_string = $return_string . '<div class="hss_download_file"><a href="' . $url . '">' . $name . '</a></div>';
                                $option_index += 1;
                        }
                        //$return_string = $return_string."</UL>";
                } else {
                        $return_string = "<div>No Video file downloads</div>";
                }


                return $return_string;
        }

        add_action('woocommerce_product_after_variable_attributes', 'hss_woo_variation_settings_fields', 10, 3);
        add_action('woocommerce_save_product_variation', 'hss_woo_save_variation_settings_fields', 10, 2);
        add_filter('woocommerce_available_variation', 'hss_woo_load_variation_settings_fields');

        function hss_woo_variation_settings_fields($loop, $variation_data, $variation)
        {
                woocommerce_wp_text_input(
                        array(
                                'id'            => "{$loop}",
                                'name'          => "_woo_ppv_id[{$loop}]",
                                'value'         => get_post_meta($variation->ID, '_woo_ppv_id', true),
                                'label'         => __('HSS purchase option ID', 'woocommerce'),
                                'desc_tip'      => true,
                                'description'   => __('ID of the purchase option configured for this video in your account on hoststreamsell.com', 'woocommerce'),
                                'wrapper_class' => 'form-row form-row-full',
                        )
                );
        }

        function hss_woo_save_variation_settings_fields($variation_id, $loop)
        {
                $text_field = $_POST['_woo_ppv_id'][$loop];

                if (!empty($text_field)) {
                        update_post_meta($variation_id, '_woo_ppv_id', esc_attr($text_field));
                }
        }

        function hss_woo_load_variation_settings_fields($variation)
        {
                $variation['_woo_ppv_id'] = get_post_meta($variation['variation_id'], '_woo_ppv_id', true);

                return $variation;
        }

        add_filter('is_protected_meta', 'show_woo_hss_custom_fields', 10, 2);
        function show_woo_hss_custom_fields($protected, $meta_key)
        {
                if ('_hss_woo_group_id' == $meta_key) return false;
                if ('_woo_video_id' == $meta_key) return false;
                if ('_woo_ppv_id' == $meta_key) return false;
                return $protected;
        }

        add_shortcode('hss_woo_list_purchased_videos', 'hss_woo_list_purchased_videos_function');
        function hss_woo_list_purchased_videos_function($atts, $content, $sc)
        {
                global $wp_query;
                global $current_user;
                $options = get_option('hss_woo_options');

                $type = 'product';
                $args = array(
                        'post_type' => $type,
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'caller_get_posts' => 1
                );
                $my_query = null;
                $my_query = new WP_Query($args);
                if ($my_query->have_posts()) {
                        while ($my_query->have_posts()) : $my_query->the_post(); ?>
                        <p><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></p>
<?php
                        endwhile;
                }
        }

        function hss_woo_user_action_links($actions, $user_object)
        {
                $actions['hss_woo_check_video_access'] = '<a href="' . wp_nonce_url(admin_url('options-general.php?page=hss_admin&check_user_video_access=yes&user=' . $user_object->ID), 'hss_check_user_video_access', '_wpnonce') . '">' . __('Check Video Usage', 'hss-woo') . '</a>';
                return $actions;
        }
        if (is_main_site()) {
                add_filter('user_row_actions', 'hss_woo_user_action_links', 10, 2);
        }

        /*  
* Create a random string  
*@param $length the length of the string to create  
* @return $str the string  
*/
        function hss_woo_randomString($length = 12)
        {
                $str = "";
                $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
                $max = count($characters) - 1;
                for ($i = 0; $i < $length; $i++) {
                        $rand = mt_rand(0, $max);
                        $str .= $characters[$rand];
                }
                return $str;
        }

        function hss_woo_endsWith($haystack, $needle)
        {
                $length = strlen($needle);
                if ($length == 0) {
                        return true;
                }

                return (substr($haystack, -$length) === $needle);
        }

        function hss_woo_get_the_user_ip()
        {
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        //check ip from share internet
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        //to check ip is pass from proxy
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                }
                return $ip;
        }

?>