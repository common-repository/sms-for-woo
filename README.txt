=== SMS For Woo ===
Contributors:
Donate link:
Tags: sms, woocommerce sms, woocommerce notification, global voice, bulksms, bulk sms, woo commerce, mass sms
Requires at least: 5.2
Tested up to: 6.4.2
Stable tag: 1.1.2
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A free SMS notifications plugin for Woocommerce shops, that uses global-voice.com API.

== Description ==

SMS Plugin for Woocommerce. Add SMS notifications to your WooCommerce shop.
Design, send and track automatic personalized SMS campaigns. Notify your customers about order status.

The SMS For Woo plugin is very useful when you want to get notified via SMS after placing an order.
Buyer can get SMS notification after an order is placed. SMS notification options can be customized very easily.

Custom defined messages for every woocommerce order status.
Custom in-message tags ([SELLER_PAGE],[BILLING_FIRST],[BILLING_LAST],[SHIPPING_FIRST],[SHIPPING_LAST],[ORDER_NUMBER],[ORDER_DATE],[ORDER_TOTAL],[ORDER_CURIER],[CURIER_CODE])
Testing section for sms testing and account information.


== Connection Guide ==

1. Go to [Global-voice](https://www.global-voice.net/)
1. Register an account
1. Then go to [sign-in page](https://retail.global-voice.net/sign-in/) and sign-in.
1. Navigate to "API connections" .
1. Click "Add connection".(note - wildcard ip can be used ex. *.*.*.*)
1. Copy user name and password.
1. Paste them into SMS for Woo "Account" section.
1. Navigate to "Tokens" on the global-voice.net "API connetions" area.
1. Click "Get token".
1. Copy the token.
1. Paste it into SMS for Woo "Account" section.
1. Click "Save changes" and you are done.

== Frequently Asked Questions ==

=How to use?=

Check the connection guide.

== Upgrade Notice ==

There have been some changes to this plugin that require an upgrade.

== Screenshots ==

1. Main page with the connection guide.
2. Account data you need to fill in.
3. Settings page where you activate and define your custom messages for every order status.
4. Bulk sms page where you send campaigns to the people that opted in.

== Changelog ==

= 1.1.2 =
* Tested with latest WordPress version

= 1.1.1 =
*Changed labels for clarification.

= 1.1.0 =
* Added 'SMS From' field that represents the Sender ID for the messages. This field has a maximum character length of 11.
* The default value is set to the first 11 characters of your site name.
* Added youtube clip to the connection guide.
* Fixed url encoding allowing for the use of special character in text messages.

= 1.0.1 =

* First release.

== Features ==

Feature list:

* Notify customer via sms on checkout.
* Notify customer via sms on woocommerce order change(if template is active and defined).
* Bulk sms (campaingn) to users that opted in to receive offers.
* Custom defined messages for every woocommerce order status.
* Custom in-message tags ([SELLER_PAGE],[BILLING_FIRST],[BILLING_LAST],[SHIPPING_FIRST],[SHIPPING_LAST],[ORDER_NUMBER],[ORDER_DATE],[ORDER_TOTAL],[ORDER_CURIER],[CURIER_CODE])
* Testing section for sms testing and account information.



