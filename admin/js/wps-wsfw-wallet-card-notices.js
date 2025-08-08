/**
 * All of the code for notices on your admin-facing JavaScript source
 * should reside in this file.
 *
 * @package           woo-gift-cards-lite
 */
jQuery( document ).ready(
    function($){
        $( document ).on(
            'click',
            '#dismiss-banner',
            function(e){
                e.preventDefault();

                    var data = {
                        action    :'wps_wsfw_dismiss_notice_banner',
                        wps_nonce :wps_wsfw_branner_notice.wps_wsfw_nonce
                    };
                    $.ajax({
                        url  : wps_wsfw_branner_notice.ajaxurl,
                        type : "POST",
                        data : data,
                        success : function(response){
                            window.location.reload();
                        }
                    });
            }
        );
       //shortcode in gunterberg work.
       const { registerBlockType }      = wp.blocks;
        const { TextControl, PanelBody } = wp.components;
        const { useState }               = wp.element;
        const { useBlockProps }          = wp.blockEditor;

        // creating user current points block.
        registerBlockType('wallet/user-old-wallet', {
            title      : 'WPSwings Classic Wallet Dashboard',
            icon       : 'media-document',
            category   : 'widgets',
            attributes : {
                shortcode : { type: 'string', default: '[wps-wallet]' }
            },
            edit: function(props) {
                return wp.element.createElement('div', useBlockProps(),
                    wp.element.createElement(TextControl, {
                        label       : 'Enter Shortcode',
                        value       : props.attributes.shortcode,
                        onChange    : function(shortcode) { props.setAttributes({ shortcode: shortcode }) },
                        placeholder : '[wps-wallet]'
                    }),
                    wp.element.createElement('p', {}, 'Shortcode Output: ' + props.attributes.shortcode)
                );
            },
            save: function(props) {
                return wp.element.createElement('div', useBlockProps.save(), props.attributes.shortcode);
            }
        });

        registerBlockType('wallet/user-wallet-amount', {
            title      : 'WPSwings Wallet Amount',
            icon       : 'media-document',
            category   : 'widgets',
            attributes : {
                shortcode : { type: 'string', default: '[wps-wallet-amount]' }
            },
            edit: function(props) {
                return wp.element.createElement('div', useBlockProps(),
                    wp.element.createElement(TextControl, {
                        label       : 'Enter Shortcode',
                        value       : props.attributes.shortcode,
                        onChange    : function(shortcode) { props.setAttributes({ shortcode: shortcode }) },
                        placeholder : '[wps-wallet-amount]'
                    }),
                    wp.element.createElement('p', {}, 'Shortcode Output: ' + props.attributes.shortcode)
                );
            },
            save: function(props) {
                return wp.element.createElement('div', useBlockProps.save(), props.attributes.shortcode);
            }
        });

        if (wps_wsfw_branner_notice.is_pro_plugin == 1){
            
            registerBlockType('wallet/user-new-wallet', {
                title      : 'WPSwings Modern Wallet Dashboard',
                icon       : 'media-document',
                category   : 'widgets',
                attributes : {
                    shortcode : { type: 'string', default: '[wps-wallet-dashboard]' }
                },
                edit: function(props) {
                    return wp.element.createElement('div', useBlockProps(),
                        wp.element.createElement(TextControl, {
                            label       : 'Enter Shortcode',
                            value       : props.attributes.shortcode,
                            onChange    : function(shortcode) { props.setAttributes({ shortcode: shortcode }) },
                            placeholder : '[wps-wallet-dashboard]'
                        }),
                        wp.element.createElement('p', {}, 'Shortcode Output: ' + props.attributes.shortcode)
                    );
                },
                save: function(props) {
                    return wp.element.createElement('div', useBlockProps.save(), props.attributes.shortcode);
                }
            });
        }
        //shortcode in gunterberg work.
    }




);