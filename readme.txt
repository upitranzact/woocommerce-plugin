=== UPITranzact for WooCommerce ===
Contributors: upitranzact
Donate link: https://www.upitranzact.com
Tags: woocommerce, upitranzact, upi, payment, qr, india, intent
Requires at least: 4.7
Tested up to: 6.9
Stable tag: 1.1.0
Requires PHP: 7.2
Requires Plugins: woocommerce
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

UPITranzact WooCommerce Plugin enables WooCommerce stores to accept UPI payments using QR codes and UPI intent flows across all UPI-enabled apps in India.

== Description ==

UPITranzact WooCommerce Plugin is a payment gateway plugin designed for Indian WooCommerce merchants to accept fast and secure UPI payments. The plugin supports both **UPI QR code** and **UPI intent-based payments**, allowing customers to pay using any UPI-enabled app such as Google Pay, PhonePe, Paytm, BHIM, or other supported apps.

UPITranzact integrates seamlessly with WooCommerce checkout and automatically updates order status after payment verification. It offers flexible display modes and reliable payment validation using both webhook and API-based confirmation.

### Features:
- **UPI QR + Intent Payments**: Accept payments via QR code and UPI intent.
- **Supports All UPI Apps**: Works with Google Pay, PhonePe, Paytm, BHIM, and more.
- **WooCommerce Compatible**: Seamless integration with WooCommerce checkout.
- **Multiple QR Display Modes**:
  - Inline QR
  - Popup / Wizard QR
- **Auto QR Refresh**: Automatically refreshes QR codes to avoid expiry.
- **Manual Confirmation Option**: Supports manual payment confirmation where required.
- **Reliable Order Validation**: Uses webhook and payment status APIs to confirm transactions.
- **Secure API Communication**: Authenticated requests for every transaction.

UPITranzact is intended for use by Indian merchants who are authorized to accept UPI payments in accordance with applicable RBI and NPCI guidelines.

== Installation ==

1. Upload the plugin to your WordPress site (`Plugins > Add New > Upload Plugin`).
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Navigate to **WooCommerce > Settings > Payments**.
4. Enable **UPITranzact** and configure the settings.
5. Enter your **UPITranzact Merchant credentials** to start accepting payments.

== Requirements ==

- **SSL Certificate Required**  
  Your website must use HTTPS to securely process payments.
  - Check for the padlock icon in the browser address bar.
  - Obtain an SSL certificate from your hosting provider if not already enabled.

== Frequently Asked Questions ==

= What is UPITranzact? =
UPITranzact is a UPI payment gateway that enables WooCommerce merchants to accept payments via UPI QR codes and UPI intent flows using any UPI-enabled mobile application.

= How do customers pay using UPITranzact? =
Customers can either:
- Scan a UPI QR code using their preferred UPI app, or
- Use the UPI intent option to complete payment directly within their app.

= Which UPI apps are supported? =
All UPI-enabled apps are supported, including Google Pay, PhonePe, Paytm, BHIM, and other UPI-compatible applications.

= Does the plugin update WooCommerce order status automatically? =
Yes. Orders are updated automatically after successful payment validation using webhooks and payment status verification APIs.

= Is SSL required to use this plugin? =
Yes. SSL (HTTPS) is mandatory to ensure secure payment processing and to comply with WooCommerce and payment security best practices.

= Does UPITranzact support refunds? =
Refunds are handled according to UPITranzact’s service policies. Please refer to the UPITranzact dashboard or contact support for refund-related queries.

= Are recurring payments supported? =
No. Currently, UPITranzact supports only one-time UPI payments. Recurring payments are not supported.

= Is the plugin compatible with WooCommerce themes? =
Yes. The plugin is compatible with all WooCommerce-compliant themes. We recommend testing on your site before going live.

== External Services ==

This plugin connects to **UPITranzact**, a third-party UPI payment service, to process payments.

### 🔧 API Endpoints Used

- **Production Base URL**:  
  `https://api.upitranzact.com/`

- **Create Order**:  
  `/v1/payments/createOrderRequest`

- **Check Payment Status**:  
  `/v1/payments/checkPaymentStatus`

- **Webhook**:  
  Used to receive asynchronous payment confirmation events from UPITranzact.

---

### 📤 Data Shared with UPITranzact

The plugin sends the following data during payment processing:
- WooCommerce Order ID
- Merchant ID
- Transaction amount
- Customer name
- Customer email
- Customer phone number
- Callback / return URL

---

### 🔄 When Data Is Sent

1. When an order is created and the customer selects **UPITranzact** as the payment method.
2. During payment verification to confirm transaction status.

---

### 🛡️ Data Sharing Consent

No data is transmitted to UPITranzact unless the customer explicitly selects the UPITranzact payment method and places an order.

---

### 🧭 Service Provider Details

**Service Provider:** UPITranzact  
**Website:** https://www.upitranzact.com  
**Terms of Service:** https://upitranzact.com/terms-and-conditions  
**Privacy Policy:** https://upitranzact.com/privacy-policy  

== Changelog ==

= 1.1.0 =
* Initial release of UPITranzact WooCommerce Plugin.
* Added support for UPI QR and UPI intent payments.
* Integrated order creation and payment status validation APIs.
* Webhook-based payment confirmation support.

== Upgrade Notice ==

= 1.1.0 =
Initial release of the UPITranzact WooCommerce Plugin.