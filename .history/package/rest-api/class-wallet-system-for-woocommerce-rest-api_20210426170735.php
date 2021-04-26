<?php
/**
 * The file that defines the core plugin api class
 *
 * A class definition that includes api's endpoints and functions used across the plugin
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/package/rest-api/version1
 */

/**
 * The core plugin  api class.
 *
 * This is used to define internationalization, api-specific hooks, and
 * endpoints for plugin.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/package/rest-api/version1
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Wallet_System_For_Woocommerce_Rest_Api {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin api.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the merthods, and set the hooks for the api and
	 *
	 * @since    1.0.0
	 * @param   string $plugin_name    Name of the plugin.
	 * @param   string $version        Version of the plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'wsfw-route/v1';

    /**
     * Route base.
     *
     * @var string
     */
    protected $base_url = '/wallet/';

	/**
	 * Define endpoints for the plugin.
	 *
	 * Uses the Wallet_System_For_Woocommerce_Rest_Api class in order to create the endpoint
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function mwb_wsfw_add_endpoint() {

		register_rest_route(
			'wsfw-route/v1',
			'/wsfw-dummy-data/',
			array(
				// 'methods'  => 'POST',
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'mwb_wsfw_default_callback' ),
				'permission_callback' => array( $this, 'mwb_wsfw_default_permission_check' ),
			)
		);

		// for getting particular user wallet details

		register_rest_route(
			$namespace,
			$base_url . '(?P<id>\d+)',
			array(
				'args' => array(
					'id' => array(
						'description' => __( 'Unique user id of user.', 'woo-wallet' ),
						'type'        => 'integer',
						'required'    => true,
					),
				),
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'mwb_wsfw_user_wallet_balance' ),
				'permission_callback' => array( $this, 'mwb_wsfw_balance_permission_check' ),
			)
		);

		register_rest_route(
			$namespace,
			$base_url . '(?P<id>\d+)',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_user_wallet_balance' ),
				'permission_callback' => array( $this, 'mwb_wsfw_default_permission_check' ),
			)
		);

	}


	/**
	 * Begins validation process of api endpoint.
	 *
	 * @param   Array $request    All information related with the api request containing in this array.
	 * @return  Array   $result   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function mwb_wsfw_default_permission_check( $request ) {

		// Add rest api validation for each request.
		$result = true;
		return $result;
	}


	/**
	 * Begins execution of api endpoint.
	 *
	 * @param   Array $request    All information related with the api request containing in this array.
	 * @return  Array   $mwb_wsfw_response   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function mwb_wsfw_default_callback( $request ) {

		require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-wallet-system-for-woocommerce-api-process.php';
		$mwb_wsfw_api_obj = new Wallet_System_For_Woocommerce_Api_Process();
		$mwb_wsfw_resultsdata = $mwb_wsfw_api_obj->mwb_wsfw_default_process( $request );
		if ( is_array( $mwb_wsfw_resultsdata ) && isset( $mwb_wsfw_resultsdata['status'] ) && 200 == $mwb_wsfw_resultsdata['status'] ) {
			unset( $mwb_wsfw_resultsdata['status'] );
			$mwb_wsfw_response = new WP_REST_Response( $mwb_wsfw_resultsdata, 200 );
		} else {
			$mwb_wsfw_response = new WP_Error( $mwb_wsfw_resultsdata );
		}
		return $mwb_wsfw_response;
	}

	/**
	 * Begins validation process of api endpoint.
	 *
	 * @param   Array $request    All information related with the api request containing in this array.
	 * @return  Array   $result   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function mwb_wsfw_balance_permission_check( $request ) {

		// Add rest api validation for each request.
		if ( ! current_user_can( 'manage_woocommerce' )  ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you cannot list resources.', 'wallet-system-for-woocommerce' ), array( 'status' => 401 ) );
		}
		return true;
	}

	/**
	 * Begins execution of api endpoint.
	 *
	 * @param   object $request    All information related with the api request containing in this array.
	 * @return  Array   $mwb_wsfw_response   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function get_user_wallet_balance( $request ) {
		require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-wallet-system-for-woocommerce-api-process.php';
		$mwb_wsfw_api_obj = new Wallet_System_For_Woocommerce_Api_Process();
		$parameters = $request->get_params();
		$mwb_wsfw_resultsdata = $mwb_wsfw_api_obj->get_wallet_balance( $parameters['id'] );
		if ( is_array( $mwb_wsfw_resultsdata ) && isset( $mwb_wsfw_resultsdata['status'] ) && 200 == $mwb_wsfw_resultsdata['status'] ) {
			unset( $mwb_wsfw_resultsdata['status'] );
			$mwb_wsfw_response = new WP_REST_Response( $mwb_wsfw_resultsdata, 200 );
		} else {
			$mwb_wsfw_response = new WP_Error( $mwb_wsfw_resultsdata );
		}
		return $mwb_wsfw_response;
	}
	
}
