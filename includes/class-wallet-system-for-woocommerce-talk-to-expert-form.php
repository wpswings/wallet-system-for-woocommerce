<?php
/**
 * Talk to Expert form integration.
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the admin Talk to Expert modal and HubSpot submission.
 */
class Wallet_System_For_Woocommerce_Talk_To_Expert_Form {

	const AJAX_ACTION       = 'wps_wsfw_submit_talk_to_expert';
	const NONCE_ACTION      = 'wps_wsfw_talk_to_expert_nonce';
	const HUBSPOT_PORTAL_ID = '25444144';
	const HUBSPOT_FORM_ID   = 'eab973a7-5c65-4264-a31d-3b1b10b82c82';

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Plugin label used in HubSpot and UI.
	 *
	 * @return string
	 */
	public static function get_plugin_label() {
		return __( 'Wallet System For WooCommerce', 'wallet-system-for-woocommerce' );
	}

	/**
	 * Service checkbox options.
	 *
	 * @return array
	 */
	public function get_service_options() {
		return array(
			'seo_services'                         => __( 'SEO Services', 'wallet-system-for-woocommerce' ),
			'google_ads_setup_and_ga4_setup'       => __( 'Google Ads Setup And GA4 Setup', 'wallet-system-for-woocommerce' ),
			'speed_optimization'                   => __( 'Speed Optimization', 'wallet-system-for-woocommerce' ),
			'woocommerce_development_services'     => __( 'WooCommerce Development Services', 'wallet-system-for-woocommerce' ),
		);
	}

	/**
	 * Budget dropdown options.
	 *
	 * @return array
	 */
	public function get_budget_options() {
		return array(
			''                => __( 'Please Select', 'wallet-system-for-woocommerce' ),
			'$500 - $1000'    => '$500 - $1000',
			'$1001 - $5000'   => '$1001 - $5000',
			'$5001 - $10000'  => '$5001 - $10000',
			'$10001 - $15000' => '$10001 - $15000',
		);
	}

	/**
	 * Service cards shown in the sidebar card.
	 *
	 * @return array
	 */
	private function get_service_cards() {
		return array(
			array(
				'icon'        => 'SEO',
				'title'       => __( 'SEO Services', 'wallet-system-for-woocommerce' ),
				'description' => __( 'Improve rankings and organic traffic', 'wallet-system-for-woocommerce' ),
			),
			array(
				'icon'        => 'ADS',
				'title'       => __( 'Google Ads Setup And GA4 Setup', 'wallet-system-for-woocommerce' ),
				'description' => __( 'Run profitable ad campaigns', 'wallet-system-for-woocommerce' ),
			),
			array(
				'icon'        => 'SPD',
				'title'       => __( 'Speed Optimization', 'wallet-system-for-woocommerce' ),
				'description' => __( 'Faster store, happier customers', 'wallet-system-for-woocommerce' ),
			),
			array(
				'icon'        => 'DEV',
				'title'       => __( 'WooCommerce Development Services', 'wallet-system-for-woocommerce' ),
				'description' => __( 'Custom solution for your store needs', 'wallet-system-for-woocommerce' ),
			),
		);
	}

	/**
	 * Render sidebar card.
	 *
	 * @return void
	 */
	public function render_sidebar_card() {
		?>
		<div class="wps-wallet-side-card wps-wallet-side-card--expert">
			<div class="wps-wallet-expert-card__header">
				<h2><?php esc_html_e( 'Grow Your Store With WP Swings', 'wallet-system-for-woocommerce' ); ?></h2>
				<span aria-hidden="true">&#9733;</span>
			</div>
			<p><?php esc_html_e( 'Expert WooCommerce services for performance, campaigns, custom development, and store growth.', 'wallet-system-for-woocommerce' ); ?></p>
			<div class="wps-wallet-expert-services">
				<?php foreach ( $this->get_service_cards() as $service_card ) { ?>
					<div class="wps-wallet-expert-service">
						<span class="wps-wallet-expert-service__icon" aria-hidden="true"><?php echo esc_html( $service_card['icon'] ); ?></span>
						<div>
							<h3><?php echo esc_html( $service_card['title'] ); ?></h3>
							<p><?php echo esc_html( $service_card['description'] ); ?></p>
						</div>
						<span aria-hidden="true">&rsaquo;</span>
					</div>
				<?php } ?>
			</div>
			<button type="button" class="wps-wallet-side-button wps-wallet-expert-open" data-wps-wsfw-open-expert-modal>
				<?php esc_html_e( 'Talk to an Expert', 'wallet-system-for-woocommerce' ); ?>
			</button>
			<p class="wps-wallet-expert-card__footer"><?php esc_html_e( 'Services by WP Swings', 'wallet-system-for-woocommerce' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render modal markup once.
	 *
	 * @return void
	 */
	public function render_modal() {
		$services = $this->get_service_options();
		$budgets  = $this->get_budget_options();
		?>
		<div class="wps-wallet-expert-modal" data-wps-wsfw-expert-modal hidden>
			<div class="wps-wallet-expert-modal__backdrop" data-wps-wsfw-close-expert-modal></div>
			<div class="wps-wallet-expert-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="wps-wallet-expert-modal-title">
				<button type="button" class="wps-wallet-expert-modal__close" data-wps-wsfw-close-expert-modal>
					<span class="screen-reader-text"><?php esc_html_e( 'Close expert request form', 'wallet-system-for-woocommerce' ); ?></span>
					&times;
				</button>
				<div class="wps-wallet-expert-form-wrap" data-wps-wsfw-expert-form-wrap>
					<span class="wps-wallet-expert-modal__eyebrow"><?php esc_html_e( 'Talk to an Expert', 'wallet-system-for-woocommerce' ); ?></span>
					<h2 id="wps-wallet-expert-modal-title"><?php esc_html_e( 'Tell us what you need help with', 'wallet-system-for-woocommerce' ); ?></h2>
					<p><?php esc_html_e( 'Share a few details and our team will get back to you with the right WooCommerce support.', 'wallet-system-for-woocommerce' ); ?></p>
					<form class="wps-wallet-expert-form" data-wps-wsfw-expert-form>
						<div class="wps-wallet-expert-form__grid">
							<label>
								<span><?php esc_html_e( 'First Name', 'wallet-system-for-woocommerce' ); ?></span>
								<input type="text" name="firstname" required>
							</label>
							<label>
								<span><?php esc_html_e( 'Last Name', 'wallet-system-for-woocommerce' ); ?></span>
								<input type="text" name="lastname" required>
							</label>
							<label>
								<span><?php esc_html_e( 'Email', 'wallet-system-for-woocommerce' ); ?></span>
								<input type="email" name="email" required>
							</label>
							<label>
								<span><?php esc_html_e( 'Phone', 'wallet-system-for-woocommerce' ); ?></span>
								<input type="tel" name="phone">
							</label>
						</div>
						<fieldset>
							<legend><?php esc_html_e( 'What services do you need help with?', 'wallet-system-for-woocommerce' ); ?></legend>
							<div class="wps-wallet-expert-form__checks">
								<?php foreach ( $services as $service_key => $service_label ) { ?>
									<label>
										<input type="checkbox" name="services[]" value="<?php echo esc_attr( $service_key ); ?>">
										<span><?php echo esc_html( $service_label ); ?></span>
									</label>
								<?php } ?>
							</div>
						</fieldset>
						<label>
							<span><?php esc_html_e( 'Budget', 'wallet-system-for-woocommerce' ); ?></span>
							<select name="budget">
								<option value=""><?php esc_html_e( 'Select budget', 'wallet-system-for-woocommerce' ); ?></option>
								<?php foreach ( $budgets as $budget_key => $budget_label ) { ?>
									<option value="<?php echo esc_attr( $budget_key ); ?>"><?php echo esc_html( $budget_label ); ?></option>
								<?php } ?>
							</select>
						</label>
						<label>
							<span><?php esc_html_e( 'Message', 'wallet-system-for-woocommerce' ); ?></span>
							<textarea name="message" rows="4" required></textarea>
						</label>
						<div class="wps-wallet-expert-form__notice" data-wps-wsfw-expert-message aria-live="polite"></div>
						<button type="submit" class="wps-wallet-expert-submit">
							<span data-wps-wsfw-submit-label><?php esc_html_e( 'Submit Request', 'wallet-system-for-woocommerce' ); ?></span>
						</button>
					</form>
				</div>
				<div class="wps-wallet-expert-success" data-wps-wsfw-expert-success hidden>
					<span aria-hidden="true">&#10003;</span>
					<h2><?php esc_html_e( 'Request sent successfully', 'wallet-system-for-woocommerce' ); ?></h2>
					<p><?php esc_html_e( 'Our team will review your details and contact you soon.', 'wallet-system-for-woocommerce' ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX handler.
	 *
	 * @return void
	 */
	public function submit_form_ajax() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You are not allowed to submit this request.', 'wallet-system-for-woocommerce' ) ),
				403
			);
		}

		$form_data = isset( $_POST['form_data'] ) ? wp_unslash( $_POST['form_data'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$payload   = json_decode( $form_data, true );

		if ( ! is_array( $payload ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid request data.', 'wallet-system-for-woocommerce' ) ),
				400
			);
		}

		$sanitized = $this->sanitize_payload( $payload );
		$response  = $this->submit_to_hubspot( $sanitized );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error(
				array( 'message' => $response->get_error_message() ),
				500
			);
		}

		wp_send_json_success(
			array( 'message' => __( 'Your request has been submitted successfully.', 'wallet-system-for-woocommerce' ) )
		);
	}

	/**
	 * Sanitize submitted payload.
	 *
	 * @param array $payload Raw payload.
	 * @return array
	 */
	private function sanitize_payload( $payload ) {
		$service_options = $this->get_service_options();
		$budget_options  = $this->get_budget_options();
		$services        = isset( $payload['services'] ) && is_array( $payload['services'] ) ? $payload['services'] : array();
		$services        = array_values(
			array_intersect(
				array_map( 'sanitize_key', $services ),
				array_keys( $service_options )
			)
		);
		$budget          = isset( $payload['budget'] ) ? sanitize_text_field( $payload['budget'] ) : '';

		return array(
			'firstname' => isset( $payload['firstname'] ) ? sanitize_text_field( $payload['firstname'] ) : '',
			'lastname'  => isset( $payload['lastname'] ) ? sanitize_text_field( $payload['lastname'] ) : '',
			'email'     => isset( $payload['email'] ) ? sanitize_email( $payload['email'] ) : '',
			'phone'     => isset( $payload['phone'] ) ? sanitize_text_field( $payload['phone'] ) : '',
			'services'  => $services,
			'budget'    => array_key_exists( $budget, $budget_options ) ? $budget : '',
			'message'   => isset( $payload['message'] ) ? sanitize_textarea_field( $payload['message'] ) : '',
		);
	}

	/**
	 * Submit sanitized payload to HubSpot.
	 *
	 * @param array $data Sanitized data.
	 * @return true|WP_Error
	 */
	private function submit_to_hubspot( $data ) {
		if ( empty( $data['firstname'] ) || empty( $data['lastname'] ) || empty( $data['email'] ) || empty( $data['message'] ) ) {
			return new WP_Error( 'missing_required_fields', __( 'Please complete all required fields.', 'wallet-system-for-woocommerce' ) );
		}

		if ( ! is_email( $data['email'] ) ) {
			return new WP_Error( 'invalid_email', __( 'Please enter a valid email address.', 'wallet-system-for-woocommerce' ) );
		}

		$endpoint = sprintf(
			'https://api.hsforms.com/submissions/v3/integration/submit/%1$s/%2$s',
			rawurlencode( self::HUBSPOT_PORTAL_ID ),
			rawurlencode( self::HUBSPOT_FORM_ID )
		);

		$fields = array(
			'firstname'                                => $data['firstname'],
			'lastname'                                 => $data['lastname'],
			'email'                                    => $data['email'],
			'phone'                                    => $data['phone'],
			'what_services_do_you_need_help_with'      => implode( ';', $data['services'] ),
			'budget'                                   => $data['budget'],
			'message'                                  => $data['message'],
			'currency'                                 => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : '',
			'org_plugin_name'                          => self::get_plugin_label(),
			'company'                                  => get_bloginfo( 'name' ),
			'website'                                  => home_url(),
			'country'                                  => $this->get_store_country(),
			'annualrevenue'                            => $this->get_annual_revenue(),
		);

		$hubspot_fields = array();
		foreach ( $fields as $name => $value ) {
			if ( '' === $value || null === $value ) {
				continue;
			}
			$hubspot_fields[] = array(
				'name'  => $name,
				'value' => (string) $value,
			);
		}

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout' => 20,
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'fields'  => $hubspot_fields,
						'context' => array(
							'pageUri'   => admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu' ),
							'pageName'  => self::get_plugin_label(),
							'ipAddress' => $this->get_client_ip(),
						),
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 > $response_code || 300 <= $response_code ) {
			return new WP_Error( 'hubspot_submission_failed', __( 'Unable to submit the request right now. Please try again.', 'wallet-system-for-woocommerce' ) );
		}

		return true;
	}

	/**
	 * Get WooCommerce store country.
	 *
	 * @return string
	 */
	private function get_store_country() {
		if ( function_exists( 'wc_get_base_location' ) ) {
			$location = wc_get_base_location();
			return isset( $location['country'] ) ? sanitize_text_field( $location['country'] ) : '';
		}

		return '';
	}

	/**
	 * Get paid order revenue for the last 365 days.
	 *
	 * @return string
	 */
	private function get_annual_revenue() {
		global $wpdb;

		$order_stats_table = $wpdb->prefix . 'wc_order_stats';
		$table_exists      = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $order_stats_table ) );

