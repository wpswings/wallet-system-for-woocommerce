const path = require('path');

const AUTH_DIR = path.join(__dirname, '..', '.auth');

module.exports = {
	ADMIN_STATE: path.join(AUTH_DIR, 'admin.json'),
	CUSTOMER_1_STATE: path.join(AUTH_DIR, 'customer1.json'),
	CUSTOMER_2_STATE: path.join(AUTH_DIR, 'customer2.json'),
};
