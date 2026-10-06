/**
 * Wallet Modal Enhancements
 * Additional functionality for the redesigned wallet modal
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        // Override the edit_wallet click handler for better modal handling
        $(document).on("click", ".edit_wallet", function(e) {
            // Clear previous values
            $('#wps_wallet-edit-popup-input').val('');
            $('#wps_wallet-edit-popup-transaction-detail').val('');
            $('#wps_wallet_contains_bonus').prop('checked', false);
            $('#wps_wallet-bonus-amount').val('');
            $('#wps_wallet_expiry_period').val('none');
            $('#wps_wallet_custom_expiry_days').val('');
            $('input[name="action_type"]').prop('checked', false);
            $('.error').hide().html('');
            $('#wps_wallet_submit_val').prop('disabled', false);

            // Hide conditional fields
            $('.wps_wallet-bonus-field').hide();
            $('.wps_wallet-custom-expiry-field').hide();

            // Add show class for animation
            setTimeout(function() {
                $('.wps_wallet-edit--popupwrap').addClass('show');
            }, 10);
        });

        // Enhanced close functionality
        $(document).on("click", "#close_wallet_form", function(e) {
            $('.wps_wallet-edit--popupwrap').removeClass('show');

            // Wait for animation to complete before hiding
            setTimeout(function() {
                $('.wps_wallet-edit--popupwrap').hide();

                // Clear form
                $('#wps_wallet-edit-popup-input').val('');
                $('#wps_wallet-edit-popup-transaction-detail').val('');
                $('input[name="action_type"]').prop('checked', false);
                $('.error').hide().html('');
                $('#wps_wallet_submit_val').prop('disabled', false);
                $('.wps_wallet-edit--popupwrap').find('.userid').remove();
            }, 300);
        });

        // Close modal when clicking outside
        $(document).on("click", ".wps_wallet-edit--popupwrap", function(e) {
            if ($(e.target).hasClass('wps_wallet-edit--popupwrap')) {
                $('#close_wallet_form').trigger('click');
            }
        });

        // Close modal on Escape key
        $(document).on("keydown", function(e) {
            if (e.key === 'Escape' && $('.wps_wallet-edit--popupwrap').hasClass('show')) {
                $('#close_wallet_form').trigger('click');
            }
        });

        // Enable submit button when form has changes
        $(document).on("input change", "#wps_wallet-edit-popup-input, #wps_wallet-edit-popup-transaction-detail, input[name='action_type']", function() {
            var amount = $('#wps_wallet-edit-popup-input').val();
            var action = $('input[name="action_type"]:checked').val();

            // Enable button only if amount and action are selected
            if (amount && action) {
                $('#wps_wallet_submit_val').prop('disabled', false);
            }
        });

        // Validate amount input
        $(document).on("input", "#wps_wallet-edit-popup-input", function() {
            var value = $(this).val();

            // Remove non-numeric characters except decimal point
            if (!/^\d*\.?\d*$/.test(value)) {
                $(this).val(value.slice(0, -1));
            }

            // Clear error when user starts typing
            $('.error').hide().html('');
        });

        // Radio button visual feedback
        $(document).on("change", "input[name='action_type']", function() {
            $('.wps_wallet-radio-option').removeClass('selected');
            $(this).closest('.wps_wallet-radio-option').addClass('selected');
        });

        // Add loading state to submit button
        var originalSubmitValue = '';
        $(document).on("click", "#wps_wallet_submit_val", function() {
            if (!$(this).prop('disabled')) {
                originalSubmitValue = $(this).val();
                $(this).val('Processing...').prop('disabled', true);

                // Reset button text after 10 seconds if not redirected
                setTimeout(function() {
                    if (originalSubmitValue) {
                        $('#wps_wallet_submit_val').val(originalSubmitValue).prop('disabled', false);
                    }
                }, 10000);
            }
        });

        // Prevent form submission on Enter key in text fields
        $(document).on("keypress", "#wps_wallet-edit-popup-input, #wps_wallet-edit-popup-transaction-detail", function(e) {
            if (e.which === 13) {
                e.preventDefault();
                // Trigger submit button click instead
                $('#wps_wallet_submit_val').trigger('click');
            }
        });

        // Toggle bonus amount field when checkbox is changed
        $(document).on("change", "#wps_wallet_contains_bonus", function() {
            if ($(this).is(':checked')) {
                $('.wps_wallet-bonus-field').slideDown(200);
            } else {
                $('.wps_wallet-bonus-field').slideUp(200);
                $('#wps_wallet-bonus-amount').val('');
            }
        });

        // Toggle custom expiry days field when dropdown changes
        $(document).on("change", "#wps_wallet_expiry_period", function() {
            if ($(this).val() === 'custom') {
                $('.wps_wallet-custom-expiry-field').slideDown(200);
            } else {
                $('.wps_wallet-custom-expiry-field').slideUp(200);
                $('#wps_wallet_custom_expiry_days').val('');
            }
        });

        // Validate bonus amount doesn't exceed total amount
        $(document).on("input", "#wps_wallet-bonus-amount", function() {
            var totalAmount = parseFloat($('#wps_wallet-edit-popup-input').val()) || 0;
            var bonusAmount = parseFloat($(this).val()) || 0;

            if (bonusAmount > totalAmount) {
                $(this).val(totalAmount);
                alert('Bonus amount cannot exceed total amount');
            }
        });

        // Show/hide expiry fields based on credit action
        $(document).on("change", "input[name='action_type']", function() {
            var action = $(this).val();

            // Only show expiry options for credit action
            if (action === 'credit') {
                $('#wps_wallet_expiry_period').closest('.wps_wallet-edit-popup-field').show();

                // Show custom field if custom is selected
                if ($('#wps_wallet_expiry_period').val() === 'custom') {
                    $('.wps_wallet-custom-expiry-field').show();
                }
            } else {
                // Hide expiry fields for debit
                $('#wps_wallet_expiry_period').closest('.wps_wallet-edit-popup-field').hide();
                $('.wps_wallet-custom-expiry-field').hide();
                $('#wps_wallet_expiry_period').val('none');
                $('#wps_wallet_custom_expiry_days').val('');
            }
        });

    });

})(jQuery);
