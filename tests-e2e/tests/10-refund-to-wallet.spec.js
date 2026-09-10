const { test, expect } = require('@playwright/test');
const { ADMIN_STATE, CUSTOMER_1_STATE } = require('../fixtures/storageStates');
const { addTestProductToCart, fillBillingDetails, placeOrder } = require('../fixtures/checkout');
const { refundOrderViaAdminUi } = require('../fixtures/adminOrders');
const { wpTool } = require('../fixtures/wpTool');
const { CUSTOMER_1, TEST_PRODUCT_PRICE } = require('../config');

test.describe('Admin: refund a wallet-paid order back to the wallet', () => {
	let userId;
	let orderId;
	const orderTotal = Number(TEST_PRODUCT_PRICE) * 1.1;

	test.beforeAll(async () => {
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;
		await wpTool('set_wallet_balance', { user_id: userId, amount: '100' });
	});

	test('customer pays an order in full via the Wallet Payment gateway', async ({ browser }) => {
		const ctx = await browser.newContext({ storageState: CUSTOMER_1_STATE });
		const page = await ctx.newPage();
		await addTestProductToCart(page);
		await page.goto('/checkout/');
		await fillBillingDetails(page);
		orderId = await placeOrder(page);
		await ctx.close();
		expect(orderId).toBeTruthy();
	});

	test('admin refunding the order "to user wallet" credits the customer back', async ({ browser }) => {
		const before = await wpTool('get_wallet_balance', { user_id: userId });

		const ctx = await browser.newContext({ storageState: ADMIN_STATE });
		const page = await ctx.newPage();
		await refundOrderViaAdminUi(page, orderId, {
			lineTotal: Number(TEST_PRODUCT_PRICE),
			lineTax: Number(TEST_PRODUCT_PRICE) * 0.1,
			toWallet: true,
		});
		await ctx.close();

		await expect
			.poll(async () => Number((await wpTool('get_wallet_balance', { user_id: userId })).balance), { timeout: 15000 })
			.toBeCloseTo(Number(before.balance) + orderTotal, 2);
	});
});
