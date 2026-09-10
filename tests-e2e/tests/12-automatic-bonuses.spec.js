const { test, expect } = require('@playwright/test');
const { ADMIN_STATE, CUSTOMER_1_STATE } = require('../fixtures/storageStates');
const { wpTool } = require('../fixtures/wpTool');
const { CUSTOMER_1, TEST_PRODUCT_TITLE, TEST_PRODUCT_PRICE } = require('../config');

test.describe('Signup bonus', () => {
	test.beforeAll(async () => {
		await wpTool('update_option', { name: 'woocommerce_enable_myaccount_registration', value: 'yes' });
	});

	test('registering a new account credits the configured signup bonus', async ({ page }) => {
		const uniqueEmail = `wpse2e.signup.${Date.now()}@example.test`;

		await page.goto('/my-account/');
		const registerForm = page.locator('form.woocommerce-form-register');
		await registerForm.locator('#reg_email').fill(uniqueEmail);
		if (await registerForm.locator('#reg_password').count()) {
			await registerForm.locator('#reg_password').fill('E2eSignup!2345');
		} else if (await registerForm.locator('input[name="password"]').count()) {
			await registerForm.locator('input[name="password"]').fill('E2eSignup!2345');
		}
		await registerForm.getByRole('button', { name: 'Register' }).click();
		await page.waitForURL(/my-account/, { timeout: 15000 });

		const newUser = await wpTool('get_user_by_email', { email: uniqueEmail });
		expect(newUser).not.toBeNull();

		// Registering also auto-logs the customer in and redirects to My
		// Account, which is itself a "site visit" — so the daily-visit bonus
		// (+5) stacks with the signup bonus (+50) on this very first balance
		// read. Confirmed against a live run before asserting the total.
		await expect
			.poll(async () => Number((await wpTool('get_wallet_balance', { user_id: newUser.id })).balance), { timeout: 15000 })
			.toBe(55);
	});
});

test.describe('Daily visit bonus', () => {
	test.use({ storageState: CUSTOMER_1_STATE });
	let userId;

	test.beforeAll(async () => {
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;
		await wpTool('delete_transient', { name: `wps_wsfw_wallet_site_visit_${userId}` });
	});

	test('first visit of the day credits the daily bonus once', async ({ page }) => {
		const before = await wpTool('get_wallet_balance', { user_id: userId });
		await page.goto('/my-account/wps-wallet/');

		await expect
			.poll(async () => Number((await wpTool('get_wallet_balance', { user_id: userId })).balance), { timeout: 15000 })
			.toBeCloseTo(Number(before.balance) + 5, 2); // baseline: wps_wsfw_wallet_action_daily_amount = 5
	});

	test('a second visit the same day does not credit again', async ({ page }) => {
		const before = await wpTool('get_wallet_balance', { user_id: userId });
		await page.goto('/my-account/wps-wallet/');
		await page.waitForTimeout(1500);

		const after = await wpTool('get_wallet_balance', { user_id: userId });
		expect(Number(after.balance)).toBeCloseTo(Number(before.balance), 2);
	});
});

test.describe('Comment/review approval bonus', () => {
	let userId;
	let productId;
	let commentId;

	test.beforeAll(async () => {
		const user = await wpTool('get_user_by_login', { login: CUSTOMER_1.login });
		userId = user.id;
		const product = await wpTool('ensure_simple_product', { title: TEST_PRODUCT_TITLE, price: TEST_PRODUCT_PRICE });
		productId = product.id;
	});

	test('approving a customer review credits the comment bonus', async ({ browser }) => {
		const before = await wpTool('get_wallet_balance', { user_id: userId });
		const inserted = await wpTool('insert_unapproved_comment', {
			post_id: productId,
			user_id: userId,
			content: 'E2E automated product review',
		});
		commentId = inserted.id;

		const ctx = await browser.newContext({ storageState: ADMIN_STATE });
		const page = await ctx.newPage();
		// WooCommerce moves product reviews (comment_type=review) out of the
		// main wp-admin Comments screen onto their own admin page — confirmed
		// live the comment is absent from edit-comments.php entirely.
		await page.goto('/wp-admin/edit.php?post_type=product&page=product-reviews');
		await page.locator(`#comment-${commentId}`).scrollIntoViewIfNeeded();
		await page.hover(`#comment-${commentId}`);
		const approveLink = page.locator(`#comment-${commentId} .approve a`);
		if (await approveLink.count()) {
			await approveLink.click();
			await page.waitForLoadState('networkidle').catch(() => {});
		} else {
			// row action requires hover to render; fall back to the direct action
			await wpTool('approve_comment', { comment_id: commentId });
		}
		await ctx.close();

		await expect
			.poll(async () => Number((await wpTool('get_wallet_balance', { user_id: userId })).balance), { timeout: 15000 })
			.toBeCloseTo(Number(before.balance) + 5, 2); // baseline: wps_wsfw_wallet_action_comment_amount = 5
	});
});
