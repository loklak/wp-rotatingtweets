=== Rotating Tweets widget and shortcode ===
Contributors: mpntod
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9XCNM4QSVHYT8
Tags: shortcode,widget,twitter,rotating,rotate,rotator,tweet,tweets,animation,jquery
Requires at least: 2.6
Tested up to: 3.4
Stable tag: 0.486
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Widget and/or shortcode to show your latest tweets one at a time an animated rotation

== Description ==
* Replaces a [shortcode](http://codex.wordpress.org/Shortcode) such as `[rotatingtweets screen_name='your_twitter_name']`, or a [widget](http://codex.wordpress.org/WordPress_Widgets), with a rotating display of your most recent tweets
* Space efficient - instead of showing all your tweets at once, shows one at a time and then smoothly replaces it with the next one. After showing all your tweets, loops back to the beginning again.
* Reliable - keeps showing your latest Tweets even if the Twitter website is down.
* Customisable - you decide whose tweets to show, how many to show, whether to include retweets and replies, and whether to show a follow button. You can also decide how quickly the tweets rotate.
* Replaces [t.co](http://t.co) links with the original link
* Caches the most recent data from Twitter to avoid problems with rate limiting
* Uses [jQuery](http://jquery.com/) and [jQuery.Cycle](http://jquery.malsup.com/cycle/) to produce a nice smooth result.

If you'd like to see what it looks like in action, you can [see the plug-in working here](http://www.martintod.org.uk/2012/05/29/new-twitter-plugin-to-show-tweets-in-rotation/).
== Installation ==
= Installation =
1. Upload the contents of `rotatingtweets.zip` to the `/wp-content/plugins/` directory or use the Wordpress installer
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place a shortcode such `[rotatingtweets screen_name='mpntod']` in your post or page, or use a widget

= Set-up =
Options include:

1. Going to the Widgets menu on the admin page and adding the Rotating Tweets widget. Options include the name of the Twitter account to show, whether to show retweets and the speed of rotation.
1. Using the basic Rotating Tweets shortcode, for example `[rotatingtweets screen_name='mpntod']`
1. Using a more complicated Rotating Tweets shortcode, for example `[rotatingtweets screen_name='mpntod' include_rts='1' tweet_count='7' timeout='3000']`

= Variables =
Possible variables for the shortcode include:

* `screen_name` = Twitter user name - required
* `include_rts` = `'0'` or `'1'` - include retweets - optional - default is `'0'`
* `exclude_replies` = `'0'` or `'1'` - exclude replies - optional - default is `'0'`
* `tweet_count` = number of tweets to show - optional - default is `5`
* `show_follow` = `'0'` or `'1'` - show follow button - optional - default is `'0'`
* `timeout` = time that each tweet is shown in milliseconds - optional - default is `'4000'` (i.e. 4 seconds)

== Frequently Asked Questions ==
= How often does the plug-in call Twitter =
In most cases, each use (or "instance") of this plug-in gets data from Twitter every 2 minutes. The exception is when two or more instances share the same settings (screen name etc.), in which case they share the same data rather than each calling it separately.

== Upgrade notice ==
= 0.48 =
Includes fix for the major bug causing crashes when Twitter goes down. More detailed error messages for Wordpress installations unable to access Twitter.

== Changelog ==
= 0.48 =
More detailed error messages for Wordpress installations unable to access Twitter.
Fixes problem on the zeeBizzCard template and sets up fix for other templates that use their own install of the `jquery-cycle` javascript.

= 0.471 = 
Making sure that cache never gets overwritten unless new, valid twitter data has been downloaded.
Dealing with the problem that someone in a long conversation may not get enough valid tweets to show by asking for only 20 tweets from Twitter.

= 0.46 = 
Properly handles rate-limiting by Twitter

= 0.44 = 
Removes follow button if Twitter has returned an empty value

= 0.43 = 
Improved error checking if Twitter has returned an empty value

= 0.42 =
Fixed major bug causing crashes when Twitter goes down

= 0.40 =
Added ability to alter speed of rotation

= 0.30 =
Fixes bug - problem with `get_object_vars()` on line 193

= 0.29 =
Better handling of retweets. No longer cuts off the end of the text on longer RTs.

= 0.28 =
Properly fixes flaw in how flags are handled.

= 0.27 =
Fixed flaw in how flags are handled.

= 0.26 =
Stops display and cacheing of non-existent twitter feeds

= 0.25 =
Stops display and cacheing of faulty twitter feeds

= 0.21 =
Replaced a missing `</div>` in the follow-button code (with thanks to [jacobp](http://wordpress.org/support/profile/jacobp) for spotting it and suggesting a fix)

= 0.2 =
Fixed a problem with cacheing

= 0.1 =
First published version

== Screenshots ==
1. This animation shows rotating tweets inserted into a blog-post via a short code. It is slightly faster than the default setting, but gives a sense of what you get.
2. You can add rotating tweets via a Widget:
3. Or by using a shortcode:
