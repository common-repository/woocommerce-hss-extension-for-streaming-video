<?php
class HSS_Woo_My_Account_View_Video {

	/**
	 * Custom endpoint name.
	 *
	 * @var string
	 */
	public static $endpoint = 'view-video';

	/**
	 * Plugin actions.
	 */
	public function __construct() {
		// Actions used to insert a new endpoint in the WordPress.
		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Change the My Accout page title.
		add_filter( 'the_title', array( $this, 'endpoint_title' ) );

		// Insering your new tab/page into the My Account page.
		//add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
		add_action( 'woocommerce_account_' . self::$endpoint .  '_endpoint', array( $this, 'endpoint_content' ) );
	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function add_endpoints() {
		add_rewrite_endpoint( self::$endpoint, EP_ROOT | EP_PAGES );
	}

	/**
	 * Add new query var.
	 *
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = self::$endpoint;
		$vars[] .= "hss-video-id";
		return $vars;
	}

	/**
	 * Set endpoint title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function endpoint_title( $title ) {
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Videos', 'hss-woo' );

			remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
		}

		return $title;
	}
	

	 public function new_menu_items( $items ) {
                // Remove the logout menu item.
                $logout = $items['customer-logout'];
                unset( $items['customer-logout'] );

                // Insert your custom endpoint.
                $items[ self::$endpoint ] = __( 'ViewVideo', 'woocommerce' );

                $items['customer-logout'] = $logout;

                return $items;
        }


	/**
	 * Endpoint HTML content.
	 */
	public function endpoint_content() {
		$options = get_option('hss_woo_options');
		$view_video_template = $options['view-video-template-endpoint'];
		
		if($view_video_template!=""){
			wc_get_template( $view_video_template );
		}else{
		global $wp_query;
		if(isset($wp_query->query_vars['hss-video-id'])) {
			$vidpost = get_post($wp_query->query_vars['hss-video-id']);
			if(get_post_meta($vidpost->ID, 'is_streaming_video', true)) {	
				hss_woo_get_template("view-purchased-video.php");
			}elseif(get_post_meta($vidpost->ID, 'is_streaming_video_bundle', true)) {
				hss_woo_get_template("purchased-video-group-list.php");
			}else{
				echo "Error: Video or Video Group not found for ID ".$wp_query->query_vars['hss-video-id'];
			}
		}
		?>
		<?php
		}
	}

	/**
	 * Plugin install action.
	 * Flush rewrite rules to make our custom endpoint available.
	 */
	public static function install() {
		flush_rewrite_rules();
	}
}

new HSS_Woo_My_Account_View_Video();

// Flush rewrite rules on plugin activation.
register_activation_hook( __FILE__, array( 'HSS_Woo_My_Account_View_Video', 'install' ) );
