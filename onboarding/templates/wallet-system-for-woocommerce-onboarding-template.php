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

global $wsfw_wps_wsfw_obj;
$wsfw_onboarding_form_fields = apply_filters( 'wps_wsfw_on_boarding_form_fields', array() );
?>

<?php if ( ! empty( $wsfw_onboarding_form_fields ) ) : ?>
	<div class="wps-wsfw-dialog mdc-dialog mdc-dialog--scrollable">
		<div class="wps-wsfw-on-boarding-wrapper-background mdc-dialog__container">
			<div class="wps-wsfw-on-boarding-wrapper mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-content">
				<div class="mdc-dialog__content">
					<div class="wps-wsfw-on-boarding-close-btn">
						<a href="#"><span class="wsfw-close-form material-icons wps-wsfw-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span></a>
					</div>

					<h3 class="wps-wsfw-on-boarding-heading mdc-dialog__title"><?php esc_html_e( 'Welcome to WP Swings', 'wallet-system-for-woocommerce' ); ?> </h3>
					<p class="wps-wsfw-on-boarding-desc"><?php esc_html_e( 'We love making new friends! Subscribe below and we promise to keep you up-to-date with our latest new plugins, updates, awesome deals and a few special offers.', 'wallet-system-for-woocommerce' ); ?></p>

					<form action="#" method="post" class="wps-wsfw-on-boarding-form">
						<?php
						$wsfw_onboarding_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_onboarding_form_fields );
						echo esc_html( $wsfw_onboarding_html );
						?>
						<div class="wps-wsfw-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="wps-wsfw-on-boarding-form-submit wps-wsfw-on-boarding-form-verify ">
								<input type="submit" class="wps-wsfw-on-boarding-submit wps-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="wps-wsfw-on-boarding-form-no_thanks">
								<a href="#" class="wps-wsfw-on-boarding-no_thanks mdc-button" data-mdc-dialog-action="discard"><?php esc_html_e( 'Skip For Now', 'wallet-system-for-woocommerce' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
