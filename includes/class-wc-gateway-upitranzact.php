<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Gateway_UpiTranZact extends WC_Payment_Gateway {
    public function __construct() {
        $this->id = 'upitranzact';
        $this->method_title = __('UPITranzact', 'upitranzact-payment-gateway');
        $this->method_description = __('Accept payments via UPITranzact Payment Gateway.', 'upitranzact-payment-gateway');
        $this->supports = array('products');

        // Set better default icon
        $icon_url = UPITRANZACT_PLUGIN_URL . 'assets/upitranzact-logo.png';
        // Check if logo file exists, if not use the URL
        if (!file_exists(UPITRANZACT_PLUGIN_DIR . 'assets/upitranzact-logo.png')) {
            $icon_url = 'https://www.upitranzact.com/public/asset/images/logo.svg';
        }
        $this->icon = apply_filters('woocommerce_upitranzact_icon', $icon_url);

        $this->init_form_fields();
        $this->init_settings();

        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->mid = $this->get_option('mid');
        $this->public_key = $this->get_option('public_key');
        $this->secret_key = $this->get_option('secret_key');
		
		$this->description = $this->get_option('description', __('Pay securely with UPITranzact - UPI', 'upitranzact-payment-gateway'));

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_api_' . strtolower(get_class($this)), array($this, 'handle_response'));
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'upitranzact-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable UPITranzact Payment', 'upitranzact-payment-gateway'),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __('Title', 'upitranzact-payment-gateway'),
                'type' => 'text',
                'description' => __('Title customers see at checkout.', 'upitranzact-payment-gateway'),
                'default' => __('UPITranzact', 'upitranzact-payment-gateway'),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'Description',
                'type'        => 'textarea',
                'description' => 'This controls the description which the user sees during checkout.',
                'default'     => 'Pay with any UPI App',
            ),
            'mid' => array(
                'title' => __('Merchant ID', 'upitranzact-payment-gateway'),
                'type' => 'text',
            ),
            'public_key' => array(
                'title' => __('Public Key', 'upitranzact-payment-gateway'),
                'type' => 'text',
            ),
            'secret_key' => array(
                'title' => __('Secret Key', 'upitranzact-payment-gateway'),
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

        /**
     * Get URL for specific UPI app icon
     */
    public function get_icon_url($app_name) {
        // Check for SVG icons first in the icons directory
        if ($app_name === 'phonepe') {
            $icon_path = 'assets/icons/icons8-phone-pe.svg';
        } elseif ($app_name === 'gpay') {
            $icon_path = 'assets/icons/icons8-google-pay.svg';
        } elseif ($app_name === 'paytm') {
            $icon_path = 'assets/icons/icons8-paytm.svg';
        } elseif ($app_name === 'bhim') {
            $icon_path = 'assets/icons/icons8-bhim.svg';
        } else {
            // Default icon as fallback
            $icon_path = 'assets/upitranzact-logo.png';
        }
        
        // Return complete URL
        return UPITRANZACT_PLUGIN_URL . $icon_path;
    }
    
    /**
     * Output payment fields (if any)
     */
    public function payment_fields() {
        echo '<div class="upitranzact-payment-description">';
        
        // More compact heading
        echo '<h4>Pay with any UPI App</h4>';
        
        // Add payment methods icons
        echo '<div class="upitranzact-payment-methods">';
        echo '<span class="upitranzact-payment-method-info">Pay with:</span>';
        
        // UPI app icons container - now in a single row
        echo '<div class="upitranzact-upi-apps">';
        
        // PhonePe icon
        echo '<div class="upitranzact-upi-app phonepe">';
        echo '<img src="' . $this->get_icon_url('phonepe') . '" alt="PhonePe" />';
        echo '<span>PhonePe</span>';
        echo '</div>';
        
        // Google Pay icon
        echo '<div class="upitranzact-upi-app gpay">';
        echo '<img src="' . $this->get_icon_url('gpay') . '" alt="Google Pay" />';
        echo '<span>GPay</span>';
        echo '</div>';
        
        // Paytm icon
        echo '<div class="upitranzact-upi-app paytm">';
        echo '<img src="' . $this->get_icon_url('paytm') . '" alt="Paytm" />';
        echo '<span>Paytm</span>';
        echo '</div>';
        
        // BHIM icon
        echo '<div class="upitranzact-upi-app bhim">';
        echo '<img src="' . $this->get_icon_url('bhim') . '" alt="BHIM UPI" />';
        echo '<span>BHIM</span>';
        echo '</div>';
        
        echo '</div>'; // End .upitranzact-upi-apps
        
        echo '</div>'; // End .upitranzact-payment-methods
        
        echo '</div>'; // End .upitranzact-payment-description
    }
}
