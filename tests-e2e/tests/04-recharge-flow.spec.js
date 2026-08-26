const { test, expect } = require('@playwright/test');
const { ADMIN_STATE, CUSTOMER_1_STATE } = require('../fixtures/storageStates');
const { emptyCart, fillBillingDetails, placeOrder } = require('../fixtures/checkout');
const { setOrderStatusViaAdminUi } = require('../fixtures/adminOrders');
const { wpTool } = require('../fixtures/wpTool');
const { CUSTOMER_1 } = require('../config');

test.describe('Wallet recharge (Add Balance)', () => {
	// Each test() otherwise gets its own fresh page — deliberately shared here
	// across serial steps so "fill the recharge form" -> "check out" can be
	// asserted as separate, individually-reportable steps.
	test.describe.configure({ mode: 'serial' });

	let userId;
	let orderId;
	let ctx;
	let page;
	const rechargeAmount = 60;

	test.beforeAll(async ({ browser }) => {
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;
		await wpTool('set_wallet_balance', { user_id: userId, amount: '0' });

		ctx = await browser.newContext({ storageState: CUSTOMER_1_STATE });
		page = await ctx.newPage();
	});

	test.afterAll(async () => {
		await ctx.close();
	});

	test('submitting the Add Balance form adds the recharge product to cart and routes to checkout', async () => {
		await emptyCart(page);
		await page.goto('/my-account/wps-wallet/wallet-topup/');
		await page.locator('#wps_wallet_recharge').fill(String(rechargeAmount));
		await page.locator('#wps_recharge_wallet').click();
		await page.waitForURL(/\/(cart|checkout)\//, { timeout: 15000 });
		await expect(page.locator('body')).toContainText(/Rechargeable Wallet Product/i);
	});

	test('completing checkout for the recharge order does not yet credit the wallet', async () => {
		if (!page.url().includes('/checkout/')) {
			await page.goto('/checkout/');
		}
		await fillBillingDetails(page);
		orderId = await placeOrder(page);
		expect(orderId).toBeTruthy();

		const balance = await wpTool('get_wallet_balance', { user_id: userId });
		expect(Number(balance.balance)).toBe(0);
	});

	test('marking the recharge order Completed credits the wallet by the recharge amount', async ({ browser }) => {
		const adminCtx = await browser.newContext({ storageState: ADMIN_STATE });
		const adminPage = await adminCtx.newPage();
		await setOrderStatusViaAdminUi(adminPage, orderId, 'Completed');
		await adminCtx.close();

		await expect
			.poll(async () => {
				const balance = await wpTool('get_wallet_balance', { user_id: userId });
				return Number(balance.balance);
			}, { timeout: 15000 })
			.toBe(rechargeAmount);
	});
});
