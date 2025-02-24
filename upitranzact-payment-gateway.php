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
	}
});

function init_upitranzact_gateway() {
    if (!upitranzact_check_woocommerce()) {
        return;
    }

    define('UPITRANZACT_PLUGIN_DIR', plugin_dir_path(__FILE__));

    require_once UPITRANZACT_PLUGIN_DIR . 'includes/class-wc-gateway-upitranzact.php';
    require_once UPITRANZACT_PLUGIN_DIR . 'includes/helpers.php';

    add_filter('woocommerce_payment_gateways', function ($gateways) {
        $gateways[] = 'WC_Gateway_UpiTranZact';
        return $gateways;
    });
}

