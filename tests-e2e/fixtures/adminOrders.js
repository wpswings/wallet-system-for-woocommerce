function orderEditUrl(orderId) {
	return `/wp-admin/admin.php?page=wc-orders&action=edit&id=${orderId}`;
}

async function setOrderStatusViaAdminUi(page, orderId, statusLabel) {
	await page.goto(orderEditUrl(orderId), { waitUntil: 'load' });
	await page.locator('#order_status').selectOption({ label: statusLabel });
	await page.locator('button.save_order, input.save_order').first().click();
	await page.waitForLoadState('networkidle').catch(() => {});
}

/**
 * Confirmed live on the order edit screen: clicking "Refund" reveals a panel
 * with per-line-item `refund_line_total[<item_id>]` / `refund_line_tax[...]`
 * inputs (the top-level `#refund_amount` is read-only, computed from these),
 * plus two distinct action buttons — `.do-manual-refund` ("Refund $X
 * manually", e.g. via the original gateway) and `.do-wallet-refund` ("Refund
 * $X to user wallet", this plugin's own wallet-credit refund path).
 */
async function refundOrderViaAdminUi(page, orderId, { lineTotal, lineTax = 0, reason = 'E2E refund', toWallet = true } = {}) {
	await page.goto(orderEditUrl(orderId), { waitUntil: 'load' });
	await page.getByRole('button', { name: /refund/i }).first().click();
	await page.waitForTimeout(500);

	await page.locator('input.refund_line_total').first().fill(String(lineTotal));
	if (lineTax) {
		await page.locator('input.refund_line_tax').first().fill(String(lineTax));
	}
	if (reason && (await page.locator('#refund_reason').count())) {
		await page.locator('#refund_reason').fill(reason);
	}
	await page.waitForTimeout(300);

	page.once('dialog', (dialog) => dialog.accept());
	const refundButton = page.locator(toWallet ? 'button.do-wallet-refund' : 'button.do-manual-refund');
	await refundButton.click();
	await page.waitForLoadState('networkidle', { timeout: 15000 }).catch(() => {});
	await page.waitForTimeout(1000);
}

module.exports = { orderEditUrl, setOrderStatusViaAdminUi, refundOrderViaAdminUi };
