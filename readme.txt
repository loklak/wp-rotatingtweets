=== Rotating Tweets widget and shortcode ===
Contributors: mpntod
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9XCNM4QSVHYT8
Tags: shortcode,widget,twitter,rotating,rotate
Requires at least: 2.6
Tested up to: 3.3.2
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Replaces a shortcode such as [rotatingtweets screen_name='your_twitter_name'], or a widget, with a rotating tweets display 

== Description ==
* Replaces a [shortcode](http://codex.wordpress.org/Shortcode) such as `[rotatingtweets screen_name='your_twitter_name']`, or a [widget](http://codex.wordpress.org/WordPress_Widgets), with a rotating display of your most recent tweets
* Space efficient - instead of showing all your tweets at once, shows one at a time and then smoothly replaces it with the next one. After showing all your tweets, loops back to the beginning again.
* Customisable - you decide whose tweets to show, how many to show, whether to include retweets and replies, and whether to show a follow button
* Replaces t.co links with the original link
* Caches the most recent data from Twitter to avoid problems with rate limiting
* Uses [jQuery](http://jquery.com/) and [jQuery.Cycle](http://jquery.malsup.com/cycle/) to produce a nice smooth result.

If you'd like to see what it looks like in action, you can [see the plug-in working here](http://www.martintod.org.uk/2012/05/29/new-twitter-plugin-to-show-tweets-in-rotation/).
== Installation ==
1. Upload the contents of `rotatingtweets.zip` to the `/wp-content/plugins/` directory or use the Wordpress installer
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place a shortcode such `[rotatingtweets screen_name='mpntod']` in your post or page, or use a widget

Possible variables for the shortcode include:

* `screen_name` = Twitter user name - required
* `include_rts` = `'0'` or `'1'` - include retweets - optional
* `exclude_replies` = `'0'` or `'1'` - exclude replies - optional
* `tweet_count` = number of tweets to show - optional - default is 5
* `show_follow` = `'0'` or `'1'` - show follow button - optional

But you may just decide to use the 'Rotating Tweets' widget!

== Frequently Asked Questions ==
= Are there any frequently asked questions? =
Not yet. Why not ask one?

== Upgrade notice ==
= 0.2 =
Fixes a serious problem with cacheing of different feeds

== Changelog ==
= 0.2 =
Fixed a problem with cacheing

= 0.1 =
First published version

== Screenshots ==
1. You can add rotating tweets to a post like this:
2. Or add them via a widget:
3. This animation is slightly fast, but gives a sense of what you get: