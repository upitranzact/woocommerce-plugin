<?php
/*
Plugin Name: UPITranzact: Payment Gateway
Plugin URI: https://upitranzact.com
Description: Fast, secure, and reliable payment gateway designed for seamless UPI transactions. Accept payments instantly, manage funds effortlessly, and provide a smooth checkout experience for your customers. Whether you're a business or an individual
Version: 1.0.2
Stable tag: 1.0.2
Author: Team UPITranzact
WC tested up to: 9.1.2
Author URI: https://upitranzact.com
Copyright: © 2023-2025 UPITranzact
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

