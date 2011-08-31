=== Post Content Cleaner ===
Contributors: hebeisenconsulting
Donate link: http://hebeisenconsulting.com/
Tags: post content cleaner, post cleaner, html strip, html tag remove
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.0

== Description ==

Post Content cleaner allows you to clean up the HTML code of your posts. Depending on where the post came from or how it was written, it may be ridden full of P, DIV, SPAN, multiple spaces and &nbsp; characters. If the HTML tags are desired to be kept, the plugin also has features to remove or ignore tag parameters found in posts. This is especially useful if one copied and pasted their content from non-standard or arbitrary sources. Using the plugin as a filter also avoids permanent changes to post data.

== Installation ==

1. Upload post-clean folder to the `/wp-content/plugins/` directory OR through wp-admin,
upload post-clean.zip in plugin 'Add New' page.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Settings can be found under 'Setting' menu at the left side of wp-admin.

== Frequently Asked Questions ==

= How does this plugin work? =

Post Content Cleaner strips unwanted html tags, its parameters, whitespace characters, or a combination thereof.

= Does it affect my wordpress database =

Post Content Cleaner works in two ways that you may choose from.

1.) Perform stripping through the filter hook only. 
With this option, Post Content Cleaner does not perform stripping on the data in the database.
Changes are instead made right before the page is loaded.

2.) Perform stripping on database data.
This option is meant for advanced users only.
It will strip html tags permanently from the post data in the database.
Not reversible!

= What html tags are currently supported? =

In this version, <span>, <div>, <p>, &nbsp; and white spaces can be removed depending on your choice.