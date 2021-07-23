=== EDD Download Images ===
Contributors: sumobi
Tags: easy digital downloads, digital downloads, e-downloads, edd, images, additional images, download images, sumobi
Requires at least: 3.3
Tested up to: 4.4.2
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add extra download images and display them.

== Description ==

This plugin requires [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/ "Easy Digital Downloads") v2.5 or higher. This plugin allows you to add extra images to your downloads. Use the included shortcode or template tag to display the images on your website. Developers can also use this plugin to get an array of the images and display the images any way they wish (eg slideshow).

= Shortcode Usage =

    [edd_download_images]

= Template Tag Usage =

    if( function_exists( 'edd_di_display_images') ) {
        edd_di_display_images();
    }

= Filtering the HTML =

To alter the HTML, the following filter is provided (example shows an extra `<div>` being added around image). Paste this into your functions.php and modify $html to your liking:

    function themename_edd_di_display_images( $html, $download_image ) {
        // here a div tag is wrapped around each image
        $html = '<div><img class="edd-di-image" src="' . $download_image['image'] . '" /></div>';
        return $html;
    }
    add_filter( 'edd_di_display_images', 'themename_edd_di_display_images', 10, 2 );

= Developers =

To get the array of images from the Database you can use the following. This will be useful if you'd like to build a slideshow to show all the download's images:

    $images = ( function_exists('edd_di_get_images') ) ? edd_di_get_images() : '';

    // see the images in the array
    var_dump( $images );

**Plugins for Easy Digital Downloads**

[https://easydigitaldownloads.com/extensions/](https://easydigitaldownloads.com/extensions/ "Plugins for Easy Digital Downloads")

== Installation ==

1. Upload entire `edd-download-images` to the `/wp-content/plugins/` directory, or just upload the ZIP package via 'Plugins > Add New > Upload' in your WP Admin
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add additional images to each download

**Extensions for Easy Digital Downloads**

[https://easydigitaldownloads.com/extensions/](https://easydigitaldownloads.com/extensions/ "Extensions for Easy Digital Downloads")

**Tips for Easy Digital Downloads**

[http://sumobi.com/blog](http://sumobi.com/blog "Tips for Easy Digital Downloads")

**Follow me on Twitter**
[http://twitter.com/sumobi_](http://twitter.com/sumobi_ "Twitter")

== Screenshots ==

1. The new repeatable image upload fields integrated seamlessly with Easy Digital Downloads

== Upgrade Notice ==

= 1.2 =
* Fix: Images sometimes were not being saved correctly on first save
* New: Images can now be reordered via drag and drop. Requires EDD 2.5+

== Changelog ==

= 1.2 =
* Fix: Images sometimes were not being saved correctly on first save
* New: Images can now be reordered via drag and drop. Requires EDD 2.5+

= 1.1.3 =
* Fix: If there were more than 3 images, they weren't being saved properly due to recent changes in EDD

= 1.1.2 =
* Fix: template tag not working from previous update

= 1.1.1 =
* Fix: [edd_download_images] not showing images in correct location

= 1.1 =
* Fix: Now working for EDD v1.9

= 1.0 =
* Initial release
