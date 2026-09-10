const WSFW_TABS = {
	general: 'wallet-system-for-woocommerce-general',
	walletUsers: 'class-wallet-user-table',
	transactions: 'class-wallet-transaction-list-table',
	withdrawalRequests: 'wallet-system-withdrawal-setting',
	cashback: 'wallet-system-for-woocommerce-cashback',
	walletActions: 'wallet-system-for-woocommerce-wallet-actions',
	buyNowPayLater: 'wallet-system-for-woocommerce-buy-now-pay-later',
	kyc: 'wallet-system-for-woocommerce-wallet-kyc',
	restApi: 'wallet-system-rest-api',
	systemStatus: 'wallet-system-for-woocommerce-system-status',
	// Pro-injected tabs (rendered because the pro plugin is active, regardless of license state)
	proLicense: 'wallet-system-for-woocommerce-pro-license',
	proWithdrawalSettings: 'wallet-system-for-woocommerce-pro-withdrawal-setting-tab',
	proWalletRestriction: 'wallet-system-for-woocommerce-pro-wallet-restriction',
	proWalletPromotions: 'wallet-system-for-woocommerce-pro-wallet-promotions',
	proQuickRecharge: 'wallet-system-for-woocommerce-pro-wallet-recharge-tab',
	proSmsNotification: 'wallet-system-for-woocommerce-pro-wallet-sms-notification-tab',
};

function settingsUrl(tabSlug) {
	return `/wp-admin/admin.php?page=wallet_system_for_woocommerce_menu&wsfw_tab=${tabSlug}`;
}

/**
 * The plugin shows a "Welcome to WP Swings" onboarding modal on its settings
 * screens until dismissed once; it intercepts pointer events for every field
 * behind it, so every navigation to a wsfw_tab page must clear it first.
 */
async function dismissOnboardingModal(page) {
	const skipLink = page.locator('.wps-wsfw-on-boarding-no_thanks');
	if (await skipLink.isVisible().catch(() => false)) {
		await skipLink.click();
		await page.locator('.wps-wsfw-dialog').waitFor({ state: 'hidden' }).catch(() => {});
	}
}

async function gotoTab(page, tabSlug) {
	await page.goto(settingsUrl(tabSlug));
	await dismissOnboardingModal(page);
}

/**
 * Applies a set of field changes on the currently loaded settings tab and
 * submits it. Field ids on this plugin's settings pages equal their WP
 * option name, confirmed by inspecting each tab's rendered form.
 */
async function setCheckbox(page, id, checked) {
	// Several tabs render checkboxes as Material-style switches: the native
	// input is visually replaced by a custom track/thumb, sized/positioned in
	// a way that makes Playwright's real click land off-target (actionability
	// waits hang, or a forced click "succeeds" but the checked state reverts).
	// Only the underlying <input checked> matters for the plain HTML form
	// this page posts on submit, so set it directly and fire the events a
	// real click would produce.
	await page.locator(`#${id}`).evaluate((el, value) => {
		el.checked = value;
		el.dispatchEvent(new Event('input', { bubbles: true }));
		el.dispatchEvent(new Event('change', { bubbles: true }));
	}, checked);
}

async function applyAndSave(page, { check = [], uncheck = [], fill = {}, select = {} }, saveButtonId) {
	for (const id of check) {
		await setCheckbox(page, id, true);
	}
	for (const id of uncheck) {
		await setCheckbox(page, id, false);
	}
	for (const [id, value] of Object.entries(fill)) {
		await page.locator(`#${id}`).fill(String(value));
	}
	for (const [id, value] of Object.entries(select)) {
		await page.locator(`#${id}`).selectOption(value);
	}
	// This page has some element (promo banner / floating widget) that never
	// settles, so even a forced Playwright click can time out waiting for
	// "stable". Dispatching the click directly in the page skips all of
	// Playwright's actionability waiting.
	await page.locator(`#${saveButtonId}`).evaluate((el) => el.click());
	// Save is an async AJAX call with no reload and no reliably-selectable
	// success indicator; navigating away immediately (as gotoTab() does)
	// aborts it mid-flight and silently drops the change. Give it a moment.
	await page.waitForLoadState('networkidle', { timeout: 5000 }).catch(() => {});
	await page.waitForTimeout(800);
}

module.exports = { WSFW_TABS, settingsUrl, gotoTab, applyAndSave, dismissOnboardingModal };
