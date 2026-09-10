const { test, expect } = require('@playwright/test');
const { CUSTOMER_1_STATE } = require('../fixtures/storageStates');
const { addTestProductToCart, fillBillingDetails, togglePartialWalletPayment, placeOrder } = require('../fixtures/checkout');
const { wpTool } = require('../fixtures/wpTool');
const { CUSTOMER_1 } = require('../config');

test.describe('Checkout: partial payment with wallet + another gateway', () => {
	test.describe.configure({ mode: 'serial' });

	let userId;
	let ctx;
	let page;
	const partialBalance = 12;

	test.beforeAll(async ({ browser }) => {
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;
		await wpTool('set_wallet_balance', { user_id: userId, amount: String(partialBalance) });

		ctx = await browser.newContext({ storageState: CUSTOMER_1_STATE });
		page = await ctx.newPage();
	});

	test.afterAll(async () => {
		await ctx.close();
	});

	test('checking "Pay by wallet" reduces the payable total by the wallet balance', async () => {
		await addTestProductToCart(page);
		await page.goto('/checkout/');
		await fillBillingDetails(page);

		await expect(page.locator('body')).toContainText(`Pay by wallet ($${partialBalance.toFixed(2)})`);

		const totalBefore = await page.locator('.wc-block-components-totals-footer-item .wc-block-formatted-money-amount').last().innerText();
		await togglePartialWalletPayment(page, true);
		await expect
			.poll(async () => page.locator('.wc-block-components-totals-footer-item .wc-block-formatted-money-amount').last().innerText())
			.not.toBe(totalBefore);
	});

	test('placing the order with the partial wallet amount checked debits the wallet', async () => {
		const orderId = await placeOrder(page);
		expect(orderId).toBeTruthy();

		await expect
			.poll(async () => Number((await wpTool('get_wallet_balance', { user_id: userId })).balance), { timeout: 15000 })
			.toBeLessThan(partialBalance);
	});
});
