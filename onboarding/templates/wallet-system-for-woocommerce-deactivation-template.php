<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/onboarding/templates
 */

global $pagenow, $wsfw_wps_wsfw_obj;
if ( empty( $pagenow ) || 'plugins.php' != $pagenow ) {
	return false;
}

$wsfw_onboarding_form_deactivate = apply_filters( 'wps_wsfw_deactivation_form_fields', array() );
?>
<?php if ( ! empty( $wsfw_onboarding_form_deactivate ) ) : ?>
	<div class="wps-wsfw-dialog mdc-dialog mdc-dialog--scrollable">
		<div class="wps-wsfw-on-boarding-wrapper-background mdc-dialog__container">
			<div class="wps-wsfw-on-boarding-wrapper mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-content">
				<div class="mdc-dialog__content">
					<div class="wps-wsfw-on-boarding-close-btn">
						<a href="#">
							<span class="wsfw-close-form material-icons wps-wsfw-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span>
						</a>
					</div>

					<h3 class="wps-wsfw-on-boarding-heading mdc-dialog__title"></h3>
					<p class="wps-wsfw-on-boarding-desc"><?php esc_html_e( 'May we have a little info about why you are deactivating?', 'wallet-system-for-woocommerce' ); ?></p>
					<form action="#" method="post" class="wps-wsfw-on-boarding-form">
						<?php
						$wsfw_onboarding_deactive_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_onboarding_form_deactivate );
						echo esc_html( $wsfw_onboarding_deactive_html );
						?>
						<div class="wps-wsfw-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="wps-wsfw-on-boarding-form-submit wps-wsfw-on-boarding-form-verify ">
								<input type="submit" class="wps-wsfw-on-boarding-submit wps-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="wps-wsfw-on-boarding-form-no_thanks">
							<a href="#" class="wps-wsfw-deactivation-no_thanks mdc-button"><?php esc_html_e( 'Skip and Deactivate Now', 'wallet-system-for-woocommerce' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
