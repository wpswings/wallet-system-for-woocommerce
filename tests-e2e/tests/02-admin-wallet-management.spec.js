const { test, expect } = require('@playwright/test');
const { ADMIN_STATE } = require('../fixtures/storageStates');
const { WSFW_TABS, gotoTab } = require('../fixtures/adminSettings');
const { wpTool } = require('../fixtures/wpTool');
const { CUSTOMER_1, CUSTOMER_2 } = require('../config');

test.use({ storageState: ADMIN_STATE });

async function openEditWalletModal(page, userId) {
	await gotoTab(page, WSFW_TABS.walletUsers);
	await page.locator(`a.edit_wallet[data-userid="${userId}"]`).click();
	await expect(page.locator('#wps_wallet-edit-popup-input')).toBeVisible();
}

async function submitEditWallet(page, { amount, actionType, note }) {
	await page.locator('#wps_wallet-edit-popup-input').fill(String(amount));
	await page.locator(`#${actionType}`).evaluate((el) => el.click());
	if (note) {
		await page.locator('#wps_wallet-edit-popup-transaction-detail').fill(note);
	}
	await page.locator('#wps_wallet_submit_val').evaluate((el) => el.click());
	await page.locator('#confirm_updatewallet').waitFor({ state: 'attached' });
	await page.locator('#confirm_updatewallet').evaluate((el) => el.click());
}

test.describe('Admin: manual wallet credit/debit', () => {
	let userId;

	test.beforeAll(async () => {
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;
		await wpTool('set_wallet_balance', { user_id: userId, amount: '100' });
	});

	test('admin can credit a specific customer wallet', async ({ page }) => {
		await openEditWalletModal(page, userId);
		await submitEditWallet(page, { amount: 50, actionType: 'credit', note: 'E2E credit test' });

		await expect(page.locator('body')).toContainText(/success|updated/i);

		const balance = await wpTool('get_wallet_balance', { user_id: userId });
		expect(Number(balance.balance)).toBeCloseTo(150, 2);
	});

	test('admin can debit a specific customer wallet', async ({ page }) => {
		await openEditWalletModal(page, userId);
		await submitEditWallet(page, { amount: 30, actionType: 'debit', note: 'E2E debit test' });

		const balance = await wpTool('get_wallet_balance', { user_id: userId });
		expect(Number(balance.balance)).toBeCloseTo(120, 2);
	});

	test('credit/debit is recorded in the wallet transaction ledger', async () => {
		const last = await wpTool('last_transaction', { user_id: userId });
		expect(last).not.toBeNull();
		expect(['credit', 'debit']).toContain(last.transaction_type_1);
		expect(last.transaction_type).toContain('E2E debit test');
	});

	test('Wallet Transactions admin tab lists the ledger', async ({ page }) => {
		await gotoTab(page, WSFW_TABS.transactions);
		await expect(page.locator('table.wp-list-table')).toBeVisible();
		await expect(page.locator('body')).toContainText(CUSTOMER_1.login);
	});
});

test.describe('Admin: bulk wallet update for all users', () => {
	test('bulk credit adds the same amount to every user without error', async ({ page }) => {
		const before1 = await wpTool('get_wallet_balance', { user_id: (await wpTool('get_user_by_login', { login: CUSTOMER_1.login })).id });
		const before2 = await wpTool('get_wallet_balance', { user_id: (await wpTool('get_user_by_login', { login: CUSTOMER_2.login })).id });

		await gotoTab(page, WSFW_TABS.walletUsers);
		await page.locator('#wsfw_wallet_amount_for_users').fill('1');
		await page.locator('#wsfw_wallet_action_for_users[value="credit"]').check();
		await page.locator('#wsfw_wallet_transaction_details_for_users').fill('E2E bulk credit');
		await page.getByRole('button', { name: /update wallet/i }).first().click();

		const confirmYes = page.locator('#confirm_updatewallet, button:has-text("Yes")').first();
		if (await confirmYes.isVisible().catch(() => false)) {
			await confirmYes.click();
		}

		await expect(page.locator('body')).toContainText(/success|updated/i);

		const id1 = (await wpTool('get_user_by_login', { login: CUSTOMER_1.login })).id;
		const id2 = (await wpTool('get_user_by_login', { login: CUSTOMER_2.login })).id;
		const after1 = await wpTool('get_wallet_balance', { user_id: id1 });
		const after2 = await wpTool('get_wallet_balance', { user_id: id2 });

		expect(Number(after1.balance)).toBeCloseTo(Number(before1.balance) + 1, 2);
		expect(Number(after2.balance)).toBeCloseTo(Number(before2.balance) + 1, 2);
	});
});