		if ( $table_exists === $order_stats_table ) {
			$total = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT SUM(total_sales) FROM {$order_stats_table} WHERE status IN ('wc-processing', 'wc-completed') AND date_created >= %s",
					gmdate( 'Y-m-d H:i:s', strtotime( '-365 days' ) )
				)
			);

			return (string) round( (float) $total, 2 );
		}

		if ( ! function_exists( 'wc_get_orders' ) ) {
			return '';
		}

		$orders = wc_get_orders(
			array(
				'limit'        => -1,
				'status'       => array( 'wc-processing', 'wc-completed' ),
				'date_created' => '>' . gmdate( 'Y-m-d', strtotime( '-365 days' ) ),
				'return'       => 'objects',
			)
		);

		$total = 0;
		foreach ( $orders as $order ) {
			if ( is_a( $order, 'WC_Order' ) ) {
				$total += (float) $order->get_total();
			}
		}

		return (string) round( $total, 2 );
	}

	/**
	 * Resolve client IP.
	 *
	 * @return string
	 */
	private function get_client_ip() {
		$keys = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );

		foreach ( $keys as $key ) {
			if ( empty( $_SERVER[ $key ] ) ) {
				continue;
			}

			$value = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
			$ip    = trim( explode( ',', $value )[0] );

			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}

		return '';
	}
}
