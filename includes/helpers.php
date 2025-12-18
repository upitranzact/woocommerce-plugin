<?php
/**
 * Helper functions for processing and validating UPITranzact payments.
 *
 * This file handles:
 * - Creating UPITranzact payment orders
 * - Redirecting customers to UPI payment flow
 * - Validating payment status on return
 * - Updating WooCommerce order status accordingly
 *
 * @package UPITranzact_WooCommerce
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a UPITranzact payment order and initiate payment flow.
 *
 * Called during WooCommerce checkout when the UPITranzact
 * payment method is selected.
 *
 * @param int                     $order_id WooCommerce order ID.
 * @param WC_Gateway_UpiTranZact   $gateway  Gateway instance with credentials.
 *
 * @return array|null
 */
function process_upitranzact_payment( $order_id, $gateway ) {

	// Fetch WooCommerce order.
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	// Generate a unique UPITranzact order reference.
	$unique_order_id = 'utz_' . uniqid();

	/**
	 * Prepare payload for UPITranzact create order API.
	 *
	 * Only required transaction and customer details
	 * are sent to the payment gateway.
	 */
	$data = array(
		'mid'             => $gateway->mid,
		'amount'          => number_format( $order->get_total(), 2, '.', '' ),
		'order_id'        => $unique_order_id,
		'redirect_url'    => add_query_arg(
			array(
				'wc-api'   => 'WC_Gateway_UPITranzact',
				'txn_id'   => $unique_order_id,
				'order_id' => $order_id,
			),
			home_url( '/wc-api/WC_Gateway_UPITranzact/' )
		),
		'note'            => sprintf(
			__( 'Payment for order %s', 'upitranzact-payment-gateway' ),
			$order_id
		),
		'customer_name'   => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
		'customer_email'  => $order->get_billing_email(),
		'customer_mobile' => $order->get_billing_phone(),
		'woo_order_id'    => $order_id,
	);

	// Prepare Basic Authentication header.
	$auth_header = base64_encode( $gateway->public_key . ':' . $gateway->secret_key );

	/**
	 * Send create order request to UPITranzact API.
	 */
	$response = wp_remote_post(
		'https://api.upitranzact.com/v1/payments/createOrderRequest',
		array(
			'headers'   => array(
				'Authorization' => 'Basic ' . $auth_header,
				'Content-Type'  => 'application/json',
			),
			'body'      => wp_json_encode( $data ),
			'method'    => 'POST',
			'timeout'   => 30,
			'sslverify' => true,
		)
	);

	// Handle network or connection errors.
	if ( is_wp_error( $response ) ) {
		wc_add_notice(
			__( 'Connection error:', 'upitranzact-payment-gateway' ) . ' ' . $response->get_error_message(),
			'error'
		);
		return;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	// Validate API response structure.
	if ( empty( $body ) || ! is_array( $body ) ) {
		wc_add_notice(
			__( 'Invalid response from payment gateway', 'upitranzact-payment-gateway' ),
			'error'
		);
		return;
	}

	// On success, store gateway order reference and redirect user.
	if ( ! empty( $body['status'] ) && ! empty( $body['data']['payment_url'] ) ) {
		update_post_meta( $order_id, '_upitranzact_order_id', $unique_order_id );

		return array(
			'result'   => 'success',
			'redirect' => $body['data']['payment_url'],
		);
	}

	// Handle gateway-side failure response.
	wc_add_notice(
		__( 'Payment error: ', 'upitranzact-payment-gateway' ) . ( $body['msg'] ?? 'Unknown error' ),
		'error'
	);
}

/**
 * Handle return callback from UPITranzact after payment attempt.
 *
 * This endpoint verifies:
 * - Order identity
 * - Gateway order reference
 * - Payment status via API
 * - Payment amount integrity
 *
 * Updates WooCommerce order status accordingly.
 *
 * @return void
 */
function handle_upitranzact_response() {

	// Validate required callback parameters.
	if ( ! isset( $_GET['order_id'], $_GET['txn_id'], $_GET['wc-api'] ) ) {
		wp_die( __( 'Invalid request', 'upitranzact-payment-gateway' ), '', array( 'response' => 400 ) );
	}

	// Ensure callback is routed to correct gateway.
	if ( $_GET['wc-api'] !== 'WC_Gateway_UPITranzact' ) {
		wp_die( __( 'Invalid callback', 'upitranzact-payment-gateway' ), '', array( 'response' => 400 ) );
	}

	$order_id = sanitize_text_field( $_GET['order_id'] );
	$txn_id   = sanitize_text_field( $_GET['txn_id'] );
	$order    = wc_get_order( $order_id );

	if ( ! $order ) {
		wp_die( __( 'Order not found', 'upitranzact-payment-gateway' ), '', array( 'response' => 404 ) );
	}

	// Redirect immediately if order is already paid.
	if ( $order->is_paid() ) {
		wp_redirect( $order->get_checkout_order_received_url() );
		exit;
	}

	$gateway         = new WC_Gateway_UpiTranZact();
	$stored_order_id = get_post_meta( $order->get_id(), '_upitranzact_order_id', true );

	// Validate transaction reference.
	if ( $stored_order_id !== $txn_id ) {
		$order->update_status(
			'failed',
			__( 'Order ID mismatch', 'upitranzact-payment-gateway' )
		);
		wp_redirect( wc_get_checkout_url() );
		exit;
	}

	/**
	 * Verify payment status with UPITranzact API.
	 */
	$response = wp_remote_post(
		'https://api.upitranzact.com/v1/payments/checkPaymentStatus',
		array(
			'body'      => wp_json_encode(
				array(
					'mid'      => $gateway->mid,
					'order_id' => $txn_id,
				)
			),
			'headers'   => array(
				'Authorization' => 'Basic ' . base64_encode( $gateway->public_key . ':' . $gateway->secret_key ),
				'Content-Type'  => 'application/json',
			),
			'timeout'   => 30,
			'sslverify' => true,
		)
	);

	// Handle verification connection errors.
	if ( is_wp_error( $response ) ) {
		$order->update_status(
			'failed',
			__( 'Payment verification failed: Gateway connection error', 'upitranzact-payment-gateway' )
		);
		wp_redirect( wc_get_checkout_url() );
		exit;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	// Validate verification response.
	if ( empty( $body ) || ! is_array( $body ) ) {
		$order->update_status(
			'failed',
			__( 'Invalid response during verification', 'upitranzact-payment-gateway' )
		);
		wp_redirect( wc_get_checkout_url() );
		exit;
	}

	// Confirm successful payment and amount integrity.
	if ( $body['status'] && $body['data']['order_id'] === $txn_id ) {

		$paid_amount  = floatval( $body['data']['amount'] ?? 0 );
		$order_amount = floatval( $order->get_total() );

		// Verify payment amount matches order total.
		if ( abs( $paid_amount - $order_amount ) > 0.01 ) {
			$order->update_status(
				'failed',
				sprintf(
					__( 'Payment amount mismatch. Expected: %s, Received: %s', 'upitranzact-payment-gateway' ),
					$order_amount,
					$paid_amount
				)
			);
			wp_redirect( wc_get_checkout_url() );
			exit;
		}

		// Mark order as paid on successful transaction.
		if ( $body['statusCode'] == 200 && $body['txnStatus'] === 'SUCCESS' ) {
			$order->payment_complete();
			$order->add_order_note( __( 'Payment successful via UPITranzact', 'upitranzact-payment-gateway' ) );
			wc_add_notice( __( 'Payment successful!', 'upitranzact-payment-gateway' ), 'success' );
			wp_redirect( $order->get_checkout_order_received_url() );
			exit;
		}

		// Handle gateway-declared failure.
		$order->update_status(
			'failed',
			sprintf(
				__( 'Payment failed: %s', 'upitranzact-payment-gateway' ),
				$body['msg'] ?? 'Unknown error'
			)
		);
		wc_add_notice( __( 'Payment failed. Please try again.', 'upitranzact-payment-gateway' ), 'error' );
	} else {
		$order->update_status(
			'failed',
			__( 'Payment verification failed: Invalid response', 'upitranzact-payment-gateway' )
		);
		wc_add_notice(
			__( 'Payment verification failed. Please contact support.', 'upitranzact-payment-gateway' ),
			'error'
		);
	}

	wp_redirect( wc_get_checkout_url() );
	exit;
}