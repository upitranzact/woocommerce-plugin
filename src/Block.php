<?php
namespace Upitranzact\PaymentServices;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * WC_UPITranzact_Payment_Services_Block class to integrate with WooCommerce Blocks.
 */
class WC_Upitranzact_Payment_Services_Block extends AbstractPaymentMethodType {
    /**
     * Payment method name/id/slug.
     *
     * @var string
     */
    protected $name = 'upitranzact-payment-gateway';

    /**
     * Initializes the payment method type.
     */
    public function initialize() {
        $this->settings = get_option('woocommerce_upitranzact_settings', []);
    }

    /**
     * Returns if this payment method should be active. If false, the scripts will not be enqueued.
     *
     * @return boolean
     */
    public function is_active() {
        return !empty($this->settings['enabled']) && $this->settings['enabled'] === 'yes';
    }
    
    /**
     * Returns an array of scripts/handles to be registered for this payment method.
     *
     * @return array
     */
    public function get_payment_method_script_handles() {
        // Script path for blocks integration
        $script_path = UPITRANZACT_PLUGIN_URL . 'assets/js/blocks-integration.js';
        
        // Dependencies for our script
        $dependencies = array(
            'wp-element',
            'jquery',
            'wc-blocks-registry'
        );
        
        // Register our payment block script
        wp_register_script(
            'upitranzact-payment-blocks',
            $script_path,
            $dependencies,
            '1.0.2',
            true
        );
        
        // Register UPI app icons
        $this->register_payment_method_data();
        
        return array('upitranzact-payment-blocks');
    }
    
    /**
     * Register payment method data to be available in JS
     */
    protected function register_payment_method_data() {
        // Get gateway
        $gateway = new \WC_Gateway_UpiTranZact();
        
        // Register icon paths
        wp_localize_script(
            'upitranzact-payment-blocks',
            'upitranzact_data', 
            [
                'title' => !empty($this->settings['title']) ? $this->settings['title'] : __('UPITranzact - UPI Payments', 'upitranzact-payment-gateway'),
                'description' => !empty($this->settings['description']) ? $this->settings['description'] : __('Pay securely with UPITranzact - UPI, Credit/Debit cards and more', 'upitranzact-payment-gateway'),
                'icons' => [
                    'phonepe' => $gateway->get_icon_url('phonepe'),
                    'gpay' => $gateway->get_icon_url('gpay'),
                    'paytm' => $gateway->get_icon_url('paytm'),
                    'bhim' => $gateway->get_icon_url('bhim'),
                ],
                'supports' => ['products'],
            ]
        );
    }

    /**
     * Returns an array of key=>value pairs of data made available to the payment methods script.
     *
     * @return array
     */
    public function get_payment_method_data() {
        return [
            'title' => !empty($this->settings['title']) ? $this->settings['title'] : __('UPITranzact - UPI Payments', 'upitranzact-payment-gateway'),
            'description' => !empty($this->settings['description']) ? $this->settings['description'] : __('Pay securely with UPITranzact - UPI, Credit/Debit cards and more', 'upitranzact-payment-gateway'),
            'supports' => ['products'],
        ];
    }
}
