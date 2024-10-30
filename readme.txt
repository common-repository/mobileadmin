=== iPhone / Mobile Admin ===
Contributors: jaredbangs, dancameron
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=sales%40scatteredmedia%2ecom&item_name=iPhone%20%2f%20Mobile%20Admin%20Donation&buyer_credit_promo_code=&buyer_credit_product_category=&buyer_credit_shipping_method=&buyer_credit_user_address_change=&page_style=PayPal&no_shipping=0&return=http%3a%2f%2fwordpress%2eorg%2fextend%2fplugins%2fmobileadmin%2f&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: admin, mobile, iphone, ipod
Requires at least: 2.2.2
Tested up to: 2.3
Stable tag: 2.0.1

Gives a mobile-friendly admin UI to browsers by user agent, using a plugin architecture. Includes support for iPhone/iPod-Touch.

== Description ==

Mobile Admin adapts the WordPress admin UI to be more friendly to mobile devices, specifically phones.

The iPhone / iPod Touch browser was the first target, but most other mobile browsers are supported at a basic level, and plugins can be used to customize for specific browsers where desired.

A plugin for the iPhone/iPod Touch devices is included, as well as a more basic plugin for Windows Mobile browsers. (Consider the latter a "beta", but it's mostly intended to be another sample for how to write a plugin.)

Most common WordPress admin features are supported:

* Dashboard sections: Incoming Links, Comments, Posts, Blog Stats
* Writing and editing posts (including auto-save)
* Tagging support in 2.3
* Comment Moderation
* Manage Posts page
* Manage Profile page
* Ability to toggle back and forth to the normal admin view.
* Support for plugins that include fields on the post page
* Compatibility with existing library of admin plugins

... with more to come in later revisions.

Patches are welcome and encouraged; if you've got a better looking / better working customization for any particular device, please submit it (via a Trac ticket - see below for link).

= Other resources =

Please submit all usage questions to the [support forums](http://scatter3d.com/forums "Mobile Admin Support")

The [Trac server](http://svn.freepressblog.org/trac/MobileAdmin/ "Mobile Admin Trac Server") can be used for viewing changes and submitting tickets

SPECIAL NOTE - Testing and bug reporting is especially encouraged, as only one of us currently owns an iPhone.

= Props = 
* To [Mark Jaquith](http://txfx.net/) for the inspiration of the Clutter Free plugin
* To [Alex King](http://alexking.org/blog) for the mobile browser check function
* To [Dr Dave](http://unknowngenius.com/blog/) for the inspiration for the plugin architecture

== Installation ==

Short and sweet.

1. Extract the downloaded file and copy the 'mobileadmin' directory under your plugin directory, like so: `/wp-content/plugins/mobileadmin/`. 
2. All files should remain under the 'mobileadmin' directory, ie: `/wp-content/plugins/mobileadmin/MobileAdmin.php`
3. Visit the admin page using a mobile device (iPhone currently). 

That's it.

* You can always switch to the normal admin view using the button at the bottom of the mobile dashboard page.
* Once you've switched back to the normal admin view, a cookie will remember that choice for the remainder of the session
* You can switch back to the mobile view by clicking the button on the Options/Mobile Admin page from the normal view

== Frequently Asked Questions ==

1. How do I disable Mobile Admin from my iPhone / other mobile device?
You can always switch to the normal admin view using the button at the bottom of the mobile dashboard page called "Revert to Normal Admin View"

2. How to I revert back to Mobile Admin?
You can switch back to the mobile view by clicking the button on the Options/Mobile Admin page from the normal view

3. How can I customize the appearance for another device?
Most mobile browsers will get a basic, trimmed down version of the interface. Extra features and styles can be applied by creating plugins.
The iPhone / iPod Touch and Windows Mobile views are included as plugins, and you can use those as guides to build your own. You can find further instructions on building your own plugins at [Extending the WP MobileAdmin Plugin](http://freepressblog.org/extending-the-wp-mobileadmin-plugin/).

PS - if you do choose to extend the MobileAdmin plugin by creating your own plugins (or just tweaking the existing ones), please consider contributing back to the project by sharing your modifications with us. Participation is welcome and encouraged. Drop us a line on the [support forums](http://scatter3d.com/forums "Mobile Admin Support") or in the [Trac system](http://svn.freepressblog.org/trac/MobileAdmin/ "Mobile Admin Trac Server").

== Screenshots ==

1. iPhone Dashboard page - (1 of 2) - Top of page.
2. iPhone Dashboard page - (2 of 2) - Middle of page.
3. iPhone "Write" page - (1 of 3) - Arrows indicate expandable sections.
4. iPhone "Write" page - (2 of 3) - Arrows indicate expandable sections.
5. iPhone "Write" page - (3 of 3) - Widescreen. - Version 1.0.9
6. iPhone Comment Moderation page
7. iPhone Manage Posts page - (1 of 2) - Main page
8. iPhone Manage Posts page - (2 of 2) - Applying filter
9. iPhone Manage Profile page
