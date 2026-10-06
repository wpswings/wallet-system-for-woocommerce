/**
 * Wallet Payment Gateway Block for WooCommerce Blocks
 */

const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
const { getSetting } = window.wc.wcSettings;
const { createElement } = window.wp.element;
const { __ } = window.wp.i18n;
const { decodeEntities } = window.wp.htmlEntities;

const settings = getSetting('woocommerce_wallet_gateway_settings', {});
const gatewayData = typeof CustomGatewayData !== 'undefined' ? CustomGatewayData : {};

const defaultLabel = __( gatewayData.title || 'Wallet Payment', 'wallet-system-for-woocommerce' );
const label = decodeEntities(settings.title) || defaultLabel;

/**
 * Content component - shows payment method description and balance
 */
const Content = () => {
	const walletBalance = gatewayData.wallet_balance_formatted || '';
	const hasSufficientBalance = gatewayData.has_sufficient_balance || false;
	const description = gatewayData.description || '';

	return createElement(
		'div',
		{ className: 'wps-wallet-payment-content' },
		description && createElement(
			'p',
			{ className: 'wps-wallet-description' },
			decodeEntities(description)
		),
		hasSufficientBalance && createElement(
			'p',
			{
				className: 'wps-wallet-balance-message',
				style: { color: '#2e7d32', fontWeight: '500' }
			},
			__('Your balance covers this order in full. Nothing else to pay.', 'wallet-system-for-woocommerce')
		)
	);
};

/**
 * Label component - shows payment method name with balance
 */
const Label = (props) => {
	const { PaymentMethodLabel } = props.components;
	const walletBalance = gatewayData.wallet_balance_formatted || '';

	return createElement(
		'div',
		{ className: 'wps-wallet-payment-label', style: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', width: '100%' } },
		createElement(PaymentMethodLabel, { text: label }),
		walletBalance && createElement(
			'span',
			{
				className: 'wps-wallet-balance',
				style: { color: '#000000', fontWeight: 'bold', fontSize: '14px' }
			},
			__('Balance ', 'wallet-system-for-woocommerce') + walletBalance
		)
	);
};

/**
 * Wallet Payment Method Config
 */
const WalletPaymentMethod = {
	name: 'wps_wcb_wallet_payment_gateway',
	label: createElement(Label, null),
	content: createElement(Content, null),
	edit: createElement(Content, null),
	canMakePayment: () => true,
	ariaLabel: label,
	supports: {
		features: gatewayData.supports || []
	}
};

// Register the payment method
registerPaymentMethod(WalletPaymentMethod);
