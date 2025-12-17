<?php
namespace Upitranzact\Blocks;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

class WC_Upitranzact_Blocks extends AbstractPaymentMethodType {
    protected $name = 'upitranzact';

    public function initialize() {
        $this->settings = get_option('woocommerce_upitranzact_settings', []);
    }

    public function is_active() {
        $gateway = new \WC_Gateway_UpiTranZact();
        return $gateway->is_available();
    }

    public function get_payment_method_script_handles() {
        $script_path = UPITRANZACT_PLUGIN_URL . 'assets/js/blocks-integration.js';
        
        $dependencies = array(
            'wp-element',
            'wc-blocks-registry'
        );
        
        wp_register_script(
            'upitranzact-payment-blocks',
            $script_path,
            $dependencies,
            '1.0.3',
            true
        );
        
        $this->register_payment_method_data();
        
        return array('upitranzact-payment-blocks');
    }

    protected function register_payment_method_data() {
        $gateway = new \WC_Gateway_UpiTranZact();
        
        wp_localize_script(
            'upitranzact-payment-blocks',
            'upitranzact_data', 
            [
                'title' => !empty($this->settings['title']) ? $this->settings['title'] : __('UPITranzact', 'upitranzact-payment-gateway'),
                'description' => !empty($this->settings['description']) ? $this->settings['description'] : __('Pay with any UPI App', 'upitranzact-payment-gateway'),
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

    public function get_payment_method_data() {
        return [
            'title' => !empty($this->settings['title']) ? $this->settings['title'] : __('UPITranzact', 'upitranzact-payment-gateway'),
            'description' => !empty($this->settings['description']) ? $this->settings['description'] : __('Pay with any UPI App', 'upitranzact-payment-gateway'),
            'supports' => ['products'],
        ];
    }
}