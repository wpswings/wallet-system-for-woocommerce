// Flat, distinctively-named routes (wallet-pos-login, not login) so they
// never collide with a real page/route on the host WordPress site.
export const ROUTES = {
	login: '/wallet-pos-login',
	registerClose: '/wallet-pos-register-close',
	sales: '/wallet-pos-sales',
	payment: '/wallet-pos-payment',
	receipt: ( orderId ) => `/wallet-pos-receipt/${ orderId }`,
	receiptPattern: '/wallet-pos-receipt/:orderId',
};

// The WP page hosting this app injects its own permalink path here (see
// Wallet_System_For_Woocommerce_Pos_Page::render_app_shell) before loading
// the app, so routing always matches wherever the admin-created "Wallet
// POS" page actually lives — no hardcoded path, no guessing. Falls back to
// '' in local dev (`npm run dev:pos`), where there's no WordPress page at
// all and the app is served at the dev server's own root.
export const BASENAME = ( typeof window !== 'undefined' && window.WSFW_POS_BASENAME ) || '';
