/**
 * UPITranzact WooCommerce Blocks Payment Method Integration
 *
 * This script registers UPITranzact as a payment method
 * for WooCommerce block-based checkout.
 *
 * It uses data localized from PHP via `wp_localize_script`
 * (window.upitranzact_data) to render payment UI elements.
 */

(function () {

    // Ensure WooCommerce Blocks registry is available before proceeding.
    if (!window.wc || !window.wc.wcBlocksRegistry) {
        return;
    }

    // Extract required helpers from WooCommerce Blocks and WordPress.
    const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
    const { createElement: h } = window.wp.element;

    /**
     * Content component displayed when the payment method is selected.
     *
     * Shows gateway description and supported UPI app icons.
     *
     * @param {Object} props Block props provided by WooCommerce.
     * @return {JSX.Element}
     */
    const Content = ({ eventRegistration, emitResponse }) => {
        return h(
            'div',
            { className: 'upitranzact-payment-box' },
            h(
                'div',
                { className: 'upitranzact-content' },
                h(
                    'p',
                    { className: 'upitranzact-text' },
                    window.upitranzact_data.description
                ),
                h(
                    'div',
                    { className: 'upitranzact-apps-row' },

                    // PhonePe icon
                    h(
                        'div',
                        { className: 'upitranzact-app-item' },
                        h('img', {
                            src: window.upitranzact_data.icons.phonepe,
                            alt: 'PhonePe',
                            className: 'upitranzact-app-logo',
                        })
                    ),

                    // Google Pay icon
                    h(
                        'div',
                        { className: 'upitranzact-app-item' },
                        h('img', {
                            src: window.upitranzact_data.icons.gpay,
                            alt: 'Google Pay',
                            className: 'upitranzact-app-logo',
                        })
                    ),

                    // Paytm icon
                    h(
                        'div',
                        { className: 'upitranzact-app-item' },
                        h('img', {
                            src: window.upitranzact_data.icons.paytm,
                            alt: 'Paytm',
                            className: 'upitranzact-app-logo',
                        })
                    ),

                    // BHIM icon
                    h(
                        'div',
                        { className: 'upitranzact-app-item' },
                        h('img', {
                            src: window.upitranzact_data.icons.bhim,
                            alt: 'BHIM',
                            className: 'upitranzact-app-logo',
                        })
                    )
                )
            )
        );
    };

    /**
     * Label component shown in the list of payment methods.
     *
     * @return {JSX.Element}
     */
    const Label = () => {
        return h(
            'span',
            { className: 'upitranzact-label' },
            window.upitranzact_data.title
        );
    };

    /**
     * UPITranzact payment method registration object.
     *
     * This configuration tells WooCommerce Blocks how
     * to render and handle the payment method.
     */
    const upitranzactPaymentMethod = {
        name: 'upitranzact',
        label: h(Label),
        content: h(Content),
        edit: h(Content),
        canMakePayment: () => true,
        ariaLabel: window.upitranzact_data.title,
        paymentMethodId: 'upitranzact',
        supports: {
            features: window.upitranzact_data.supports || ['products'],
        },
    };

    // Register the payment method with WooCommerce Blocks.
    registerPaymentMethod(upitranzactPaymentMethod);

})();