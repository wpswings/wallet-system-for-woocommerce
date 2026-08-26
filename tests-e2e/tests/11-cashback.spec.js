const { test, expect } = require('@playwright/test');
const { ADMIN_STATE, CUSTOMER_1_STATE } = require('../fixtures/storageStates');
const { addTestProductToCart, fillBillingDetails, selectPaymentMethod, placeOrder } = require('../fixtures/checkout');
const { setOrderStatusViaAdminUi } = require('../fixtures/adminOrders');
const { wpTool } = require('../fixtures/wpTool');
const { CUSTOMER_1, TEST_PRODUCT_PRICE } = require('../config');

test.describe('Cart-wise cashback on order completion', () => {
	test.describe.configure({ mode: 'serial' });

	let userId;
	let orderId;
	let ctx;
	let page;
	const expectedCashback = Number(TEST_PRODUCT_PRICE) * 0.1; // baseline: 10% cart-wise cashback

	test.beforeAll(async ({ browser }) => {
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;

		ctx = await browser.newContext({ storageState: CUSTOMER_1_STATE });
		page = await ctx.newPage();
	});

	test.afterAll(async () => {
		await ctx.close();
	});

	test('cart page shows the cashback notice before checkout', async () => {
		await addTestProductToCart(page);
		await page.goto('/cart/');
		await expect(page.locator('body')).toContainText(new RegExp(`cashback of \\$${expectedCashback.toFixed(2)}`, 'i'));
	});

	test('placing and completing the order does not credit cashback until Completed', async () => {
		const before = await wpTool('get_wallet_balance', { user_id: userId });
		await page.goto('/checkout/');
		await fillBillingDetails(page);
		// Blocks checkout remembers the last-used payment method (confirmed
		// live) — a prior test in another file may have left Wallet Payment
		// selected, which would debit the wallet here instead of via COD.
		await selectPaymentMethod(page, 'Cash on delivery');
		orderId = await placeOrder(page);
		expect(orderId).toBeTruthy();

		const afterPlaced = await wpTool('get_wallet_balance', { user_id: userId });
		expect(Number(afterPlaced.balance)).toBe(Number(before.balance));
	});

	test('marking the order Completed credits the cashback amount', async ({ browser }) => {
		const before = await wpTool('get_wallet_balance', { user_id: userId });
		const adminCtx = await browser.newContext({ storageState: ADMIN_STATE });
		const adminPage = await adminCtx.newPage();
		await setOrderStatusViaAdminUi(adminPage, orderId, 'Completed');
		await adminCtx.close();

		await expect
			.poll(async () => {
				const balance = await wpTool('get_wallet_balance', { user_id: userId });
				return Number(balance.balance);
			}, { timeout: 15000 })
			.toBeCloseTo(Number(before.balance) + expectedCashback, 2);
	});

	test('cancelling a completed cashback order claws the cashback back', async ({ browser }) => {
		const before = await wpTool('get_wallet_balance', { user_id: userId });
		const adminCtx = await browser.newContext({ storageState: ADMIN_STATE });
		const adminPage = await adminCtx.newPage();
		await setOrderStatusViaAdminUi(adminPage, orderId, 'Cancelled');
		await adminCtx.close();

		await expect
			.poll(async () => {
				const balance = await wpTool('get_wallet_balance', { user_id: userId });
				return Number(balance.balance);
			}, { timeout: 15000 })
			.toBeCloseTo(Number(before.balance) - expectedCashback, 2);
	});
});
