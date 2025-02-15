<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Gateway_UpiTranZact extends WC_Payment_Gateway {
    public function __construct() {
        $this->id = 'upitranzact';
        $this->method_title = __('UPITranzact', 'woocommerce');
        $this->method_description = __('Accept payments via UPITranzact Payment Gateway.', 'woocommerce');
        $this->supports = array('products');

        $this->init_form_fields();
        $this->init_settings();

        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->mid = $this->get_option('mid');
        $this->public_key = $this->get_option('public_key');
        $this->secret_key = $this->get_option('secret_key');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_api_' . strtolower(get_class($this)), array($this, 'handle_response'));
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable UPITranzact Payment', 'woocommerce'),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __('Title', 'woocommerce'),
                'type' => 'text',
                'description' => __('Title customers see at checkout.', 'woocommerce'),
                'default' => __('UPITranzact', 'woocommerce'),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'Description',
                'type'        => 'textarea',
                'description' => 'This controls the description which the user sees during checkout.',
                'default'     => 'Pay with any UPI App',
            ),
            'mid' => array(
                'title' => __('Merchant ID', 'woocommerce'),
                'type' => 'text',
            ),
            'public_key' => array(
                'title' => __('Public Key', 'woocommerce'),
                'type' => 'text',
            ),
            'secret_key' => array(
                'title' => __('Secret Key', 'woocommerce'),
                'type' => 'text',
            ),
        );
    }

    public function is_available() {
        return $this->enabled === 'yes' && !empty($this->mid) && !empty($this->public_key) && !empty($this->secret_key);
    }

    public function process_payment($order_id) {
        require_once UPITRANZACT_PLUGIN_DIR . 'includes/helpers.php';
        return process_upitranzact_payment($order_id, $this);
    }

    public function handle_response() {
        require_once UPITRANZACT_PLUGIN_DIR . 'includes/helpers.php';
        handle_upitranzact_response($this);
    }
}
