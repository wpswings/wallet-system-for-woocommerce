# Wallet System POS (frontend)

A tablet-first app for the `pos/v1` REST API exposed by the Wallet System for WooCommerce plugin. Screens: Login → Register Open → Sales → Payment → Receipt, plus Register Close.

This app shares the plugin's existing root `package.json`/`node_modules` — there is no separate package.json here, just source files plus a Vite config. Run everything from the **plugin root**, not from this folder.

## Setup

From the plugin root (`wallet-system-for-woocommerce/`):

```
npm install
npm run dev:pos
```

By default the dev server proxies `/wp-json` to `http://localhost:10130` (see `vite.config.js`). If your WordPress site runs on a different URL, create `pos-app/.env.local`:

```
WP_SITE_URL=http://your-site.local
```

Then open the URL Vite prints, e.g. `http://localhost:5173/wallet-pos-login` (also reachable from other devices on the same network via `--host`, already enabled). There's no WordPress page involved in dev mode — it's the raw Vite dev server.

## Auth model

There's no custom token system. Login calls `POST /pos/v1/staff/login` with the staff member's WordPress username/email + their POS PIN; on success the server issues a real **WordPress Application Password** for that user, which this app stores in `localStorage` and sends as `Authorization: Basic` on every subsequent request.

Storing an Application Password in `localStorage` is appropriate for a trusted, single-purpose POS terminal device — treat the tablet itself as the security boundary, same as you would a physical card terminal. It is not appropriate to reuse this pattern for a general public-facing web app.

## How it's served in production: a real WordPress page

Rather than pointing a browser at a raw static file URL, POS is turned on from **wp-admin → WP Swings → POS Registers → Enable POS Terminal**. Clicking that:

1. Creates a real WordPress page (`Wallet_System_For_Woocommerce_Pos_Page::enable()` in `includes/class-wallet-system-for-woocommerce-pos-page.php`), e.g. at `/wallet-pos/`.
2. Registers a wildcard rewrite rule so sub-paths under that page (`/wallet-pos/wallet-pos-sales`, etc.) also resolve to it — the same pattern WooCommerce uses for its "My Account" page endpoints. This is what makes a browser refresh on any screen work, since there's no file on disk matching those paths; WordPress's own rewrite system routes them all to the one page.
3. Whenever that page (or a sub-path under it) is requested, `template_redirect` intercepts it, skips the theme entirely, and outputs a minimal HTML shell loading `pos-app/dist/assets/index.js`/`index.css` — no header, footer, or theme styling, since this is a full-screen kiosk app, not a themed page.

The page's own permalink is injected into that shell as `window.WSFW_POS_BASENAME`, which `src/routes.js` reads to scope `BrowserRouter`'s `basename` — so navigation always matches wherever WordPress actually put the page, with nothing hardcoded on the JS side. Route names are still distinctive (`wallet-pos-sales`, not `sales`) as extra insurance against colliding with an unrelated page/route on the site.

Disabling POS from the same screen turns off the redirect but leaves the page in place, so re-enabling later doesn't create a duplicate or lose its URL.

## Building

From the plugin root:

```
npm run build:pos
```

Outputs `pos-app/dist/assets/index.js` and `index.css` with **fixed filenames** (no content hash) — the PHP side references them by that exact, stable path, so no manifest lookup is needed. Cache-busting instead comes from the plugin's version number appended as a query string. `pos-app/dist/` is committed to the plugin (same convention as the existing `build/` folder for the KYC app) — end customers never run `npm install` or a build step themselves.

**Known caveat:** REST calls use WordPress's pretty-permalink routing (`/wp-json/...`). Sites still on the legacy "Plain" permalink structure expose the REST API at `/?rest_route=/...` instead, which this app doesn't currently handle. This affects a small, shrinking minority of sites.

## What's scaffolded vs. deferred

- **No server-side cart.** The spec described `/cart/add` and `/cart/apply-discount` endpoints; this app accumulates the cart client-side in React state and submits it whole at checkout. There's no cross-device/session cart resume — if the page is closed mid-sale, the cart is lost (same as most single-terminal POS software).
- **No discounts yet.** Checkout has no discount field. Add one to both this app and the `/checkout` endpoint if/when needed.
- **No offline queueing.** The spec marks this v2. Every action here requires a live connection to the WordPress site.
- **No barcode/QR scanning UI.** Customer/product search are text inputs; a barcode scanner that types+Enters into a focused text field will work today without extra code, but there's no camera-based scanning.
- **No PWA/installable/offline app-shell.** Deferred for simplicity — can be added back later if wanted (tablet home-screen install, etc.).
