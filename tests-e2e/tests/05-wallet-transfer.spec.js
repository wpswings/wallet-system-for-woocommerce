const { test, expect } = require('@playwright/test');
const { CUSTOMER_1_STATE } = require('../fixtures/storageStates');
const { wpTool } = require('../fixtures/wpTool');
const { CUSTOMER_1, CUSTOMER_2 } = require('../config');

test.describe('Wallet-to-wallet transfer', () => {
	test.use({ storageState: CUSTOMER_1_STATE });

	let senderId;
	let recipientId;

	test.beforeEach(async () => {
		const sender = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		const recipient = await wpTool('get_user_by_login', { login: CUSTOMER_2.login });
		senderId = sender.id;
		recipientId = recipient.id;
		await wpTool('set_wallet_balance', { user_id: senderId, amount: '200' });
		await wpTool('set_wallet_balance', { user_id: recipientId, amount: '0' });
	});

	test('transfer by recipient email moves funds between wallets', async ({ page }) => {
		await page.goto('/my-account/wps-wallet/wallet-transfer/');
		await page.locator('#wps_wallet_transfer_method').selectOption('email');
		await page.locator('#wps_wallet_transfer_user_email').fill(CUSTOMER_2.email);
		await page.locator('#wps_wallet_transfer_amount').fill('30');
		await page.locator('#wps_proceed_transfer').click();
		await expect(page.locator('body')).toContainText(/success|transfer/i, { timeout: 15000 });

		await expect
			.poll(async () => Number((await wpTool('get_wallet_balance', { user_id: senderId })).balance), { timeout: 15000 })
			.toBeCloseTo(170, 2);
		await expect
			.poll(async () => Number((await wpTool('get_wallet_balance', { user_id: recipientId })).balance), { timeout: 15000 })
			.toBeCloseTo(30, 2);
	});

	test('transfer by recipient Wallet ID also works', async ({ page }) => {
		const recipientWalletId = (await wpTool('get_wallet_id', { user_id: recipientId })).wallet_id;

		await page.goto('/my-account/wps-wallet/wallet-transfer/');
		await page.locator('#wps_wallet_transfer_method').selectOption('wallet_id');
		await page.locator('#wps_wallet_transfer_user_walletid').fill(recipientWalletId);
		await page.locator('#wps_wallet_transfer_amount').fill('15');
		await page.locator('#wps_proceed_transfer').click();
		await expect(page.locator('body')).toContainText(/success|transfer/i, { timeout: 15000 });

		await expect
			.poll(async () => Number((await wpTool('get_wallet_balance', { user_id: recipientId })).balance), { timeout: 15000 })
			.toBeCloseTo(15, 2);
	});

	test('cannot transfer more than the current balance', async ({ page }) => {
		await page.goto('/my-account/wps-wallet/wallet-transfer/');
		await page.locator('#wps_wallet_transfer_method').selectOption('email');
		await page.locator('#wps_wallet_transfer_user_email').fill(CUSTOMER_2.email);
		await page.locator('#wps_wallet_transfer_amount').fill('99999');
		await page.locator('#wps_proceed_transfer').click();
		await expect(page.locator('body')).toContainText(/insufficient|greater|exceed|not enough|less than/i, { timeout: 15000 });

		const balance = await wpTool('get_wallet_balance', { user_id: senderId });
		expect(Number(balance.balance)).toBe(200);
	});

	test('cannot transfer to self', async ({ page }) => {
		await page.goto('/my-account/wps-wallet/wallet-transfer/');
		await page.locator('#wps_wallet_transfer_method').selectOption('email');
		await page.locator('#wps_wallet_transfer_user_email').fill(CUSTOMER_1.email);
		await page.locator('#wps_wallet_transfer_amount').fill('10');
		// Client-side validation disables Proceed outright for a self-transfer
		// rather than letting the click through to a server-side error.
		await expect(page.locator('#wps_proceed_transfer')).toBeDisabled({ timeout: 15000 });

		const balance = await wpTool('get_wallet_balance', { user_id: senderId });
		expect(Number(balance.balance)).toBe(200);
	});
});
