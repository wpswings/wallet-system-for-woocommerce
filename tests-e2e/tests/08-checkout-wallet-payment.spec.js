const { test, expect } = require('@playwright/test');
const { CUSTOMER_1_STATE } = require('../fixtures/storageStates');
const { addTestProductToCart, fillBillingDetails, placeOrder } = require('../fixtures/checkout');
const { wpTool } = require('../fixtures/wpTool');
const { CUSTOMER_1, TEST_PRODUCT_PRICE } = require('../config');

test.describe('Checkout: pay with Wallet Payment gateway', () => {
	test.use({ storageState: CUSTOMER_1_STATE });

	let userId;

	test.beforeAll(async () => {
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;
	});

	test('gateway is hidden when balance is below the order total', async ({ page }) => {
		await wpTool('set_wallet_balance', { user_id: userId, amount: '1' });
		await addTestProductToCart(page);
		await page.goto('/checkout/');
		await fillBillingDetails(page);
		await expect(page.locator('#radio-control-wc-payment-method-options-wps_wcb_wallet_payment_gateway')).toHaveCount(0);
	});

	test('gateway is offered once balance covers the total, and debits on order placement', async ({ page }) => {
		await wpTool('set_wallet_balance', { user_id: userId, amount: '100' });
		await addTestProductToCart(page);
		await page.goto('/checkout/');
		await fillBillingDetails(page);

		// Blocks checkout remembers the customer's last-used payment method
		// (confirmed live: an earlier test's COD selection persisted here),
		// so select Wallet Payment explicitly rather than assuming it
		// defaults to checked just because it's the first/only-affordable option.
		const walletOption = page.locator('#radio-control-wc-payment-method-options-wps_wcb_wallet_payment_gateway');
		await expect(walletOption).toBeVisible();
		await walletOption.check();
		await expect(walletOption).toBeChecked();

		const orderId = await placeOrder(page);
		expect(orderId).toBeTruthy();

		const order = await wpTool('get_order_status', { order_id: orderId });
		// baseline enabled "Auto Complete Wallet Payment Order Status"
		expect(order.status).toBe('completed');

		// Auto-completing also fires the cart-wise cashback hook (baseline:
		// 10% of the $25 subtotal = $2.50), so the net change is debit - cashback.
		const orderTotal = Number(TEST_PRODUCT_PRICE) * 1.1;
		const cashback = Number(TEST_PRODUCT_PRICE) * 0.1;
		const expectedBalance = 100 - orderTotal + cashback;
		await expect
			.poll(async () => Number((await wpTool('get_wallet_balance', { user_id: userId })).balance), { timeout: 15000 })
			.toBeCloseTo(expectedBalance, 2);
	});
});
