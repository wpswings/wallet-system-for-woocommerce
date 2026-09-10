const BASE_URL = process.env.WPS_E2E_BASE_URL || 'http://localhost:10130';

const ADMIN = {
	username: process.env.WPS_E2E_ADMIN_USER || 'admin',
	password: process.env.WPS_E2E_ADMIN_PASS || 'Admin@12345',
};

// Two fixed, dedicated customer accounts used across the suite (created/managed
// by wp-tool's `ensure_customer` action — see scripts/seed.js). Kept separate
// from any real accounts on this site so tests never mutate someone's data.
const CUSTOMER_1 = {
	login: 'wpse2e_buyer1',
	email: 'wpse2e.buyer1@example.test',
	password: 'E2eBuyer1!23',
};

const CUSTOMER_2 = {
	login: 'wpse2e_buyer2',
	email: 'wpse2e.buyer2@example.test',
	password: 'E2eBuyer2!23',
};

const MAILPIT_URL = process.env.WPS_E2E_MAILPIT_URL || 'http://localhost:10115';

const TEST_PRODUCT_TITLE = 'WPS E2E Test Product';
const TEST_PRODUCT_PRICE = '25';

module.exports = {
	BASE_URL,
	ADMIN,
	CUSTOMER_1,
	CUSTOMER_2,
	MAILPIT_URL,
	TEST_PRODUCT_TITLE,
	TEST_PRODUCT_PRICE,
};
