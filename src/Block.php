<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

class WC_UPI_Tranzact_Block extends AbstractPaymentMethodType {
    protected $name = 'upitranzact';

    public function initialize() {
        $this->settings = get_option('woocommerce_upitranzact_settings', []);
    }

    public function is_active() {
        return !empty($this->settings['enabled']) && $this->settings['enabled'] === 'yes';
    }
}
