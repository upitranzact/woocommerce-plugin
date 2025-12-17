(function() {
    if (!window.wc || !window.wc.wcBlocksRegistry) {
        return;
    }

    const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
    const { createElement: h } = window.wp.element;

    const Content = ({ eventRegistration, emitResponse }) => {
        return h('div', { className: 'upitranzact-payment-box' },
            h('div', { className: 'upitranzact-content' },
                h('p', { className: 'upitranzact-text' }, window.upitranzact_data.description),
                h('div', { className: 'upitranzact-apps-row' },
                    h('div', { className: 'upitranzact-app-item' },
                        h('img', { src: window.upitranzact_data.icons.phonepe, alt: 'PhonePe', className: 'upitranzact-app-logo' })
                    ),
                    h('div', { className: 'upitranzact-app-item' },
                        h('img', { src: window.upitranzact_data.icons.gpay, alt: 'Google Pay', className: 'upitranzact-app-logo' })
                    ),
                    h('div', { className: 'upitranzact-app-item' },
                        h('img', { src: window.upitranzact_data.icons.paytm, alt: 'Paytm', className: 'upitranzact-app-logo' })
                    ),
                    h('div', { className: 'upitranzact-app-item' },
                        h('img', { src: window.upitranzact_data.icons.bhim, alt: 'BHIM', className: 'upitranzact-app-logo' })
                    )
                )
            )
        );
    };

    const Label = () => {
        return h('span', { className: 'upitranzact-label' }, window.upitranzact_data.title);
    };

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

    registerPaymentMethod(upitranzactPaymentMethod);
})();