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

	protected $current_user;

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
		
		// for getting particular user wallet details
		register_rest_route(
			$this->namespace,
			$this->base_url . '(?P<id>\d+)',
			array(
				array(
					'args' => array(
						'id' => array(
							'description' => __( 'Unique user id of user.', 'wallet-system-for-woocommerce' ),
							'type'        => 'integer',
							'required'    => true,
						),
						'context' => array(
							'default' => 'view',
						),
					),
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'mwb_wsfw_user_wallet_balance' ),
					'permission_callback' => array( $this, 'mwb_wsfw_get_permission_check' ),
				),
				// update wallet of user
				array(
					'args' => array(
						'id' => array(
							'description' => __( 'Unique user id of user.', 'wallet-system-for-woocommerce' ),
							'type'        => 'integer',
							'required'    => true,
						),
						'amount' => array(
							'description' => __( 'Wallet transaction amount.', 'wallet-system-for-woocommerce' ),
							'type'        => 'number',
							'required'    => true,
						),
						'transaction_type' => array(
							'type'        => 'string',
							'description' => __( 'Wallet transaction type.', 'wallet-system-for-woocommerce' ),
							'required'    => true,
						),
						'payment_method' => array(
							'type'        => 'string',
							'description' => __( 'Payment method used.', 'wallet-system-for-woocommerce' ),
							'required'    => true,
						),
						'note' => array(
							'description' => __( 'Note during wallet transfer.', 'wallet-system-for-woocommerce' ),
							'type'        => 'string',
						),
						'order_id' => array(
							'description' => __( 'If amount is deducted when wallet used as payment gateway.', 'wallet-system-for-woocommerce' ),
							'type'        => 'number',
						),
					),
					'methods'  => WP_REST_Server::EDITABLE,
					'callback' => array( $this, 'mwb_wsfw_edit_wallet_balance' ),
					'permission_callback' => array( $this, 'mwb_wsfw_update_item_permissions_check' ),
				)
				
			)
		);

		// show transactions of particular user
		register_rest_route(
			$this->namespace,
			$this->base_url . 'transactions/(?P<id>\d+)',
			array(
				'args' => array(
					'id' => array(
						'description' => __( 'Unique user id of user.', 'wallet-system-for-woocommerce' ),
						'type'        => 'integer',
						'required'    => true,
					),
					'context' => array(
						'default' => 'view',
					),
				),
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'mwb_wsfw_user_wallet_transactions' ),
				'permission_callback' => array( $this, 'mwb_wsfw_get_permission_check' ),
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
	public function mwb_wsfw_get_permission_check( $request ) {

		// Add rest api validation for each request.
		// if ( ! current_user_can( 'edit_something' )  ) {
		// 	return new WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you cannot list resources.', 'wallet-system-for-woocommerce' ), array( 'status' => 401 ) );
		// }
		return current_user_can('edit_others_posts');
		//return true;
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $request
	 * @return void
	 */
	public function mwb_wsfw_update_item_permissions_check( $request ) {
		return user_can($this->current_user, 'some_capability');
		// if ( ! current_user_can( 'edit_something' )  ) {
		// 	return new WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you cannot list resources.', 'wallet-system-for-woocommerce' ), array( 'status' => 401 ) );
		// }
		return true;
	}

	/**
	 * Returns user's current wallet balance
	 *
	 * @param [type] $request
	 * @return void
	 */
	public function mwb_wsfw_user_wallet_balance( $request ) {
		require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-wallet-system-for-woocommerce-api-process.php';
		$mwb_wsfw_api_obj = new Wallet_System_For_Woocommerce_Api_Process();
		$parameters = $request->get_params();
		$mwb_wsfw_resultsdata = $mwb_wsfw_api_obj->get_wallet_balance( $parameters['id'] );
		if ( is_array( $mwb_wsfw_resultsdata ) && isset( $mwb_wsfw_resultsdata['status'] ) && 200 == $mwb_wsfw_resultsdata['status'] ) {
			unset( $mwb_wsfw_resultsdata['status'] );
			$mwb_wsfw_response = new WP_REST_Response( $mwb_wsfw_resultsdata['data'], 200 );
		} else {
			$mwb_wsfw_response = new WP_Error( $mwb_wsfw_resultsdata );
		}
		return $mwb_wsfw_response;
	}

	/**
	 * Edit user wallet( credit/debit )
	 *
	 * @param [type] $request
	 * @return void
	 */
	public function mwb_wsfw_edit_wallet_balance( $request ) {
		require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-wallet-system-for-woocommerce-api-process.php';
		$mwb_wsfw_api_obj = new Wallet_System_For_Woocommerce_Api_Process();
		$parameters = $request->get_params();
		if ( isset( $parameters['amount'] ) && ! empty( $parameters['amount'] ) ) { 
			$mwb_wsfw_resultsdata = $mwb_wsfw_api_obj->update_wallet_balance( $parameters );
			if ( is_array( $mwb_wsfw_resultsdata ) && isset( $mwb_wsfw_resultsdata['status'] ) && 200 == $mwb_wsfw_resultsdata['status'] ) {
				unset( $mwb_wsfw_resultsdata['status'] );
				$mwb_wsfw_response = new WP_REST_Response( $mwb_wsfw_resultsdata['data'], 200 );
			} else {
				$mwb_wsfw_response = new WP_Error( $mwb_wsfw_resultsdata );
			}
		} else {
			$mwb_wsfw_response = new WP_REST_Response( array( 'response' => 'Amount should be greater than 0' ), 401 );
		}
		return $mwb_wsfw_response;
	}

	/**
	 * Returns user's all wallet transaction details
	 *
	 * @param [type] $request
	 * @return void
	 */
	public function mwb_wsfw_user_wallet_transactions( $request ) {
		require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'package/rest-api/version1/class-wallet-system-for-woocommerce-api-process.php';
		$mwb_wsfw_api_obj = new Wallet_System_For_Woocommerce_Api_Process();
		$parameters = $request->get_params();
		$mwb_wsfw_resultsdata = $mwb_wsfw_api_obj->get_user_wallet_transactions( $parameters['id'] );
		if ( is_array( $mwb_wsfw_resultsdata ) && isset( $mwb_wsfw_resultsdata['status'] ) && 200 == $mwb_wsfw_resultsdata['status'] ) {
			unset( $mwb_wsfw_resultsdata['status'] );
			$mwb_wsfw_response = new WP_REST_Response( $mwb_wsfw_resultsdata['data'], 200 );
		} else {
			$mwb_wsfw_response = new WP_Error( $mwb_wsfw_resultsdata );
		} 

		return $mwb_wsfw_response;
	}
	 
}
