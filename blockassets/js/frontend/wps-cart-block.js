/**
 * Wallet Block Cart - Display wallet balance before estimated total
 */

(function() {
	'use strict';

	// Wait for DOM to be ready
	const initWalletCartBlock = () => {
		// Check if wallet data is available
		if (typeof wsfwCartBlockData === 'undefined') {
			return;
		}

		// Check if placement is enabled
		if (!wsfwCartBlockData.placement_enabled) {
			return;
		}

		// Check if user has wallet balance
		if (parseFloat(wsfwCartBlockData.wallet_balance) <= 0) {
			return;
		}

		// Check if we're on a block-based cart
		const cartBlock = document.querySelector('.wp-block-woocommerce-cart');
		if (!cartBlock) {
			return;
		}

		// Function to add wallet balance notice banner above product table
		const addWalletBalanceAboveTable = () => {
			// Find the cart items container
			const cartItemsContainer = document.querySelector('.wc-block-cart-items');

			if (!cartItemsContainer) {
				return false;
			}

			// Check if we already added wallet notice banner
			if (document.querySelector('.wps-wallet-notice-banner-block')) {
				return true;
			}

			// Create wallet notice banner
			const walletNotice = document.createElement('div');
			walletNotice.className = 'wps-wallet-notice-banner-block woocommerce-info';
			walletNotice.innerHTML = `
				<span class="wps-wallet-notice-icon">ℹ️</span>
				<span class="wps-wallet-notice-message">
					You have ${wsfwCartBlockData.wallet_balance_formatted} in your wallet. It can be applied at checkout.
				</span>
			`;

			// Insert before cart items
			if (cartItemsContainer.parentNode) {
				cartItemsContainer.parentNode.insertBefore(walletNotice, cartItemsContainer);
				return true;
			}

			return false;
		};

		// Function to add wallet balance box before estimated total
		const addWalletBalanceBox = () => {
			// Find the estimated total row
			const totalRows = document.querySelectorAll('.wc-block-components-totals-footer-item');

			if (totalRows.length === 0) {
				return false;
			}

			// Check if we already added wallet balance box
			if (document.querySelector('.wps-wallet-block-cart-balance')) {
				return true;
			}

			// Find the last total row (estimated total)
			const estimatedTotalRow = totalRows[totalRows.length - 1];

			// Create wallet balance box
			const walletBox = document.createElement('div');
			walletBox.className = 'wc-block-components-totals-item wps-wallet-block-cart-balance';
			walletBox.innerHTML = `
				<div class="wps-wallet-balance-container">
					<div class="wps-wallet-balance-row">
						<span class="wps-wallet-balance-label">${wsfwCartBlockData.label_wallet_balance}</span>
						<span class="wps-wallet-balance-amount">${wsfwCartBlockData.wallet_balance_formatted}</span>
					</div>
					<div class="wps-wallet-balance-subtitle">
						${wsfwCartBlockData.label_available_to_spend}
					</div>
				</div>
			`;

			// Insert before estimated total
			if (estimatedTotalRow && estimatedTotalRow.parentNode) {
				estimatedTotalRow.parentNode.insertBefore(walletBox, estimatedTotalRow);
				return true;
			}

			return false;
		};

		// Get placement option
		const placementOption = wsfwCartBlockData.placement_option;

		// Add displays based on placement option
		// option_a = Inside Cart Totals only
		// option_b = Notice Banner Above Cart Table only

		if (placementOption === 'option_b') {
			// Notice Banner - Above Cart Table
			if (!addWalletBalanceAboveTable()) {
				setTimeout(addWalletBalanceAboveTable, 500);
			}

			// Watch for cart items changes
			const cartItemsWrapper = document.querySelector('.wp-block-woocommerce-cart-items-block');
			if (cartItemsWrapper) {
				const itemsObserver = new MutationObserver(() => {
					addWalletBalanceAboveTable();
				});

				itemsObserver.observe(cartItemsWrapper, {
					childList: true,
					subtree: true
				});
			}
		} else if (placementOption === 'option_a') {
			// Inside Cart Totals - Above Estimated Total
			if (!addWalletBalanceBox()) {
				setTimeout(addWalletBalanceBox, 500);
			}

			// Watch for cart totals changes
			const cartTotals = document.querySelector('.wc-block-components-totals-wrapper');
			if (cartTotals) {
				const observer = new MutationObserver(() => {
					addWalletBalanceBox();
				});

				observer.observe(cartTotals, {
					childList: true,
					subtree: true
				});
			}
		}
	};

	// Initialize on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initWalletCartBlock);
	} else {
		initWalletCartBlock();
	}

})();
