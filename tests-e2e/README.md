# E2E tests — Wallet System for WooCommerce (free)

Playwright end-to-end suite that drives the real plugin against a live WordPress/WooCommerce
site (this repo's Local by Flywheel dev site), covering the free plugin's own feature set.

## Prerequisites

- The site at `WPS_E2E_BASE_URL` (default `http://localhost:10130`) is running, with
  WooCommerce and this plugin active.
- Node 18+.
- Admin credentials (`WPS_E2E_ADMIN_USER` / `WPS_E2E_ADMIN_PASS`, defaults in `config.js`).

## Install

```bash
npm install
npx playwright install chromium
```

## Run

```bash
npm test              # headless, full suite
npm run test:headed   # see the browser
npm run test:ui       # Playwright's interactive UI mode
npm run report        # open the last HTML report
```

Run a single file: `npx playwright test tests/05-wallet-transfer.spec.js`.

## How it's wired together

- **`playwright.config.js`** — two projects: `setup` (auth + baseline config, runs once)
  and `chromium` (the actual specs, depends on `setup`).
- **`auth-setup/auth.setup.js`** — creates two dedicated test customers (`wpse2e_buyer1/2`,
  never real accounts), logs in as admin + each customer, saves `.auth/*.json` storage
  states, and drives the real Settings UI to turn on every feature toggle the suite
  exercises (most are off by default on a fresh install — confirmed by inspecting
  `wp_options` before writing this). Reused via `test.use({ storageState })` per spec file.
- **`wp-tool/index.php`** + **`scripts/wp-bootstrap-exec.sh`** — a tiny CLI that boots this
  site's real `wp-load.php` (via Local's own PHP 8.0.30 binary/php.ini so it hits the right
  MySQL socket — there's no `wp-cli` here) to do setup/teardown and DB-level assertions
  through WordPress's own APIs (`wp_insert_user`, `wc_get_order`, ledger table reads)
  instead of raw SQL or fragile UI-only checks. Called from Node via `fixtures/wpTool.js`.
- **`fixtures/adminSettings.js`** — settings-tab navigation + a save helper. Note: several
  toggles render as Material-style switches whose native `<input>` is visually replaced,
  so Playwright's normal `.check()` can hang or silently no-op; the helper sets the
  checkbox property directly and dispatches `input`/`change`. The save button also
  triggers an async AJAX request with no reliable success indicator, so the helper waits
  for network-idle before returning — skipping that wait was the cause of several
  initially-flaky "setting didn't persist" failures during development.
- **`fixtures/checkout.js`** — this store's checkout is the **React-based WooCommerce
  Cart & Checkout Blocks** experience, not the classic shortcode checkout (confirmed live
  via markup, `wc-block-checkout` classes). Field ids use a hyphen
  (`#billing-first_name`) though the POSTed field name still has an underscore. Also
  empties the cart first — WooCommerce restores a logged-in customer's *persistent cart*
  on login, so leftovers from a previous run/manual session otherwise leak into the next
  test.
- **`fixtures/mailpit.js`** — Local's built-in Mailpit catches this site's outgoing mail;
  used to assert on wallet notification emails without a real SMTP provider.

## Scope

Covers: admin settings (general/cashback/wallet actions/KYC/REST API/system status),
admin manual & bulk wallet credit/debit, wallet recharge → checkout → completion credit,
wallet-to-wallet transfer (by email and by Wallet ID), withdrawal requests, KYC document
submission, the Wallet Payment gateway (full and gated-by-balance), partial payment,
refund-to-wallet, cart-wise cashback (grant + clawback on cancel), signup/daily-visit/
comment-approval bonuses, shortcodes, and the plugin's own REST API.

Not covered (no UI in the free plugin / needs infrastructure this dev environment
doesn't have): the four Elementor-style shortcodes and "Wallet Referral" tab, which are
present in the code but not hooked up in this build (confirmed by full-repo grep); the
POS subsystem, which is a distinct feature area deserving its own test plan.
