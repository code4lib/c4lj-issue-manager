=== Issue Manager ===
Contributors: jbrinley
Tags: admin, management, publish, magazine, journal, periodical
Requires at least: 2.7
Tested up to: 2.8
Stable tag: 1.4.4

== Description ==

Allows an editor to publish an "issue", which is to say, all pending posts with a given category, all at once. Until a category is published, all posts with that category will remain in the pending state.

Note: The current version works with version 2.7+. If you are using WordPress version 2.6.x, use an older version of this plugins (1.3.x is recommended).

== Installation ==

This plugin is installed just like any other WordPress plugin. More [detailed installation instructions](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins "Installing Plugins - WordPress Codex") are available on the WordPress Codex.

1. Download
1. Unzip into to `/wp-contents/plugins/`
1. Activate the plugin
1. Access the controls (*Posts->Issues*)

All categories start out "Ignored", meaning the plugin has nothing to do with them.

A category can be set to "Unpublished", changing any published posts in that category to pending status. Any attempt to publish a post with that category will set its status to pending.

A category can be published, which will set the status of any pending posts in that category to published. Those posts have their timestamps adjusted to the time selected, with a one second space between each post.