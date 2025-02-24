=== UPITranzact Payment Gateway ===
Contributors: upitranzact  
Tags: woocommerce, payment, upitranzact, upigateway, ecommerce  
Requires at least: 5.0  
Tested up to: 6.7  
Requires PHP: 7.4  
Stable tag: 1.0.2  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Fast, secure, and reliable payment gateway designed for seamless UPI transactions.

== Description ==  
Fast, secure, and reliable payment gateway designed for seamless UPI transactions. Accept payments instantly, manage funds effortlessly, and provide a smooth checkout experience for your customers. Whether you're a business or an individual.  

== External Services ==  
This plugin connects to the UPITranzact API (`api.upitranzact.com`) to process payments, ensuring secure and real-time UPI transactions. It also communicates with `upitranzact.com` for merchant verification and service-related functionalities.  

**External Services Used:**  
1. **UPITranzact API (`api.upitranzact.com`)**  
   - **What it is used for:** Payment processing, checking payment status, and order validation.  
   - **Data sent:** Merchant ID, order ID, public and secret keys, and transaction details.  
   - **When data is sent:** When creating a payment request or checking payment status.  

2. **UPITranzact Website (`business.upitranzact.com`)**  
   - **What it is used for:** Merchant authentication, dashboard services, and API documentation access.  
   - **Data sent:** Basic merchant details for authentication and API key verification.  
   - **When data is sent:** When accessing merchant dashboard or verifying credentials.  

**Service Provider:** UPITranzact  
- **Terms of Service:** https://business.upitranzact.com/terms
- **Privacy Policy:** https://business.upitranzact.com/privacy 

== Installation ==  
1. Upload `upitranzact-payment-gateway` to `/wp-content/plugins/`.  
2. Activate it from the WordPress admin panel.  
3. Configure your API keys in WooCommerce settings.  

== Security ==  
All output is properly escaped for security.  

== Changelog ==  
= 1.0.2 =  
* Initial release.  
