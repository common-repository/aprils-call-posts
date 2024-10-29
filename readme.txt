=== April's Call Posts ===
Contributors: springthistle
Tags: posts, shortcode
Requires at least: 5.0
Tested up to: 5.6
Stable tag: 2.1.1
Requires PHP: 5.6

Via shortcode, lets you call in a list of posts that are filtered, ordered, and displayed based on criteria you provide.

== Description ==

This plugin is useful if you are using lots of posts in a variety of ways on your website, i.e. not just on your homepage and not just separated out by categories. For example, You may have a blog with lots of information on upcoming events and lots of announcements. You have a variety of people who come to your blog looking for different information, and it's hard for them to filter through everything. You can have a "Cats" page on which you talk about your stance on cats, and use <code>[ahs_callposts]</code> to also call in a list of posts in the "cats" category, perhaps only the most recent 10, perhaps just titles, perhaps displaying just excerpts, perhaps displaying an image for each post. Then have a separate page for the "dogs" category. Etc!

== Features ==

* Specify category
* Specify number of posts
* Specify content style
* Specify random order
* Specify multiple columns
* Create your own template (NEW as of 2.0)
* ... and more
* Choose global default settings that can be overridden for any individual instance of the shortcode.

== Installation ==

1. Upload the aprils-call-posts directory to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Study the documentation so you understand the options
1. Start using the shortcode

== Frequently Asked Questions ==

= No questions yet =

No answers yet.

== Screenshots ==

1. Documentation Area 1
1. Documentation Area 2
1. Documentation Area 3
1. Global Settings
1. Template Settings

== Changelog ==

= 2.1.1 =
* Added screenshots.
* Fixed Readme.txt error.

= 2.1 =
* Cleaned up code, updated to be compatible with latest PHP and WordPress.
* Updated compatibility version.

= 2.0.14 =
* Added option to ignore globally-set custom_field on specific shortcode instances.
* Updated compatibility version.

= 2.0.13 =
* Updated compatibility version.

= 2.0.12 =
* Replaced a custom expert-generator with WordPress's built-in one, to solve efficiency issues.

= 2.0.11 =
* Fixed an error where excerpts and content were being manufactured even for templates that don't include them
* Updated compatibility version

= 2.0.10 =
* Fixed a small error introduced by 2.0.9

= 2.0.9 =
* Fixed a bug that was doing custom-post-type ordering incorrectly when there were a lot of posts

= 2.0.8 =
* Fixed a bug introduced in 2.0.7 that affected situations where a shortcode was calling a single category (not custom tax)

= 2.0.7 =
* Added ability to specify a different taxonomy than 'category' with <code>category_tax</code>

= 2.0.6 =
* Added more tags (div, span, script, iframe) to the list of tags that are not stripped out with CONTENTNOIMG
* Added category option "byid" which only grabs ids specified. You can also now specify an order of "post__in" which is useful in this case.
* Updated compatibility and tweaked admin display css.

= 2.0.5 =
* Fixed minor regex error causing wrong image sizes to be pulled.

= 2.0.4 =
* Added option to pull in posts specified by ID.

= 2.0.3 =
* Minor tweaks including adding a donate button.

= 2.0.2 =
* Fixed a problem that converted dollar amounts (e.g. $10) to zero (0).

= 2.0.1 =
* Default order set to DESC instead of ASC
* Template tag added for 'full content sans images'
* Can now add a header to the resulting list that doesn't show if list is empty

= 2.0 =
* NEW!! Added a template-driven system for post display
* FUN! Re-designed admin page to have tabs
* Added option to specify a post_type to handle custom post types
* Removed requirement for specifying a category/type

= 1.6.3 =
* Fixed random ordering

= 1.6.2 =
* Fixed problems with "linktitle"
* Fixed broken 'edit' image if WP not installed in root

= 1.6.1 =
* Fixed title-style list problem some people were having
* Fixed get_the_excerpt() problem

= 1.6 =
* Added proper LI as option for separating title-style list

= 1.5 =
* Added option to pull posts in at random instead of just by post_date or custom field
* Added option to control size of the image, if it's included
* Added option to choose 'read more' text and pre-'read more' text
* Post titles now linked by default. Added option to un-link them
* Miscellaneous code cleanup

= 1.4 =
* Added shortcode examples to settings page.
* Added col_item_width, so let you control the width of columns when in two-column mode
* Moved included styles from stylesheet to settings page, to make them easier to maintain

= 1.3 =
* Now can specify whether or not to show the date and if so, what PHP date() format to use.

= 1.2 =
* Now can specify multiple categories.

= 1.1 =
* Fixed path to icon_edit.gif.
* Added do_action so that shortcode can be used in widgets

= 1.0 =
* No significant changes have been made to the functionality; it's just being converted to a legit plugin.

= 0.5 =
* The first version of this plugin was actually a collection of functions in a single website's functions.php file.
