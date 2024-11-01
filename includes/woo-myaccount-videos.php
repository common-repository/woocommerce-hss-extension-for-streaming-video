<?php
class Woo_My_Account_Videos {

	/**
	 * Custom endpoint name.
	 *
	 * @var string
	 */
	public static $endpoint = 'videos';

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
		add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
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

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items
	 * @return array
	 */
	public function new_menu_items( $items ) {
		// Remove the logout menu item.
		$logout = $items['customer-logout'];
		if($logout)
			unset( $items['customer-logout'] );
		$account = $items['edit-account'];
		if($account)
			unset( $items['edit-account'] );
		$addresses = $items['edit-address'];
		if($addresses)
			unset( $items['edit-address'] );
		$downloads = $items['downloads'];
		if($downloads)
			unset( $items['downloads'] );
		
		// Insert your custom endpoint.
		$items[ self::$endpoint ] = __( 'Videos', 'hss-woo' );

		// Insert back the logout item.
		
		if($downloads)
			$items['downloads'] = $downloads;
		if($addresses)
			$items['edit-address'] = $addresses;
		if($account)
			$items['edit-account'] = $account;
		if($logout)
			$items['customer-logout'] = $logout;
		
		return $items;
	}

	/**
	 * Endpoint HTML content.
	 */
	public function endpoint_content() {
                $options = get_option('hss_woo_options');
                $videos_template = $options['videos-template-endpoint'];

                if($videos_template!=""){
                        wc_get_template( $videos_template );
                }else{

			hss_woo_get_template("purchased-videos-list.php");
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

new Woo_My_Account_Videos();

// Flush rewrite rules on plugin activation.
register_activation_hook( __FILE__, array( 'Woo_My_Account_Videos', 'install' ) );
