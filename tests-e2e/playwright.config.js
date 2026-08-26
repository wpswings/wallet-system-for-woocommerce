const { defineConfig, devices } = require('@playwright/test');
const { BASE_URL } = require('./config');

module.exports = defineConfig({
	testDir: '.',
	fullyParallel: false,
	workers: 1,
	retries: 0,
	reporter: [['list'], ['html', { open: 'never', outputFolder: 'playwright-report' }]],
	timeout: 60000,
	expect: { timeout: 10000 },
	use: {
		baseURL: BASE_URL,
		trace: 'retain-on-failure',
		screenshot: 'only-on-failure',
		video: 'retain-on-failure',
	},
	projects: [
		{
			name: 'setup',
			testMatch: /auth-setup\/.*\.setup\.js/,
		},
		{
			name: 'chromium',
			testMatch: /tests\/.*\.spec\.js/,
			use: {
				...devices['Desktop Chrome'],
			},
			dependencies: ['setup'],
		},
	],
});
