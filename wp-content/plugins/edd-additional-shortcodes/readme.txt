=== Easy Digital Downloads - Additional Shortcodes ===
Contributors: easydigitaldownloads, cklosows
Tags: Easy Digital Downloads, Shortcodes
Requires at least: 3.0
Tested up to: 4.7
Stable tag: 1.4
Donate link: https://easydigitaldownloads.com/donate/
License: GPLv2 or later

Add additional shortcodes for Easy Digital Downloads

== Description ==

As simple as it sounds. Adds some additional shortcodes to the Easy Digital Downloads plugin. The shortcodes included all need opening and closing tags:

Show only if the cart has items in it
[edd_cart_has_contents]Content Here[/edd_cart_has_contents]

Show only if the cart is empty
[edd_cart_is_empty]Content Here[/edd_cart_is_empty]

Show only if the cart contains any/all of the specified items
[edd_items_in_cart ids="20"]Content Here[/edd_items_in_cart]
[edd_items_in_cart ids="20,34,25:1"]Content Here[/edd_items_in_cart]
[edd_items_in_cart ids="20,34,25:1" match="all"]Content Here[/edd_items_in_cart]
[edd_items_in_cart ids="20,34,25:1" match="any"]Content Here[/edd_items_in_cart]

Show if the user has made previous purchases (will always be hidden if logged out)
[edd_user_has_purchases]Content Here[/edd_user_has_purchases]

Show only if the user has no purchases. Includes the 'loggedout' parameter to specify if logged out users
should be included in seeing the content. (Default true)
[edd_user_has_no_purchases loggedout=true]Content Here[/edd_user_has_no_purchases]

Show content only if a user is logged in
[edd_is_user_logged_in]Content Here[/edd_is_user_logged_in]

Show content only if a user is logged out
[edd_is_user_logged_out]Content Here[/edd_is_user_logged_out]

Show content only if a user has purchased any of the specified download ids.
Supports multiple IDs. If a download has variable pricing, you can pass just the ID for all options, or <download id>:<price id> for a specific variable pricing option.
[edd_user_has_purchased ids="20,34,25:1"]Content Here[/edd_user_has_purchased]

Software Licensing Support:
Show content only if a user has active licenses
[edd_has_active_licenses]Content Here[/edd_has_active_licenses]

Show content only if user has expired licenses
[edd_has_expired_licenses]Content Here[/edd_has_expired_licenses]

== Changelog ==
= 1.4 - February 24, 2017 =
* NEW: Add support for Software Licensing.
* NEW: Add support for specific items in the cart.
* TWEAK: Remove references to extract.

= 1.3 =
* NEW: edd_is_user_logged_out shortcode

= 1.2 =
* NEW: edd_user_has_purchased shortcode

= 1.1 =
* NEW: Added edd_is_user_logged_in shortcode

= 1.0 =
* NEW: Initial Release
