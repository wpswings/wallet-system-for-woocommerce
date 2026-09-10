const { test: setup, expect } = require('@playwright/test');
const { ADMIN, CUSTOMER_1, CUSTOMER_2 } = require('../config');
const { wpTool } = require('../fixtures/wpTool');
const { ADMIN_STATE, CUSTOMER_1_STATE, CUSTOMER_2_STATE } = require('../fixtures/storageStates');
const { WSFW_TABS, gotoTab, applyAndSave } = require('../fixtures/adminSettings');

async function login(page, username, password) {
	await page.goto('/wp-login.php');
	await page.locator('#user_login').fill(username);
	await page.locator('#user_pass').fill(password);
	await page.locator('#wp-submit').click();
	await expect(page.locator('#login_error')).toHaveCount(0);
}

setup('ensure test customers exist', async () => {
	await wpTool('ensure_customer', {
		login: CUSTOMER_1.login,
		email: CUSTOMER_1.email,
		password: CUSTOMER_1.password,
	});
	await wpTool('ensure_customer', {
		login: CUSTOMER_2.login,
		email: CUSTOMER_2.email,
		password: CUSTOMER_2.password,
	});
});

setup('authenticate as admin', async ({ page }) => {
	await login(page, ADMIN.username, ADMIN.password);
	await page.goto('/wp-admin/');
	await expect(page.locator('#wpadminbar')).toBeVisible();
	await page.context().storageState({ path: ADMIN_STATE });
});

// Freshly-activated plugin state has most feature toggles off (confirmed via
// wp-tool get_option before writing this). Turn on everything the suite
// exercises so every spec file is independently runnable regardless of order,
// and drive it through the real settings UI so this doubles as admin-settings
// coverage rather than a DB shortcut.
setup('configure plugin baseline settings', async ({ page }) => {
	await login(page, ADMIN.username, ADMIN.password);

	await gotoTab(page, WSFW_TABS.general);
	await applyAndSave(
		page,
		{
			check: [
				'wsfw_enable_wallet_recharge',
				'wsfw_wallet_payment_order_status_checkout',
				'wsfw_wallet_payment_refund_order_payment',
				'wsfw_wallet_partial_payment_method_enabled',
				'wps_wsfw_enable_email_notification_for_wallet_update',
			],
		},
		'wsfw_button_demo'
	);

	await gotoTab(page, WSFW_TABS.cashback);
	await applyAndSave(
		page,
		{
			check: ['wps_wsfw_enable_cashback'],
			select: { wps_wsfw_cashback_rule: 'cartwise', wps_wsfw_cashback_type: 'percent' },
			fill: {
				wps_wsfw_cashback_amount: '10',
				wps_wsfw_cart_amount_min: '1',
				wps_wsfw_cashback_amount_max: '100',
			},
		},
		'wsfw_button_cashback'
	);

	await gotoTab(page, WSFW_TABS.walletActions);
	await applyAndSave(
		page,
		{
			check: [
				'wps_wsfw_wallet_action_daily_enable',
				'wps_wsfw_wallet_action_registration_enable',
				'wps_wsfw_wallet_action_comment_enable',
			],
			fill: {
				wps_wsfw_wallet_action_daily_amount: '5',
				wps_wsfw_wallet_action_registration_amount: '50',
				wps_wsfw_wallet_action_comment_amount: '5',
				wps_wsfw_wallet_action_restrict_comment: '3',
			},
		},
		'wsfw_button_wallet_action'
	);

	await gotoTab(page, WSFW_TABS.kyc);
	await applyAndSave(page, { check: ['wsfw_enable_wallet_kyc'] }, 'wsfw_button_wallet_kyc_tab_option');
});

setup('authenticate as customer 1', async ({ page }) => {
	await login(page, CUSTOMER_1.login, CUSTOMER_1.password);
	await page.goto('/my-account/');
	await expect(page.locator('body.woocommerce-account')).toBeVisible();
	await page.context().storageState({ path: CUSTOMER_1_STATE });
});

setup('authenticate as customer 2', async ({ page }) => {
	await login(page, CUSTOMER_2.login, CUSTOMER_2.password);
	await page.goto('/my-account/');
	await expect(page.locator('body.woocommerce-account')).toBeVisible();
	await page.context().storageState({ path: CUSTOMER_2_STATE });
});
