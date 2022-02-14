<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html for system status.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Template for showing information about system status.
global $wsfw_wps_wsfw_obj;
$wsfw_default_status    = $wsfw_wps_wsfw_obj->wps_wsfw_plug_system_status();
$wsfw_wordpress_details = is_array( $wsfw_default_status['wp'] ) && ! empty( $wsfw_default_status['wp'] ) ? $wsfw_default_status['wp'] : array();
$wsfw_php_details       = is_array( $wsfw_default_status['php'] ) && ! empty( $wsfw_default_status['php'] ) ? $wsfw_default_status['php'] : array();
?>
<div class="wps-wsfw-table-wrap">
	<div class="wps-col-wrap">
		<div id="wps-wsfw-table-inner-container" class="table-responsive mdc-data-table">
			<div class="mdc-data-table__table-container">
				<table class="wps-wsfw-table mdc-data-table__table wps-table" id="wps-wsfw-wp">
					<thead>
						<tr>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'WP Variables', 'wallet-system-for-woocommerce' ); ?></th>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'WP Values', 'wallet-system-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody class="mdc-data-table__content">
						<?php if ( is_array( $wsfw_wordpress_details ) && ! empty( $wsfw_wordpress_details ) ) { ?>
							<?php foreach ( $wsfw_wordpress_details as $wp_key => $wp_value ) { ?>
								<?php if ( isset( $wp_key ) && 'wp_users' != $wp_key ) { ?>
									<tr class="mdc-data-table__row">
										<td class="mdc-data-table__cell"><?php echo esc_html( $wp_key ); ?></td>
										<td class="mdc-data-table__cell"><?php echo esc_html( $wp_value ); ?></td>
									</tr>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="wps-col-wrap">
		<div id="wps-wsfw-table-inner-container" class="table-responsive mdc-data-table">
			<div class="mdc-data-table__table-container">
				<table class="wps-wsfw-table mdc-data-table__table wps-table" id="wps-wsfw-sys">
					<thead>
						<tr>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'System Variables', 'wallet-system-for-woocommerce' ); ?></th>
							<th class="mdc-data-table__header-cell"><?php esc_html_e( 'System Values', 'wallet-system-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody class="mdc-data-table__content">
						<?php if ( is_array( $wsfw_php_details ) && ! empty( $wsfw_php_details ) ) { ?>
							<?php foreach ( $wsfw_php_details as $php_key => $php_value ) { ?>
								<tr class="mdc-data-table__row">
									<td class="mdc-data-table__cell"><?php echo esc_html( $php_key ); ?></td>
									<td class="mdc-data-table__cell"><?php echo esc_html( $php_value ); ?></td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
