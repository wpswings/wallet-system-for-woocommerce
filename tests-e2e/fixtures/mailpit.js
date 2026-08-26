const { MAILPIT_URL } = require('../config');

/**
 * Local by Flywheel intercepts this site's outgoing mail into a Mailpit
 * inbox (REST API on MAILPIT_URL). Used to assert wallet notification
 * emails actually get sent, without needing a real SMTP provider.
 */
async function searchMessages(query) {
	const url = `${MAILPIT_URL}/api/v1/search?query=${encodeURIComponent(query)}`;
	const res = await fetch(url);
	if (!res.ok) {
		throw new Error(`Mailpit search failed: ${res.status}`);
	}
	const data = await res.json();
	return data.messages || [];
}

async function waitForMessage(query, { timeoutMs = 15000, intervalMs = 1000 } = {}) {
	const deadline = Date.now() + timeoutMs;
	for (;;) {
		const messages = await searchMessages(query);
		if (messages.length > 0) {
			return messages[0];
		}
		if (Date.now() > deadline) {
			return null;
		}
		await new Promise((r) => setTimeout(r, intervalMs));
	}
}

async function deleteAllMessages() {
	await fetch(`${MAILPIT_URL}/api/v1/messages`, { method: 'DELETE' });
}

module.exports = { searchMessages, waitForMessage, deleteAllMessages };
