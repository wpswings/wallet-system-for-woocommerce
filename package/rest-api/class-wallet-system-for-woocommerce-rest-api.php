<?php
/**
 * The file that defines the core plugin api class
 *
 * A class definition that includes api's endpoints and functions used across the plugin
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/package/rest-api
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
 * @subpackage Wallet_System_For_Woocommerce/package/rest-api
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Wallet_System_For_Woocommerce_Rest_Api extends WP_REST_Controller {

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
		$this->version     = $version;

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
	public function wps_wsfw_add_endpoint() {
		// Show all users.
		register_rest_route(
			$this->namespace,
			$this->base_url . 'users',
			array(
				'args'                => array(
					'consumer_key'    => array(
						'description' => __( 'Merchant Consumer Key.', 'wallet-system-for-woocommerce' ),
						'type'        => 'string',
						'required'    => true,
					),
					'consumer_secret' => array(
						'description' => __( 'Merchant Consumer Secret', 'wallet-system-for-woocommerce' ),
						'type'        => 'string',
						'required'    => true,
					),
					'context'         => array(
						'default' => 'view',
					),
				),
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'wps_wsfw_users' ),
				'permission_callback' => array( $this, 'wps_wsfw_get_permission_check' ),
			)
		);
		// For getting particular user wallet details.
		register_rest_route(
			$this->namespace,
			$this->base_url . '(?P<id>\d+)',
			array(
				'args' => array(
					'id' => array(
						'description' => __( 'Unique user id of user.', 'wallet-system-for-woocommerce' ),
						'type'        => 'integer',
						'required'    => true,
					),
				),
				array(
					'args'                => array(
						'consumer_key'    => array(
							'description' => __( 'Merchant Consumer Key.', 'wallet-system-for-woocommerce' ),
							'type'        => 'string',
							'required'    => true,
						),
						'consumer_secret' => array(
							'description' => __( 'Merchant Consumer Secret', 'wallet-system-for-woocommerce' ),
							'type'        => 'string',
							'required'    => true,
						),
						'context'         => array(
							'default' => 'view',
						),
					),
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'wps_wsfw_user_wallet_balance' ),
					'permission_callback' => array( $this, 'wps_wsfw_get_permission_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->base_url . '(?P<id>[\d]+)',
			array(
				'args' => array(
					'id' => array(
						'description' => __( 'Unique user id of user.', 'wallet-system-for-woocommerce' ),
						'type'        => 'integer',
						'required'    => true,
					),
				),
				// Update wallet of user.
				array(
					'args'                => array(
						'consumer_key'       => array(
							'description' => __( 'Merchant Consumer Key.', 'wallet-system-for-woocommerce' ),
							'type'        => 'string',
							'required'    => true,
						),
						'consumer_secret'    => array(
							'description' => __( 'Merchant Consumer Secret', 'wallet-system-for-woocommerce' ),
							'type'        => 'string',
							'required'    => true,
						),
						'amount'             => array(
							'description' => __( 'Wallet transaction amount.', 'wallet-system-for-woocommerce' ),
							'type'        => 'number',
							'required'    => true,
						),
						'action'             => array(
							'type'        => 'string',
							'description' => __( 'Wallet transaction type.', 'wallet-system-for-woocommerce' ),
							'required'    => true,
						),
						'transaction_detail' => array(
							'type'        => 'string',
							'description' => __( 'Wallet transaction details.', 'wallet-system-for-woocommerce' ),
							'required'    => true,
						),
						'payment_method'     => array(
							'type'        => 'string',
							'description' => __( 'Payment method used.', 'wallet-system-for-woocommerce' ),
						),
						'note'               => array(
							'description' => __( 'Note during wallet transfer.', 'wallet-system-for-woocommerce' ),
							'type'        => 'string',
						),
						'order_id'           => array(
							'description' => __( 'If wallet amount is deducted when wallet used as payment gateway.', 'wallet-system-for-woocommerce' ),
							'type'        => 'integer',
						),
					),
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'wps_wsfw_edit_wallet_balance' ),
					'permission_callback' => array( $this, 'wps_wsfw_update_item_permissions_check' ),
				),

			)
		);

		// Show transactions of particular user.
		register_rest_route(
			$this->namespace,
			$this->base_url . 'transactions/(?P<id>\d+)',
			array(
				'args'                => array(
					'id'              => array(
						'description' => __( 'Unique user id of user.', 'wallet-system-for-woocommerce' ),
						'type'        => 'integer',
						'required'    => true,
					),
					'consumer_key'    => array(
						'description' => __( 'Merchant Consumer Key.', 'wallet-system-for-woocommerce' ),
						'type'        => 'string',
						'required'    => true,
					),
					'consumer_secret' => array(
						'description' => __( 'Merchant Consumer Secret', 'wallet-system-for-woocommerce' ),
						'type'        => 'string',
						'required'    => true,
					),
					'context'         => array(
						'default' => 'view',
					),
				),
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'wps_wsfw_user_wallet_transactions' ),
				'permission_callback' => array( $this, 'wps_wsfw_get_permission_check' ),
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
	public function wps_wsfw_get_permission_check( $request ) {
		$parameters    = $request->get_params();
		$rest_api_keys = get_option( 'wps_wsfw_wallet_rest_api_keys', '' );
		if ( ! empty( $rest_api_keys ) && is_array( $rest_api_keys ) ) {
			$key    = $parameters['consumer_key'];
			$secret = $parameters['consumer_secret'];
			if ( $key === $rest_api_keys['consumer_key'] && $secret === $rest_api_keys['consumer_secret'] ) {
				return true;
			}
			return new WP_Error( 'rest_forbidden', esc_html__( 'Sorry, your key details are incorrect.', 'wallet-system-for-woocommerce' ), array( 'status' => 401 ) );
		}
		return false;
	}

	/**
	 * Begins validation process of api endpoint.
	 *
	 * @param Array $request All information related with the api request containing in this array.
	 * @return  Boolean
	 */
	public function wps_wsfw_update_item_permissions_check( $request ) {
		$data = json_decode( $request->get_body() );
		$rest_api_keys = get_option( 'wps_wsfw_wallet_rest_api_keys', '' );
		if ( ! empty( $rest_api_keys ) && is_array( $rest_api_keys ) ) {
			$key    = $data->consumer_key;
			$secret = $data->consumer_secret;
			if ( $key === $rest_api_keys['consumer_key'] && $secret === $rest_api_keys['consumer_secret'] ) {
				return true;
			}
			return new WP_Error( 'rest_forbidden', esc_html__( 'Sorry, your key details are incorrect.', 'wallet-system-for-woocommerce' ), array( 'status' => 401 ) );
		}
		return false;
	}

	/**
	 * Returns users details
	 *
	 * @param Array $request All information related with the api request containing in this array.
	 * @return Array
	 */
	public function wps_wsfw_users( $request ) {
		require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-wallet-system-for-woocommerce-api-process.php';
		$wps_wsfw_api_obj     = new Wallet_System_For_Woocommerce_Api_Process();
		$wps_wsfw_resultsdata = $wps_wsfw_api_obj->wps_wsfw_get_users();
		if ( is_array( $wps_wsfw_resultsdata ) && isset( $wps_wsfw_resultsdata['status'] ) && 200 === $wps_wsfw_resultsdata['status'] ) {
			unset( $wps_wsfw_resultsdata['status'] );
			$wps_wsfw_response = new WP_REST_Response( $wps_wsfw_resultsdata['data'], 200 );
		} else {
			$wps_wsfw_response = new WP_Error( $wps_wsfw_resultsdata );
		}
		return $wps_wsfw_response;
	}

	/**
	 * Returns user's current wallet balance
	 *
	 * @param Array $request All information related with the api request containing in this array.
	 * @return Array
	 */
	public function wps_wsfw_user_wallet_balance( $request ) {
		require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-wallet-system-for-woocommerce-api-process.php';
		$wps_wsfw_api_obj     = new Wallet_System_For_Woocommerce_Api_Process();
		$parameters           = $request->get_params();
		$wps_wsfw_resultsdata = $wps_wsfw_api_obj->get_wallet_balance( $parameters['id'] );
		if ( is_array( $wps_wsfw_resultsdata ) && isset( $wps_wsfw_resultsdata['status'] ) && 200 === $wps_wsfw_resultsdata['status'] ) {
			unset( $wps_wsfw_resultsdata['status'] );
			$wps_wsfw_response = new WP_REST_Response( $wps_wsfw_resultsdata['data'], 200 );
		} else {
			$wps_wsfw_response = new WP_Error( $wps_wsfw_resultsdata );
		}
		return $wps_wsfw_response;
	}

	/**
	 * Edit user wallet( credit/debit )
	 *
	 * @param Array $request All information related with the api request containing in this array.
	 * @return Array
	 */
	public function wps_wsfw_edit_wallet_balance( $request ) {
		require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-wallet-system-for-woocommerce-api-process.php';
		$wps_wsfw_api_obj = new Wallet_System_For_Woocommerce_Api_Process();
		$parameters       = $request->get_params();
		if ( isset( $parameters['amount'] ) && ! empty( $parameters['amount'] ) ) {
			$wps_wsfw_resultsdata = $wps_wsfw_api_obj->update_wallet_balance( $parameters );
			if ( is_array( $wps_wsfw_resultsdata ) && isset( $wps_wsfw_resultsdata['status'] ) && 200 === $wps_wsfw_resultsdata['status'] ) {
				unset( $wps_wsfw_resultsdata['status'] );
				$wps_wsfw_response = new WP_REST_Response( $wps_wsfw_resultsdata['data'], 200 );
			} else {
				$wps_wsfw_response = new WP_Error( $wps_wsfw_resultsdata );
			}
		} else {
			$wps_wsfw_response = new WP_REST_Response( array( 'response' => 'Amount should be greater than 0' ), 401 );
		}
		return $wps_wsfw_response;
	}

	/**
	 * Returns user's all wallet transaction details
	 *
	 * @param Array $request All information related with the api request containing in this array.
	 * @return Array
	 */
	public function wps_wsfw_user_wallet_transactions( $request ) {
		require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-wallet-system-for-woocommerce-api-process.php';
		$wps_wsfw_api_obj     = new Wallet_System_For_Woocommerce_Api_Process();
		$parameters           = $request->get_params();
		$wps_wsfw_resultsdata = $wps_wsfw_api_obj->get_user_wallet_transactions( $parameters['id'] );
		if ( is_array( $wps_wsfw_resultsdata ) && isset( $wps_wsfw_resultsdata['status'] ) && 200 === $wps_wsfw_resultsdata['status'] ) {
			unset( $wps_wsfw_resultsdata['status'] );
			$wps_wsfw_response = new WP_REST_Response( $wps_wsfw_resultsdata['data'], 200 );
		} else {
			$wps_wsfw_response = new WP_Error( $wps_wsfw_resultsdata );
		}

		return $wps_wsfw_response;
	}

}
