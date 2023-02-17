<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://wpswings.com/
 * @since 1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}
global $wsfw_wps_wsfw_obj;
$wsfw_default_tabs = $wsfw_wps_wsfw_obj->wps_wsfw_plug_default_tabs();
$wsfw_tab_key = '';
?>
<header>
	<?php
	// desc - This hook is used for trial.
	do_action( 'wps_wsfw_settings_saved_notice' );
	?>
	<div class="wps-header-container wps-bg-white wps-r-8">
		<h1 class="wps-header-title"><?php echo esc_attr( __( 'WP Swings' ) ); ?></h1>
	</div>
</header>
<main class="wps-main wps-bg-white wps-r-8">
	<section class="wps-section">
		<div>
			<?php
				// desc - This hook is used for trial.
			do_action( 'wps_wsfw_before_common_settings_form' );
				// if submenu is directly clicked on woocommerce.
			$wsfw_genaral_settings = apply_filters(
				'wsfw_home_settings_array',
				array(

					array(
						'title'       => __( 'Enable Tracking', 'wallet-system-for-woocommerce' ),
						'type'        => 'radio-switch',
						'description' => '',
						'name'        => 'wsfw_enable_tracking',
						'id'          => 'wsfw_enable_tracking',
						'value'       => get_option( 'wsfw_enable_tracking' ),
						'class'       => 'wsfw-radio-switch-class',
						'options'     => array(
							'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
							'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
						),
					),
					array(
						'type'  => 'button',
						'id'    => 'wsfw_button_demo_welcome',
						'button_text' => __( 'Save', 'wallet-system-for-woocommerce' ),
						'class' => 'wsfw-button-class',
					),
				)
			);
			?>
			<form action="" method="POST" class="wps-wsfw-gen-section-form">
				<div class="wsfw-secion-wrap">
					<?php
					$wsfw_general_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_genaral_settings );
					echo esc_html( $wsfw_general_html );

					?>
					<input type="hidden" id="updatenonce" name="updatenonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	
				</div>
			</form>
			<?php
			do_action( 'wps_wsfw_before_common_settings_form' );
			$all_plugins = get_plugins();
			?>
		</div>
	</section>
	<style type="text/css">
		.cards {
			   display: flex;
			   flex-wrap: wrap;
			   padding: 20px 40px;
		}
		.card {
			flex: 1 0 518px;
			box-sizing: border-box;
			margin: 1rem 3.25em;
			text-align: center;
		}

	</style>
	<div class="centered">
		<section class="cards">
			<?php foreach ( get_plugins() as $key => $value ) : ?>
				<?php if ( 'WP Swings' === $value['Author'] ) : ?>
					<article class="card">
						<div class="container">
							<h4><b><?php echo wp_kses_post( $value['Name'] ); ?></b></h4> 
							<p><?php echo wp_kses_post( $value['Version'] ); ?></p> 
							<p><?php echo wp_kses_post( $value['Description'] ); ?></p>
						</div>
					</article>
				<?php endif; ?>
			<?php endforeach; ?>
		</section>
	</div>
