<?php
/**
 * Main WooCommerce payment gateway class for UPITranzact.
 *
 * This class:
 * - Registers UPITranzact as a WooCommerce payment method
 * - Defines admin settings fields
 * - Handles checkout payment initiation
 * - Routes gateway callbacks for payment verification
 *
 * @package UPITranzact_WooCommerce
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Gateway_UpiTranZact
 *
 * Extends WooCommerce core payment gateway functionality.
 */
class WC_Gateway_UpiTranZact extends WC_Payment_Gateway {

	/**
	 * Constructor.
	 *
	 * Initializes gateway properties, settings, hooks,
	 * and registers callback handlers.
	 */
	public function __construct() {

		// Unique payment gateway ID.
		$this->id = 'upitranzact';

		// Admin-visible gateway title and description.
		$this->method_title       = __( 'UPITranzact', 'upitranzact-payment-gateway' );
		$this->method_description = __( 'Accept payments via UPITranzact Payment Gateway.', 'upitranzact-payment-gateway' );

		// Supported payment features.
		$this->supports = array( 'products' );

		/**
		 * Set gateway icon.
		 *
		 * Falls back to hosted logo if local asset is missing.
		 */
		$icon_url = UPITRANZACT_PLUGIN_URL . 'assets/upitranzact-logo.png';
		if ( ! file_exists( UPITRANZACT_PLUGIN_DIR . 'assets/upitranzact-logo.png' ) ) {
			$icon_url = 'https://www.upitranzact.com/logo/upitranzact-logo.png';
		}
		$this->icon = apply_filters( 'woocommerce_upitranzact_icon', $icon_url );

		// Initialize admin form fields and saved settings.
		$this->init_form_fields();
		$this->init_settings();

		// Load configured gateway options.
		$this->enabled     = $this->get_option( 'enabled' );
		$this->title       = $this->get_option( 'title' );
		$this->mid         = $this->get_option( 'mid' );
		$this->public_key  = $this->get_option( 'public_key' );
		$this->secret_key  = $this->get_option( 'secret_key' );
		$this->description = $this->get_option(
			'description',
			__( 'Pay securely with UPITranzact - UPI', 'upitranzact-payment-gateway' )
		);

		// Save admin settings.
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array( $this, 'process_admin_options' )
		);

		// Register payment response handler (return URL).
		add_action(
			'woocommerce_api_' . strtolower( get_class( $this ) ),
			array( $this, 'handle_response' )
		);
	}

	/**
	 * Define admin configuration fields for the gateway.
	 *
	 * These fields appear under WooCommerce → Payments → UPITranzact.
	 *
	 * @return void
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'      => array(
				'title'   => __( 'Enable/Disable', 'upitranzact-payment-gateway' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable UPITranzact Payment', 'upitranzact-payment-gateway' ),
				'default' => 'yes',
			),
			'title'        => array(
				'title'       => __( 'Title', 'upitranzact-payment-gateway' ),
				'type'        => 'text',
				'description' => __( 'Title customers see at checkout.', 'upitranzact-payment-gateway' ),
				'default'     => __( 'Pay via UPITranzact', 'upitranzact-payment-gateway' ),
				'desc_tip'    => true,
			),
			'description'  => array(
				'title'       => __( 'Description', 'upitranzact-payment-gateway' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description shown to customers during checkout.', 'upitranzact-payment-gateway' ),
				'default'     => __( 'Pay securely using UPI apps like PhonePe, Google Pay, Paytm, BHIM, and more.', 'upitranzact-payment-gateway' ),
			),
			'mid'          => array(
				'title' => __( 'Merchant ID', 'upitranzact-payment-gateway' ),
				'type'  => 'text',
			),
			'public_key'   => array(
				'title' => __( 'Public Key', 'upitranzact-payment-gateway' ),
				'type'  => 'text',
			),
			'secret_key'   => array(
				'title' => __( 'Secret Key', 'upitranzact-payment-gateway' ),
				'type'  => 'text',
			),
		);
	}

	/**
	 * Check if the gateway is available for use.
	 *
	 * Ensures the gateway is enabled and all required
	 * credentials are configured.
	 *
	 * @return bool
	 */
	public function is_available() {
		return (
			$this->enabled === 'yes' &&
			! empty( $this->mid ) &&
			! empty( $this->public_key ) &&
			! empty( $this->secret_key )
		);
	}

	/**
	 * Process payment during WooCommerce checkout.
	 *
	 * Delegates payment creation logic to helper functions.
	 *
	 * @param int $order_id WooCommerce order ID.
	 * @return array|null
	 */
	public function process_payment( $order_id ) {
		require_once UPITRANZACT_PLUGIN_DIR . 'includes/helpers.php';
		return process_upitranzact_payment( $order_id, $this );
	}

	/**
	 * Handle return response from UPITranzact gateway.
	 *
	 * Triggered via WooCommerce API callback URL.
	 *
	 * @return void
	 */
	public function handle_response() {
		require_once UPITRANZACT_PLUGIN_DIR . 'includes/helpers.php';
		handle_upitranzact_response();
	}

	/**
	 * Get icon URL for supported UPI apps.
	 *
	 * Used in checkout UI and block integration.
	 *
	 * @param string $app_name UPI app identifier.
	 * @return string
	 */
	public function get_icon_url( $app_name = '' ) {

		if ( $app_name === 'phonepe' ) {
			$icon_path = 'assets/icons/icons8-phone-pe.svg';
		} elseif ( $app_name === 'gpay' ) {
			$icon_path = 'assets/icons/icons8-google-pay.svg';
		} elseif ( $app_name === 'paytm' ) {
			$icon_path = 'assets/icons/icons8-paytm.svg';
		} elseif ( $app_name === 'bhim' ) {
			$icon_path = 'assets/icons/icons8-bhim.svg';
		} else {
			$icon_path = 'assets/upitranzact-logo.png';
		}

		return UPITRANZACT_PLUGIN_URL . $icon_path;
	}

	/**
	 * Output payment method UI on classic checkout page.
	 *
	 * Displays supported UPI apps with icons for visual clarity.
	 *
	 * @return void
	 */
	public function payment_fields() {

		echo '<div class="upitranzact-payment-description">';
		echo '<h4>' . esc_html__( 'Pay with any UPI App', 'upitranzact-payment-gateway' ) . '</h4>';

		echo '<div class="upitranzact-payment-methods">';
		echo '<span class="upitranzact-payment-method-info">' . esc_html__( 'Pay with:', 'upitranzact-payment-gateway' ) . '</span>';
		echo '<div class="upitranzact-upi-apps">';

		echo '<div class="upitranzact-upi-app phonepe">';
		echo '<img src="' . esc_url( $this->get_icon_url( 'phonepe' ) ) . '" alt="PhonePe" />';
		echo '<span>PhonePe</span>';
		echo '</div>';

		echo '<div class="upitranzact-upi-app gpay">';
		echo '<img src="' . esc_url( $this->get_icon_url( 'gpay' ) ) . '" alt="Google Pay" />';
		echo '<span>GPay</span>';
		echo '</div>';

		echo '<div class="upitranzact-upi-app paytm">';
		echo '<img src="' . esc_url( $this->get_icon_url( 'paytm' ) ) . '" alt="Paytm" />';
		echo '<span>Paytm</span>';
		echo '</div>';

		echo '<div class="upitranzact-upi-app bhim">';
		echo '<img src="' . esc_url( $this->get_icon_url( 'bhim' ) ) . '" alt="BHIM UPI" />';
		echo '<span>BHIM</span>';
		echo '</div>';

		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}