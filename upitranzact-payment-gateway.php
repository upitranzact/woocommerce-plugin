<?php
/**
 * Plugin Name: UPITranzact Payment Gateway
 * Plugin URI: https://github.com/upitranzact/upitranzact-woocommerce-plugin
 * Description: Fast, secure, and reliable payment gateway designed for seamless UPI transactions. Accept payments instantly, manage funds effortlessly, and provide a smooth checkout experience for WooCommerce customers in India.
 * Version: 1.1.0
 * Stable tag: 1.1.0
 * Author: Team UPITranzact
 * Author URI: https://www.upitranzact.com
 * Text Domain: upitranzact-payment-gateway
 * WC tested up to: 6.9
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package UPITranzact_WooCommerce
 */

// Exit if accessed directly to prevent unauthorized execution.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin absolute directory path.
 *
 * Used for including plugin files.
 */
define( 'UPITRANZACT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin URL path.
 *
 * Used for enqueuing assets like CSS and JS files.
 */
define( 'UPITRANZACT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Enqueue frontend styles on WooCommerce checkout page only.
 *
 * This stylesheet is responsible for styling the UPI QR
 * and checkout UI elements specific to UPITranzact.
 *
 * @return void
 */
function upitranzact_enqueue_checkout_styles() {
	if ( is_checkout() ) {
		wp_enqueue_style(
			'upitranzact-checkout-styles',
			UPITRANZACT_PLUGIN_URL . 'assets/css/upitranzact-checkout.css',
			array(),
			'1.0.2'
		);
	}
}
add_action( 'wp_enqueue_scripts', 'upitranzact_enqueue_checkout_styles' );

/**
 * Check if WooCommerce is installed and active.
 *
 * Displays an admin notice if WooCommerce is not available,
 * since this plugin depends on WooCommerce APIs.
 *
 * @return bool
 */
function upitranzact_check_woocommerce() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p><strong>UPITranzact Payment Gateway</strong> requires WooCommerce to be installed and active.</p></div>';
			}
		);
		return false;
	}
	return true;
}

/**
 * Initialize UPITranzact payment gateway after plugins are loaded.
 *
 * Priority 11 ensures WooCommerce is loaded before initializing the gateway.
 */
add_action( 'plugins_loaded', 'init_upitranzact_gateway', 11 );

/**
 * Declare compatibility with WooCommerce features.
 *
 * - Custom Order Tables (HPOS)
 * - Cart & Checkout Blocks
 * - Block-based Checkout
 *
 * This prevents compatibility warnings in WooCommerce settings.
 */
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'block_checkout', __FILE__, true );
		}
	}
);

/**
 * Load UPITranzact gateway and helper files.
 *
 * Registers the payment gateway class with WooCommerce.
 *
 * @return void
 */
function init_upitranzact_gateway() {
	if ( ! upitranzact_check_woocommerce() ) {
		return;
	}

	// Load main payment gateway class.
	require_once UPITRANZACT_PLUGIN_DIR . 'includes/class-wc-gateway-upitranzact.php';

	// Load helper functions used across the plugin.
	require_once UPITRANZACT_PLUGIN_DIR . 'includes/helpers.php';

	// Register the gateway with WooCommerce.
	add_filter(
		'woocommerce_payment_gateways',
		function ( $gateways ) {
			$gateways[] = 'WC_Gateway_UpiTranZact';
			return $gateways;
		},
		1
	);
}

/**
 * Register UPITranzact payment method for WooCommerce Blocks.
 *
 * Ensures compatibility with block-based checkout experience.
 */
add_action(
	'woocommerce_blocks_loaded',
	function() {
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			require_once UPITRANZACT_PLUGIN_DIR . 'src/Block.php';

			add_action(
				'woocommerce_blocks_payment_method_type_registration',
				function( $registry ) {
					$registry->register( new \Upitranzact\Blocks\WC_Upitranzact_Blocks() );
				}
			);
		}
	}
);

/**
 * Plugin activation hook.
 *
 * Sets a temporary option to reinitialize payment gateways
 * after activation.
 */
register_activation_hook(
	__FILE__,
	function() {
		add_option( 'upitranzact_plugin_activated', '1' );
	}
);

/**
 * Reinitialize WooCommerce payment gateways after activation.
 *
 * This ensures the UPITranzact gateway is available immediately
 * without requiring a manual refresh.
 */
add_action(
	'admin_init',
	function() {
		if ( get_option( 'upitranzact_plugin_activated' ) === '1' ) {
			delete_option( 'upitranzact_plugin_activated' );

			if ( function_exists( 'WC' ) ) {
				WC()->payment_gateways()->init();
			}
		}
	}
);