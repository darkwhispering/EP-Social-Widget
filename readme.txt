=== EP Social Widget ===
Contributors: darkwhispering
Donate link: http://darkwhispering.com/buy-me-a-beer
Tags: social, widget, plugin, facebook, twitter, flickr, rss, social share, google, google plus, youtube, linkedin, soundcloud, pinterest, vimeo, instagram, github
Requires at least: 3.0.0
Tested up to: 4.6.0
Stable tag: 1.5.1

Very small and easy to use widget and shortcode to display social icons on your site. Facebook, Twitter, Flickr, Google Plus, Youtube and more.

== Description ==

This plugin add a new widget and shortcode that you can use to add social icons on your site by using the default networks or add your own via the option page.

Available default networks are

* Twitter
* Facebook
* Flickr
* Google Plus
* Youtube
* LinkedIn
* Soundcloud
* Pinterest
* Vimeo
* Tumblr
* Google
* Instagram
* Github

You can also choose to display a icon for your RSS 2.0 feed. Icons are in black and white colors only.

**Shortcode - How to**
Your are able to show the same icons as in the widget in your posts or pages with the shortcode.

`[ep-social-widget facebook="https://facebook.com" gplus="https://plus.google.com" rss="1"]`

Available networks are

* facebook
* gplus
* twitter
* flickr
* youtube
* rss
* linkedin
* soundcloud
* pinterest
* vimeo
* tumblr
* google
* instagram
* github

Just use the one you want to display in your post/page and give it a link. The RSS option only need a 1 as value if you want that displayed. Remove any network completely to remove it from the post/page.

To display a network you have added yourself, just use the network name displayed in the list at the option page.

Plugin also available on [Github](https://github.com/darkwhispering/EP-Social-Widget).

== Installation ==

1. Upload the zipped file to yoursite/wp-content/plugins
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Frontend screenshot
2. Backend screenshot
3. Option page

== Changelog ==

= 1.5.1 =
* Fixed deprecated warning for old method `WP_Widget()`, now using correct `__construct()`.
* Minor HTML code fix.
* Tested on Wordpress 4.6.0.

= 1.5.0 =
* Replaced old GIF icons with new SVG icons to get consistent quality between platforms and devices.
* Added more spacing between icons.
* Added slight fade out effect on hover.
* Added Github.
* Added Instagram.
* Added Google. *As general for anything Google, Gplus icon still exists separately.*
* Removed Bandcamp.
* Removed Deviantart.
* Removed Meetup.
* Removed MySpace.
* Source code now follows the Wordpress coding standards more closely.
* Tested on Wordpress 4.4

= 1.4.1 =
* Added title attribute to the link tags for better SEO.
* Added alt attribute to the icon images for better SEO.
* Tested on WordPress 4.1

= 1.4.0 =
* Updated settings page layout and design to match the new admin design in WP 3.8
* Tested on WP 3.8

= 1.3.0 =
* Added Tumblr as default network
* Tested on Wordpress 3.6

= 1.2.0 =
* Added Pinterest as default network.
* Added Vimeo as default network.

= 1.1.5 =
* Fixed issue with toggling input fields in widget settings directly after adding the widget to a widget area
* Update the twitter image to the new twitter icon

= 1.1.4 =
* Now able to remove links from the widget again.
* Tested on WP multi/network site.

= 1.1.3 =
* Icons with broken image path will now be removed and not displayed in the widget.

= 1.1.2 =
* Fixed error in update function

= 1.1.1 =
* Found error in the shortcode short after releasing version 1.1.0, fixed in this update.

= 1.1.0 =
* Now allowing gif, png and jpg images to be uploaded and used as icons.
* Automatic update scripts added to prevent the widget to break when updating from a version lower then 1.1.0.

= 1.0.2 =
* Removed the error displaying in the widget if the epsocial_icon folder did not exist in the uploads folder.

= 1.0.1 =
* Removed the error displaying if the epsocial_icon folder did not exist in the uploads folder.

= 1.0.0 =
* Added an option page to give you the ability to add any network of your choosing
* Moved all default icons into a folder

= 0.6.0 =
* Added soundcloud and bandcamp after request

= 0.5.3 =
* Fixed bug in shortcode with rss icon

= 0.5.2 =
* Fixed bug where rss icon where showing even if no where selected in widget settings

= 0.5.1 =
* Fixed bug for widget empty after 0.5.0 version update
* Fixed bug with all icons showing after 0.5.0 version update

= 0.5.0 =
* Added new social networks, LinkedIn, DeviantArt, Meetup and MySpace
* Added some bottom padding to the icons for good 2 row layout
* Made the code a lot smaller, cleaner and faster

= 0.4.0 =
* Added support for shortcode in posts and pages
* Tested with wordpress 3.3.2

= 0.3.0 =
* Added youtube
* Rewritten the CSS. Now targeting the a link instead of the img tag
* Added float clear CSS
* Updated screenshots & plugin description

= 0.2.0 =
* Added Google Plus
* Changed description text for the input fields in widgets settings
* Updated screenshots & description about the plugin

= 0.1.1 =
* Fixed correct file path for images and css file
* New plugins url

= 0.1.0 =
* Initial release
