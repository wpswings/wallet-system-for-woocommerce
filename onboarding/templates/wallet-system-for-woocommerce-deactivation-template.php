<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Makewebbetter_Onboarding
 * @subpackage Makewebbetter_Onboarding/admin/onboarding
 */

global $pagenow, $wsfw_mwb_wsfw_obj;
if ( empty( $pagenow ) || 'plugins.php' != $pagenow ) {
	return false;
}

$wsfw_onboarding_form_deactivate = apply_filters( 'mwb_wsfw_deactivation_form_fields', array() );
?>
<?php if ( ! empty( $wsfw_onboarding_form_deactivate ) ) : ?>
	<div class="mwb-wsfw-dialog mdc-dialog mdc-dialog--scrollable">
		<div class="mwb-wsfw-on-boarding-wrapper-background mdc-dialog__container">
			<div class="mwb-wsfw-on-boarding-wrapper mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-content">
				<div class="mdc-dialog__content">
					<div class="mwb-wsfw-on-boarding-close-btn">
						<a href="#">
							<span class="wsfw-close-form material-icons mwb-wsfw-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span>
						</a>
					</div>

					<h3 class="mwb-wsfw-on-boarding-heading mdc-dialog__title"></h3>
					<p class="mwb-wsfw-on-boarding-desc"><?php esc_html_e( 'May we have a little info about why you are deactivating?', 'wallet-system-for-woocommerce' ); ?></p>
					<form action="#" method="post" class="mwb-wsfw-on-boarding-form">
						<?php
						$wsfw_onboarding_deactive_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_onboarding_form_deactivate );
						echo esc_html( $wsfw_onboarding_deactive_html );
						?>
						<div class="mwb-wsfw-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="mwb-wsfw-on-boarding-form-submit mwb-wsfw-on-boarding-form-verify ">
								<input type="submit" class="mwb-wsfw-on-boarding-submit mwb-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="mwb-wsfw-on-boarding-form-no_thanks">
								<a href="#" class="mwb-wsfw-deactivation-no_thanks mdc-button"><?php esc_html_e( 'Skip and Deactivate Now', 'wallet-system-for-woocommerce' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
