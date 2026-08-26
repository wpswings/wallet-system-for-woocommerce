const { test, expect } = require('@playwright/test');
const { CUSTOMER_1_STATE } = require('../fixtures/storageStates');
const { wpTool } = require('../fixtures/wpTool');

test.describe('Shortcodes', () => {
	let dashboardPageUrl;
	let amountPageUrl;

	test.beforeAll(async () => {
		const dashboardPage = await wpTool('ensure_page', {
			title: 'WPS E2E Wallet Dashboard Shortcode',
			content: '[wps-wallet]',
		});
		dashboardPageUrl = dashboardPage.url;

		const amountPage = await wpTool('ensure_page', {
			title: 'WPS E2E Wallet Amount Shortcode',
			content: '[wps-wallet-amount]',
		});
		amountPageUrl = amountPage.url;
	});

	test('[wps-wallet] shows a login prompt for guests', async ({ page }) => {
		await page.goto(dashboardPageUrl);
		await expect(page.locator('body')).toContainText(/log ?in|username/i);
	});

	test('[wps-wallet] renders the wallet dashboard for a logged-in customer', async ({ browser }) => {
		const ctx = await browser.newContext({ storageState: CUSTOMER_1_STATE });
		const page = await ctx.newPage();
		await page.goto(dashboardPageUrl);
		await expect(page.locator('body')).toContainText(/Wallet Balance/i);
		await ctx.close();
	});

	test('[wps-wallet-amount] renders the current balance for a logged-in customer', async ({ browser }) => {
		const user = await wpTool('get_user_by_login', { login: 'wpse2e_buyer1' });
		await wpTool('set_wallet_balance', { user_id: user.id, amount: '42.50' });

		const ctx = await browser.newContext({ storageState: CUSTOMER_1_STATE });
		const page = await ctx.newPage();
		await page.goto(amountPageUrl);
		await expect(page.locator('body')).toContainText('42.50');
		await ctx.close();
	});
});
