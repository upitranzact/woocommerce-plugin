<?php
/*
Plugin Name: UPITranzact Payment Gateway
Plugin URI: https://github.com/upitranzact/woocommerce-plugin
Description: Fast, secure, and reliable payment gateway designed for seamless UPI transactions. Accept payments instantly, manage funds effortlessly, and provide a smooth checkout experience for your customers. Whether you're a business or an individual
Version: 1.0.2
Stable tag: 1.0.2
Author: Team UPITranzact
Text Domain: upitranzact-payment-gateway
WC tested up to: 6.7
Author URI: https://upitranzact.com
Copyright: © 2023-2025 UPITranzact
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin path constant early
define('UPITRANZACT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UPITRANZACT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Enqueue styles for checkout
function upitranzact_enqueue_checkout_styles() {
    // Only enqueue on checkout page
    if (is_checkout()) {
        wp_enqueue_style(
            'upitranzact-checkout-styles',
            UPITRANZACT_PLUGIN_URL . 'assets/css/upitranzact-checkout.css',
            array(),
            '1.0.2'
        );
    }
}
add_action('wp_enqueue_scripts', 'upitranzact_enqueue_checkout_styles');

function upitranzact_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p><strong>UPITranzact Payment Gateway</strong> requires WooCommerce to be installed and active.</p></div>';
        });
        return false;
    }
    return true;
}

add_action('plugins_loaded', 'init_upitranzact_gateway', 11);

add_action('before_woocommerce_init', function() {
	if (class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class))
    {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
        // Ensure HPOS compatibility
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('block_checkout', __FILE__, true);
	}
});

function init_upitranzact_gateway() {
    if (!upitranzact_check_woocommerce()) {
        return;
    }

    define('UPITRANZACT_PLUGIN_DIR', plugin_dir_path(__FILE__));

    require_once UPITRANZACT_PLUGIN_DIR . 'includes/class-wc-gateway-upitranzact.php';
    require_once UPITRANZACT_PLUGIN_DIR . 'includes/helpers.php';

    // Make our gateway initialize first to avoid conflicts
    add_filter('woocommerce_payment_gateways', function ($gateways) {
        $gateways[] = 'WC_Gateway_UpiTranZact';
        return $gateways;
    }, 1);

    // Add WooCommerce Blocks support
    if (class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
        add_action('woocommerce_blocks_loaded', 'upitranzact_register_block_support');
    }
}

function upitranzact_register_block_support() {
    // Include our block integration class
    require_once UPITRANZACT_PLUGIN_DIR . 'src/Block.php';
    
    // Register with WooCommerce Blocks
    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function($registry) {
            $registry->register(new Upitranzact\PaymentServices\WC_Upitranzact_Payment_Services_Block());
        }
    );
    
    // Register block assets - when needed
    add_action('wp_enqueue_scripts', 'upitranzact_register_block_scripts');
}

function upitranzact_register_block_scripts() {
    // Only load on checkout page
    if (!is_checkout()) {
        return;
    }
    
    // Enqueue our vanilla JS files for the blocks checkout
    wp_enqueue_script(
        'upitranzact-blocks-integration',
        UPITRANZACT_PLUGIN_URL . 'assets/js/blocks-integration.js',
        ['wp-element', 'jquery'],
        '1.0.2',
        true
    );
}

// Force refresh of payment gateways when plugin is activated
register_activation_hook(__FILE__, function() {
    add_option('upitranzact_plugin_activated', '1');
});

add_action('admin_init', function() {
    if (get_option('upitranzact_plugin_activated') === '1') {
        delete_option('upitranzact_plugin_activated');
        
        // Reload payment gateways
        if (function_exists('WC')) {
            WC()->payment_gateways()->init();
        }
    }
});

