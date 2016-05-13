=== Arconix Plugins ===
Contributors: jgardner03
Donate link: http://arcnx.co/acpldonation
Tags: arconix, plugins
Requires at least: 4.3
Tested up to: 4.5
Stable tag: 1.0.0

Made for WordPress plugin developers, this plugin will help you display your WP.org hosted plugins on your website by accessing the WP.org plugin API.

== Description ==
**Support for this plugin will be provided at [Github.com](http://arcnx.co/acplissues)**

Showcase your WP.org-hosted plugins on your website with this easy-to-use plugin. Add each plugin as a "post" under the "plugins" post type. Enter the plugin slug that's used on WP.org, along with any other links you'd like to include, and that's it! Navigate to yoursite.com/plugins/post-slug and you'll see all the details available on WP.org. If you'd like to supply your own layout, add a filter and you have complete control over the post content.

== Installation ==

You can download and install Arconix Plugins using the built in WordPress plugin installer. If you download the plugin manually, make sure the files are uploaded to `/wp-content/plugins/arconix-plugins/`.

Activate Arconix Plugins in the "Plugins" admin panel using the "Activate" link.

== Upgrade Notice ==

Upgrade normally via your WordPress admin -> Plugins panel.

== Frequently Asked Questions ==

= How can I show my plugins?  =

This plugin utlizes WordPress' built-in post_type_archive, so simply visit yoursite.com/plugins for the list and yoursite.com/plugins/post-slug for the individual plugin

= Why is the plugin basically unstyled? =

With no 2 themes exactly alike, it's impossible to style a plugin that seamlessly integrates without issue. That's why I made the plugin flexible -- copy `includes/arconix-plugins.css` to the root of your theme's folder and make your desired modifications. My plugin will automatically load that CSS file instead. If you'd like to bundle the styles directly into your style.css file, there's a filter available that will avoid loading the CSS file entirely.

= Is there any other documentation? =

Currently, no.

= I have a problem or a bug =

* Post your concern at the [Issues section on Github](http://arcnx.co/aplissues)

= I have a great idea for your plugin! =

That's fantastic! Feel free to submit a pull request over at [Github](http://arcnx.co/aplsource), add an idea to the [Trello Board](http://arcnx.co/apltrello), or you can contact me through [Twitter](http://arcnx.co/twitter), [Facebook](http://arcnx.co/facebook) or my [Website](http://arcnx.co/1)

== Screenshots ==


== Changelog ==

= 1.0.0 =
Initial release