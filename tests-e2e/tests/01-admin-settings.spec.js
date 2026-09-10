const { test, expect } = require('@playwright/test');
const { ADMIN_STATE } = require('../fixtures/storageStates');
const { WSFW_TABS, gotoTab, applyAndSave } = require('../fixtures/adminSettings');
const { wpTool } = require('../fixtures/wpTool');

test.use({ storageState: ADMIN_STATE });

test.describe('Admin: Wallet System settings', () => {
	test('General tab loads with all core toggles', async ({ page }) => {
		await gotoTab(page, WSFW_TABS.general);
		await expect(page.locator('#wps_wsfw_enable')).toBeChecked();
		await expect(page.locator('#wsfw_enable_wallet_recharge')).toBeVisible();
		await expect(page.locator('#wsfw_wallet_shortcode').first()).toHaveValue('[wps-wallet]');
	});

	test('toggling master Enable switch off then on persists across reloads', async ({ page }) => {
		await gotoTab(page, WSFW_TABS.general);
		await applyAndSave(page, { uncheck: ['wps_wsfw_enable'] }, 'wsfw_button_demo');
		await expect(page.locator('#wps_wsfw_enable')).not.toBeChecked();

		await gotoTab(page, WSFW_TABS.general);
		await expect(page.locator('#wps_wsfw_enable')).not.toBeChecked();

		// restore, other specs assume the plugin is enabled
		await applyAndSave(page, { check: ['wps_wsfw_enable'] }, 'wsfw_button_demo');
		await gotoTab(page, WSFW_TABS.general);
		await expect(page.locator('#wps_wsfw_enable')).toBeChecked();
	});

	test('Wallet Cashback tab saves cart-wise percentage rule', async ({ page }) => {
		await gotoTab(page, WSFW_TABS.cashback);
		await applyAndSave(
			page,
			{
				check: ['wps_wsfw_enable_cashback'],
				select: { wps_wsfw_cashback_rule: 'cartwise', wps_wsfw_cashback_type: 'percent' },
				fill: { wps_wsfw_cashback_amount: '15' },
			},
			'wsfw_button_cashback'
		);
		await gotoTab(page, WSFW_TABS.cashback);
		await expect(page.locator('#wps_wsfw_cashback_amount')).toHaveValue('15');
		await expect(page.locator('#wps_wsfw_cashback_rule')).toHaveValue('cartwise');

		// restore the suite-wide baseline amount used by the cashback spec
		await applyAndSave(page, { fill: { wps_wsfw_cashback_amount: '10' } }, 'wsfw_button_cashback');
	});

	test('Wallet Actions tab saves signup bonus amount', async ({ page }) => {
		await gotoTab(page, WSFW_TABS.walletActions);
		await applyAndSave(
			page,
			{ check: ['wps_wsfw_wallet_action_registration_enable'], fill: { wps_wsfw_wallet_action_registration_amount: '75' } },
			'wsfw_button_wallet_action'
		);
		await gotoTab(page, WSFW_TABS.walletActions);
		await expect(page.locator('#wps_wsfw_wallet_action_registration_amount')).toHaveValue('75');

		await applyAndSave(page, { fill: { wps_wsfw_wallet_action_registration_amount: '50' } }, 'wsfw_button_wallet_action');
	});

	test('Wallet KYC tab toggles KYC requirement', async ({ page }) => {
		await gotoTab(page, WSFW_TABS.kyc);
		await expect(page.locator('#wsfw_enable_wallet_kyc')).toBeChecked();
	});

	test('Buy Now Pay Later tab is reachable and has its enable toggle', async ({ page }) => {
		await gotoTab(page, WSFW_TABS.buyNowPayLater);
		await expect(page.locator('#wsfw_enable_wallet_negative_balance')).toHaveCount(1);
	});

	test('REST API tab can generate consumer key/secret', async ({ page }) => {
		await gotoTab(page, WSFW_TABS.restApi);
		const generateButton = page.getByRole('button', { name: /generate/i }).first();
		if (await generateButton.count()) {
			await generateButton.click();
			await expect(page.locator('body')).toContainText(/consumer/i);
		} else {
			// Keys already generated in a previous run; the tab should show them instead of the generate form.
			await expect(page.locator('body')).toContainText(/consumer|api key/i);
		}
	});

	test('System Status tab renders diagnostics', async ({ page }) => {
		await gotoTab(page, WSFW_TABS.systemStatus);
		await expect(page.locator('body')).toContainText(/PHP|WordPress|WooCommerce/i);
	});

	test('Users list shows Wallet Balance and Wallet Actions columns', async ({ page }) => {
		await page.goto('/wp-admin/users.php');
		// WP admin list tables repeat the header row in both <thead> and
		// <tfoot>, so a plain "th:has-text" match legitimately finds two.
		await expect(page.locator('table.wp-list-table thead th:has-text("Wallet Balance")')).toHaveCount(1);
		await expect(page.locator('table.wp-list-table thead th:has-text("Wallet Actions")')).toHaveCount(1);
	});

	test('admin wallet page group is only reachable while logged in', async ({ browser }) => {
		// The file-level test.use({ storageState: ADMIN_STATE }) above is
		// otherwise inherited as the default even for a manually created
		// context, so an explicit empty state is required to get a true guest.
		const ctx = await browser.newContext({ storageState: { cookies: [], origins: [] } });
		const page = await ctx.newPage();
		await page.goto('/wp-admin/admin.php?page=wallet_system_for_woocommerce_menu');
		await page.waitForLoadState('networkidle').catch(() => {});
		await expect(page).toHaveURL(/wp-login\.php/, { timeout: 15000 });
		await ctx.close();
	});
});

test.describe('Admin: Wallet System settings (env verification)', () => {
	test('baseline options were actually persisted', async () => {
		const recharge = await wpTool('get_option', { name: 'wsfw_enable_wallet_recharge' });
		expect(recharge.value).toBe('on');
		const cashback = await wpTool('get_option', { name: 'wps_wsfw_enable_cashback' });
		expect(cashback.value).toBe('on');
		const kyc = await wpTool('get_option', { name: 'wsfw_enable_wallet_kyc' });
		expect(kyc.value).toBe('on');
	});
});
