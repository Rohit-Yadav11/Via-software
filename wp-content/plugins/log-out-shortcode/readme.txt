=== Log Out Shortcode ===
Tags: logout, shortcode, log-out, log out, link
Contributors: jamescollins
Requires at least: 3.9
Tested up to: 5.5
Stable tag: 1.0.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add a log out link or button to a post or page using a simple shortcode.

== Description ==

There are 3 shortcodes that can be used:

`[logout]` - A standard Log Out link, where the user stays on the login screen after being logged out.
`[logout_to_home]` - A link that logs the user out, and redirects them to your home page.
`[logout_to_current]` - A link that logs the user out, and redirects them back to the current page/URL.

**Advanced Usage:**

All 3 shortcodes all support 3 attributes/parameters:

`[logout text="" class="" redirect=""]`

The 3 supported attributes/parameters are:

`text=""`
- The text to use for the link anchor text.
- Default: `Log Out`
`class=""`
- The HTML class(es) to use for the link. Multiple classes should be separated by a space.
- Default: `logout`
`redirect=""`
- The path/URL to redirect the user to after they are logged out.
- Default: the WordPress login screen.
- Note: must be a URL or path on the same domain name as the WordPress website (cannot be an external domain/URL)

**Some Examples:**
`[logout text="Log Off"]` - A `Log Off` link that logs the user out.
`[logout redirect="/members/"]` - Log the user out, and redirect them to `/members/`.
`[logout_to_home text="Log Off"]` - A `Log Off` link that logs the user out and redirects them to your home page.
`[logout class="log-out-link"]` - Use a custom CSS/HTML class name for the log out link.


Note: the shortcodes will return nothing/empty if the user isn't logged in.

This plugin is inspired by the [Logout Button plugin by Ben Poland](https://wordpress.org/plugins/logout-button/).

If you like, you can [contribute to this plugin on GitHub](https://github.com/OM4/log-out-shortcode).

== Installation ==

1. Activate the plugin.
1. Add one of the shortcodes to a post/page.

== Frequently Asked Questions ==

= What shortcodes can be used? =

Please see the [plugin description](https://wordpress.org/plugins/log-out-shortcode/) for a list of shortcodes and supported parameters.

== Changelog ==

= 1.0.8 =
* Fix Typo

= 1.0.7 =
* Mark WP 5.5 compatible

= 1.0.6 =
* Mark WP 5.2 compatible

= 1.0.5 =
* Fix a bug that meant it was not possible to use specify multiple classes in the class="" attribute. Thanks to @hughc for the bug report.

= 1.0.4 =
* WordPress 4.4 compatibility.
* Documentation updates.

= 1.0.3 =
* Clarify usage of the redirect="" parameter.
* WordPress 4.1 compatibility.

= 1.0.2 =
* WordPress 4.0 compatibility.

= 1.0.1 =
* WordPress 3.9.2 compatibility.
* Add [logout] shortcode to plugin description.

= 1.0 =
* Initial release.
