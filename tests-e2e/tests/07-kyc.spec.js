const path = require('path');
const { test, expect } = require('@playwright/test');
const { ADMIN_STATE, CUSTOMER_1_STATE } = require('../fixtures/storageStates');
const { wpTool } = require('../fixtures/wpTool');
const { CUSTOMER_1 } = require('../config');

const SAMPLE_DOCUMENT = path.join(__dirname, '..', 'fixtures', 'assets', 'sample-kyc.png');

test.describe('Wallet KYC verification', () => {
	let userId;

	test.beforeAll(async () => {
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;
		await wpTool('update_user_meta', { user_id: userId, key: 'key_verification_status', value: '' });
	});

	test('customer can submit a KYC document for review', async ({ browser }) => {
		const ctx = await browser.newContext({ storageState: CUSTOMER_1_STATE });
		const page = await ctx.newPage();
		await page.goto('/my-account/wps-wallet/wallet-kyc-verification/');
		await page.locator('#wps_wallet_kyc_document_1').setInputFiles(SAMPLE_DOCUMENT);
		await page.locator('#wps_submit_kyc').click();
		await expect(page.locator('body')).toContainText(/success|submitted|pending/i, { timeout: 15000 });
		await ctx.close();

		await expect
			.poll(async () => (await wpTool('get_user_meta', { user_id: userId, key: 'key_verification_status' })).value, { timeout: 15000 })
			.toBe('pending');
	});

	test('admin can see and approve the pending KYC request', async ({ browser }) => {
		const ctx = await browser.newContext({ storageState: ADMIN_STATE });
		const page = await ctx.newPage();
		await page.goto('/wp-admin/admin.php?page=wallet_system_for_woocommerce_menu&wsfw_tab=wallet-system-for-woocommerce-wallet-kyc');
		// Settings and the requests table are two sub-views of the same tab.
		await page.getByText('View Kyc Request').click();

		const row = page.locator('.kyc-table tr', { hasText: CUSTOMER_1.email });
		await expect(row).toBeVisible({ timeout: 15000 });

		await row.locator('.kyc-remark-input').fill('E2E approved');
		await row.locator('.kyc-status-select').selectOption('approved');
		await ctx.close();

		await expect
			.poll(async () => (await wpTool('get_user_meta', { user_id: userId, key: 'key_verification_status' })).value, { timeout: 15000 })
			.toBe('approved');
	});
});
