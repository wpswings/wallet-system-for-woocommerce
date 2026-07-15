import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// WP_SITE_URL points the dev proxy at your local WordPress site so the
// browser talks to /wp-json on the same origin as the app (no CORS setup
// needed in dev). Override via a .env.local file: WP_SITE_URL=http://localhost:XXXXX
const WP_SITE_URL = process.env.WP_SITE_URL || 'http://localhost:10130';

// This app shares the plugin's root package.json/node_modules, so its
// scripts are run from the plugin root (`npm run dev:pos` / `build:pos`).
// Pin root to this file's own directory — Vite otherwise resolves index.html
// relative to the current working directory, not the config file location.
const appRoot = fileURLToPath( new URL( '.', import.meta.url ) );

export default defineConfig( {
	root: appRoot,
	plugins: [ react() ],
	build: {
		// Fixed filenames (no content hash) so the PHP side
		// (Wallet_System_For_Woocommerce_Pos_Page) can enqueue them by a
		// known, stable path instead of reading a manifest at runtime.
		// Cache-busting is handled via the plugin's version number in the
		// enqueue call instead.
		rollupOptions: {
			output: {
				entryFileNames: 'assets/index.js',
				chunkFileNames: 'assets/[name].js',
				assetFileNames: 'assets/index[extname]',
			},
		},
	},
	server: {
		host: true,
		proxy: {
			'/wp-json': {
				target: WP_SITE_URL,
				changeOrigin: true,
			},
		},
	},
} );
