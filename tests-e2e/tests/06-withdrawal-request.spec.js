const { test, expect } = require('@playwright/test');
const { CUSTOMER_1_STATE } = require('../fixtures/storageStates');
const { wpTool } = require('../fixtures/wpTool');
const { CUSTOMER_1 } = require('../config');

test.describe('Wallet withdrawal request', () => {
	test.use({ storageState: CUSTOMER_1_STATE });

	let userId;

	test.beforeEach(async () => {
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;
		await wpTool('set_wallet_balance', { user_id: userId, amount: '200' });
	});

	test('submitting a withdrawal request succeeds and does not immediately debit the wallet', async ({ page }) => {
		await page.goto('/my-account/wps-wallet/wallet-withdrawal/');
		await page.locator('#wps_wallet_withdrawal_amount').fill('40');
		await page.locator('#wps_withdrawal_request').click();
		await expect(page.locator('body')).toContainText(/success|request|submitted/i, { timeout: 15000 });

		// Withdrawal requests are pending admin approval, not an instant debit.
		const balance = await wpTool('get_wallet_balance', { user_id: userId });
		expect(Number(balance.balance)).toBe(200);
	});
});
