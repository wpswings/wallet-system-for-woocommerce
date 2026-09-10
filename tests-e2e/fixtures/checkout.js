const { TEST_PRODUCT_TITLE, TEST_PRODUCT_PRICE } = require('../config');
const { wpTool } = require('./wpTool');

let cachedProductId;

/** Creates (once) the plugin-agnostic simple product the checkout specs buy. */
async function ensureTestProduct() {
	if (cachedProductId) {
		return cachedProductId;
	}
	const product = await wpTool('ensure_simple_product', { title: TEST_PRODUCT_TITLE, price: TEST_PRODUCT_PRICE });
	cachedProductId = product.id;
	return cachedProductId;
}

/**
 * WooCommerce restores a logged-in customer's persistent cart (user meta)
 * on login, so leftover items from earlier manual exploration or a previous
 * test survive into a brand-new browser context. Every checkout test starts
 * from a clean cart to avoid that cross-run contamination.
 */
async function emptyCart(page) {
	await page.goto('/cart/', { waitUntil: 'load' });
	// Blocks cart (confirmed live markup, not the classic a.remove link) is a
	// React app that hasn't hydrated yet right after navigation — check for
	// either a remove button or the empty-cart state before deciding there's
	// nothing left to remove.
	for (let i = 0; i < 10; i++) {
		const removeButton = page.locator('.wc-block-cart-item__remove-link').first();
		const isEmpty = page.locator('.wc-block-cart__empty-cart, .is-empty');
		await Promise.race([
			removeButton.waitFor({ state: 'visible', timeout: 8000 }).catch(() => {}),
			isEmpty.first().waitFor({ state: 'visible', timeout: 8000 }).catch(() => {}),
		]);
		if (!(await removeButton.count())) {
			break;
		}
		await removeButton.click();
		await page.waitForTimeout(1200);
	}
}

async function addTestProductToCart(page) {
	const productId = await ensureTestProduct();
	await emptyCart(page);
	await page.goto(`/?add-to-cart=${productId}&quantity=1`, { waitUntil: 'load' });
	return productId;
}

/**
 * This site's checkout is the React-based WooCommerce Cart & Checkout Blocks
 * experience (confirmed live: markup uses wc-block-checkout / .wc-block-*
 * classes, not classic name="billing_first_name" fields). Field ids use a
 * hyphen (id="billing-first_name") while the underlying POSTed name still
 * uses an underscore (name="billing_first_name").
 */
async function fillBillingDetails(page, { firstName = 'WPS', lastName = 'E2E Tester', address = '123 Test St', city = 'New York', postcode = '10001', phone = '5555550100' } = {}) {
	// Once an address has been used before, Blocks checkout shows a collapsed
	// read-only summary card (with an "Edit" link) instead of the input form.
	// That's a still-valid address for placing the order, so there's nothing
	// to fill in that case.
	const firstNameField = page.locator('#billing-first_name');
	const isFormVisible = await firstNameField.isVisible().catch(() => false);
	if (!isFormVisible) {
		return;
	}
	await firstNameField.fill(firstName);
	await page.locator('#billing-last_name').fill(lastName);
	await page.locator('#billing-address_1').fill(address);
	await page.locator('#billing-city').fill(city);
	await page.locator('#billing-postcode').fill(postcode);
	if (await page.locator('#billing-phone').count()) {
		await page.locator('#billing-phone').fill(phone);
	}
}

async function selectPaymentMethod(page, matcher) {
	const option = page.locator('.wc-block-components-radio-control__option', { hasText: matcher }).first();
	await option.waitFor({ state: 'visible', timeout: 20000 });
	await option.click();
}

/**
 * The "Pay by wallet ($balance)" partial-payment checkbox next to the order
 * total (id="partial_payment_wallet" despite the plugin's manual/total-pay
 * option). Checking it alone does nothing to the total — confirmed live it
 * reveals a separate "Amount want to use from wallet" input (#wallet_amount)
 * + "Apply wallet" button (#apply_wallet) that the customer must also use;
 * that's the manual_pay mode this site is configured with (baseline default).
 */
async function togglePartialWalletPayment(page, checked = true, amount) {
	const checkbox = page.locator('#partial_payment_wallet');
	await checkbox.waitFor({ state: 'visible', timeout: 20000 });
	if (checked) {
		await checkbox.check();
		const amountField = page.locator('#wallet_amount');
		await amountField.waitFor({ state: 'visible', timeout: 10000 });
		const walletBalance = await checkbox.getAttribute('data-walletamount');
		await amountField.fill(String(amount ?? walletBalance));
		await page.locator('#apply_wallet').click();
	} else {
		await checkbox.uncheck();
	}
	// Triggers an AJAX recalculation of the order total; give it a moment.
	await page.waitForTimeout(1500);
}

async function placeOrder(page) {
	const button = page.getByRole('button', { name: 'Place Order' });
	await button.click();
	await page.waitForURL(/order-received/, { timeout: 30000 });
	const match = page.url().match(/order-received\/(\d+)/) || page.url().match(/[?&]order=(?:wc_order_)?([a-zA-Z0-9]+)/) || page.url().match(/order_id=(\d+)/);
	return match ? match[1] : null;
}

module.exports = {
	ensureTestProduct,
	addTestProductToCart,
	emptyCart,
	fillBillingDetails,
	selectPaymentMethod,
	togglePartialWalletPayment,
	placeOrder,
};
