<?php
/**
 * WooCommerce Blocks payment method integration for UPITranzact.
 *
 * This file registers UPITranzact as a supported payment method
 * for the WooCommerce block-based checkout experience.
 *
 * @package UPITranzact_WooCommerce
 */

namespace Upitranzact\Blocks;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Class WC_Upitranzact_Blocks
 *
 * Extends WooCommerce Blocks payment method abstraction
 * to expose UPITranzact in block-based checkout.
 */
class WC_Upitranzact_Blocks extends AbstractPaymentMethodType {

	/**
	 * Payment method identifier.
	 *
	 * This must match the gateway ID used in the WooCommerce gateway class.
	 *
	 * @var string
	 */
	protected $name = 'upitranzact';

	/**
	 * Initialize payment method settings.
	 *
	 * Loads gateway settings saved in WooCommerce.
	 *
	 * @return void
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_upitranzact_settings', array() );
	}

	/**
	 * Determine if the payment method is active.
	 *
	 * Uses the main WooCommerce gateway availability logic
	 * to ensure consistent enable/disable behavior.
	 *
	 * @return bool
	 */
	public function is_active() {
		$gateway = new \WC_Gateway_UpiTranZact();
		return $gateway->is_available();
	}

	/**
	 * Register and return script handles required for blocks checkout.
	 *
	 * This script is responsible for rendering the UPITranzact
	 * payment option inside WooCommerce blocks.
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		$script_path = UPITRANZACT_PLUGIN_URL . 'assets/js/blocks-integration.js';

		$dependencies = array(
			'wp-element',
			'wc-blocks-registry',
		);

		wp_register_script(
			'upitranzact-payment-blocks',
			$script_path,
			$dependencies,
			'1.0.3',
			true
		);

		// Pass dynamic gateway data to the frontend script.
		$this->register_payment_method_data();

		return array( 'upitranzact-payment-blocks' );
	}

	/**
	 * Localize gateway data for block-based checkout scripts.
	 *
	 * Provides title, description, supported features,
	 * and UPI app icons to the frontend block.
	 *
	 * @return void
	 */
	protected function register_payment_method_data() {
		$gateway = new \WC_Gateway_UpiTranZact();

		wp_localize_script(
			'upitranzact-payment-blocks',
			'upitranzact_data',
			array(
				'title'       => ! empty( $this->settings['title'] )
					? $this->settings['title']
					: __( 'UPITranzact', 'upitranzact-payment-gateway' ),
				'description' => ! empty( $this->settings['description'] )
					? $this->settings['description']
					: __( 'Pay with any UPI App', 'upitranzact-payment-gateway' ),
				'icons'       => array(
					'phonepe' => $gateway->get_icon_url( 'phonepe' ),
					'gpay'    => $gateway->get_icon_url( 'gpay' ),
					'paytm'   => $gateway->get_icon_url( 'paytm' ),
					'bhim'    => $gateway->get_icon_url( 'bhim' ),
				),
				'supports'    => array( 'products' ),
			)
		);
	}

	/**
	 * Provide static payment method data for blocks checkout.
	 *
	 * Used by WooCommerce Blocks to display
	 * the payment method during checkout.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		return array(
			'title'       => ! empty( $this->settings['title'] )
				? $this->settings['title']
				: __( 'UPITranzact', 'upitranzact-payment-gateway' ),
			'description' => ! empty( $this->settings['description'] )
				? $this->settings['description']
				: __( 'Pay with any UPI App', 'upitranzact-payment-gateway' ),
			'supports'    => array( 'products' ),
		);
	}
}