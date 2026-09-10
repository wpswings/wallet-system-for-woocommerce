const { test, expect } = require('@playwright/test');
const { wpTool } = require('../fixtures/wpTool');
const { BASE_URL, CUSTOMER_1 } = require('../config');

const KEYS = { consumer_key: 'e2e_test_consumer_key', consumer_secret: 'e2e_test_consumer_secret' };

test.describe('REST API (wsfw-route/v1)', () => {
	let userId;

	test.beforeAll(async () => {
		await wpTool('update_option', { name: 'wps_wsfw_wallet_rest_api_keys', value: KEYS });
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;
		await wpTool('set_wallet_balance', { user_id: userId, amount: '10' });
	});

	test('GET /wallet/users requires valid consumer key/secret', async ({ request }) => {
		const badResponse = await request.get(`${BASE_URL}/wp-json/wsfw-route/v1/wallet/users`, {
			params: { consumer_key: 'wrong', consumer_secret: 'wrong' },
		});
		expect(badResponse.status()).toBe(401);

		const goodResponse = await request.get(`${BASE_URL}/wp-json/wsfw-route/v1/wallet/users`, {
			params: KEYS,
		});
		expect(goodResponse.ok()).toBeTruthy();
	});

	test('GET /wallet/{id} returns that user\'s balance', async ({ request }) => {
		const response = await request.get(`${BASE_URL}/wp-json/wsfw-route/v1/wallet/${userId}`, {
			params: KEYS,
		});
		expect(response.ok()).toBeTruthy();
		const body = await response.json();
		const balance = Array.isArray(body) ? body[0] : body;
		expect(JSON.stringify(balance)).toContain('10');
	});

	test('PUT /wallet/{id} credits the wallet via the API', async ({ request }) => {
		const response = await request.put(`${BASE_URL}/wp-json/wsfw-route/v1/wallet/${userId}`, {
			data: {
				...KEYS,
				amount: 7,
				action: 'credit',
				transaction_detail: 'E2E REST API credit',
			},
		});
		expect(response.ok()).toBeTruthy();

		const balance = await wpTool('get_wallet_balance', { user_id: userId });
		expect(Number(balance.balance)).toBeCloseTo(17, 2);
	});

	test('PUT /wallet/{id} debits the wallet via the API', async ({ request }) => {
		const response = await request.put(`${BASE_URL}/wp-json/wsfw-route/v1/wallet/${userId}`, {
			data: {
				...KEYS,
				amount: 5,
				action: 'debit',
				transaction_detail: 'E2E REST API debit',
			},
		});
		expect(response.ok()).toBeTruthy();

		const balance = await wpTool('get_wallet_balance', { user_id: userId });
		expect(Number(balance.balance)).toBeCloseTo(12, 2);
	});

	test('GET /wallet/transactions/{id} lists the ledger', async ({ request }) => {
		const response = await request.get(`${BASE_URL}/wp-json/wsfw-route/v1/wallet/transactions/${userId}`, {
			params: KEYS,
		});
		expect(response.ok()).toBeTruthy();
		const body = await response.json();
		expect(Array.isArray(body) ? body.length : Object.keys(body).length).toBeGreaterThan(0);
	});
});
