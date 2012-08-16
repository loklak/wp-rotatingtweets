=== Rotating Tweets (Twitter widget and shortcode) ===
Contributors: mpntod
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9XCNM4QSVHYT8
Tags: shortcode,widget,twitter,rotating,rotate,rotator,tweet,tweets,animation,jquery,jquery cycle,cycle,multilingual
Requires at least: 2.6
Tested up to: 3.4.1
Stable tag: 0.505
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Twitter widget and shortcode to show your latest tweets one at a time an animated rotation

== Description ==
* **Replaces a [shortcode](http://codex.wordpress.org/Shortcode) such as `[rotatingtweets screen_name='your_twitter']`, or a [widget](http://codex.wordpress.org/WordPress_Widgets), with a rotating display of your most recent tweets**
* **Space efficient** - instead of showing all your tweets at once, shows one at a time and then smoothly replaces it with the next one. After showing all your tweets, loops back to the beginning again.
* **Reliable** - keeps showing your latest Tweets even if the Twitter website is down.
* **Customisable** - you decide whose tweets to show, how many to show, whether to include retweets and replies, and whether to show a follow button. You can also decide how quickly the tweets rotate and what type of animation to use.
* Gives you the option to show a fully customisable Twitter 'follow' button
* Replaces [t.co](http://t.co) links with the original link
* Caches the most recent data from Twitter to avoid problems with rate limiting
* Uses [jQuery](http://jquery.com/) and [jQuery.Cycle](http://jquery.malsup.com/cycle/) to produce a nice smooth result.
* **Multi-lingual** - now set up to be multi-lingual. The Twitter 'follow' button is automatically translated to match your site's language setting [if Twitter has made the appropriate language available](https://dev.twitter.com/docs/api/1/get/help/languages). Also uses [Wordpress's multi-lingual capability](http://codex.wordpress.org/I18n_for_WordPress_Developers) to enable translation of all the other text used by the plug-in via language packs.  If you have made the plug-in work in your language, please send the [gettext PO and MO files](http://codex.wordpress.org/I18n_for_WordPress_Developers) to [me](http://www.martintod.org.uk/contact-martin/) and I will then share them with everyone else. You can download [the latest POT file](http://plugins.svn.wordpress.org/rotatingtweets/trunk/languages/rotatingtweets.pot), and [PO files in each language](http://plugins.svn.wordpress.org/rotatingtweets/trunk/languages/) from this site. You may find [Poedit](http://www.poedit.net/) rather useful for translation and creation of PO and MO files.

If you'd like to see what the plug-in looks like in action, you can [see the plug-in working here](http://www.martintod.org.uk/2012/05/29/new-twitter-plugin-to-show-tweets-in-rotation/).

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
* `no_show_count` = `'0'` or `'1'` - remove the follower count from the Twitter follow button - optional - default is `'0'`
* `no_show_screen_name` = `'0'` or `'1'` - remove the screen name from the Twitter follow button - optional - default is `'0'`
* `show_meta_timestamp` = `'0'` or `'1'` - show the time and date of each tweet - optional - default is `'1'`
* `show_meta_screen` = `'0'` or `'1'` - show who posted each tweet - optional - default is `'1'`
* `show_meta_via` = `'0'` or `'1'` - show how each tweet was posted - optional - default is `'1'`
* `show_meta_reply_retweet_favorite` = `'0'` or `'1'` - show 'reply', 'retweet' and 'favorite' buttons - optional - default is `'0'`
* `rotation_type` = any of the options listed on the [jQuery.cycle website](http://jquery.malsup.com/cycle/browser.html) - default is `'scrollUp'`

although the only one you *have* to have is `screen_name`.

== Frequently Asked Questions ==
= How often does the plug-in call Twitter =
In most cases, each use (or "instance") of this plug-in gets data from Twitter every 2 minutes. The exception is when two or more instances share the same settings (screen name etc.), in which case they share the same data rather than each calling it separately.

== Upgrade notice ==
= 0.505 =
Minimised Javascript. Set-up for I18n.  Includes Javascript fix for zero height tweets problem.

== Changelog ==
= 0.505 =
Minimised Javascript. Set-up for I18n.

= 0.502 =
Javascript fix for zero height tweets problem

= 0.500 =
Adds options for how tweet information is displayed and how the tweet rotates.

= 0.492 =
Solves `Cannot use string offset as an array` error on line 232

= 0.491 =
Lets you customise the Twitter 'follow' button. Fixes problem with media links. Sorts problem of overlong links reshaping widgets.

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
