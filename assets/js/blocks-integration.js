/**
 * UPITranzact Payment Gateway Blocks Integration
 */
(function() {
  // Make sure WC Blocks is available
  if (!window.wc || !window.wc.wcBlocksRegistry || !window.wc.wcBlocksRegistry.registerPaymentMethod) {
    return;
  }

  // Import WC Blocks registry
  const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
  const { createElement } = window.wp.element;

  /**
   * Create UPITranzact payment component
   */
  const Content = () => {
    return createElement('div', { className: 'upitranzact-description' },
      createElement('div', { className: 'upitranzact-payment-description' },
        createElement('h4', {}, 'Pay with any UPI App'),
        createElement('div', { className: 'upitranzact-payment-methods' },
          createElement('span', { className: 'upitranzact-payment-method-info' }, 'Pay with:'),
          createElement('div', { className: 'upitranzact-upi-apps' },
            // PhonePe
            createElement('div', { className: 'upitranzact-upi-app phonepe' },
              createElement('img', { src: window.upitranzact_data.icons.phonepe, alt: 'PhonePe' }),
              createElement('span', {}, 'PhonePe')
            ),
            // Google Pay
            createElement('div', { className: 'upitranzact-upi-app gpay' },
              createElement('img', { src: window.upitranzact_data.icons.gpay, alt: 'Google Pay' }),
              createElement('span', {}, 'GPay')
            ),
            // Paytm
            createElement('div', { className: 'upitranzact-upi-app paytm' },
              createElement('img', { src: window.upitranzact_data.icons.paytm, alt: 'Paytm' }),
              createElement('span', {}, 'Paytm')
            ),
            // BHIM
            createElement('div', { className: 'upitranzact-upi-app bhim' },
              createElement('img', { src: window.upitranzact_data.icons.bhim, alt: 'BHIM UPI' }),
              createElement('span', {}, 'BHIM')
            )
          )
        )
      )
    );
  };

  /**
   * Create label component
   */
  const Label = (props) => {
    const { PaymentMethodLabel } = props.components;
    return createElement(PaymentMethodLabel, { text: window.upitranzact_data.title });
  };

  /**
   * Register payment method
   */
  const upitranzactPaymentMethod = {
    name: 'upitranzact-payment-gateway',
    label: createElement(Label),
    content: createElement(Content),
    edit: createElement(Content),
    canMakePayment: () => true,
    ariaLabel: window.upitranzact_data.title,
    supports: {
      features: window.upitranzact_data.supports || ['products'],
    },
  };

  // Register payment method
  registerPaymentMethod(upitranzactPaymentMethod);
})(); 