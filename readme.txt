=== Rotating Tweets (Twitter widget and shortcode) ===
Contributors: mpntod
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9XCNM4QSVHYT8
Tags: shortcode,widget,twitter,rotating,rotate,rotator,tweet,tweets,animation,jquery,jquery cycle,cycle,multilingual
Requires at least: 2.6
Tested up to: 3.5
Stable tag: 1.3.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Twitter widget and shortcode to show your latest tweets one at a time an animated rotation

== Description ==
* **Replaces a [shortcode](http://codex.wordpress.org/Shortcode) such as `[rotatingtweets screen_name='your_twitter']`, or a [widget](http://codex.wordpress.org/WordPress_Widgets), with a rotating display of your most recent tweets**
* **Supports v 1.1 of the API** - yes! it will keep working after March 2013
* **Space efficient** - instead of showing all your tweets at once, shows one at a time and then smoothly replaces it with the next one. After showing all your tweets, loops back to the beginning again.
* **Reliable** - keeps showing your latest Tweets even if the Twitter website is down.
* **Customisable** - you decide whose tweets to show, how many to show, whether to include retweets and replies, and whether to show a follow button. You can also decide how quickly the tweets rotate and what type of animation to use.
* Gives you the option to show a fully customisable Twitter 'follow' button
* Replaces [t.co](http://t.co) links with the original link
* Caches the most recent data from Twitter to avoid problems with rate limiting
* Uses [jQuery](http://jquery.com/) and [jQuery.Cycle](http://jquery.malsup.com/cycle/) to produce a nice smooth result.
* **Multi-lingual** - now set up to be multi-lingual. The Twitter 'follow' button is automatically translated to match your site's language setting [if Twitter has made the appropriate language available](https://dev.twitter.com/docs/api/1.1/get/help/languages). Also uses [Wordpress's multi-lingual capability](http://codex.wordpress.org/I18n_for_WordPress_Developers) to enable translation of all the other text used by the plug-in via language packs.

Currently the following languages are available:
* US English *(complete)*
* British English *(complete - mainly changing 'favorite' to 'favourite'!)*
* German *(basic tweet display only)*
* Spanish *(tweet display only)*
* Italian *(tweet display only)*
* Dutch *(tweet display only)*

If you have made the plug-in work in your language, please send the [gettext PO and MO files](http://codex.wordpress.org/I18n_for_WordPress_Developers) to [me](http://www.martintod.org.uk/contact-martin/) and I will then share them with everyone else. You can download [the latest POT file](http://plugins.svn.wordpress.org/rotatingtweets/trunk/languages/rotatingtweets.pot), and [PO files in each language](http://plugins.svn.wordpress.org/rotatingtweets/trunk/languages/) from this site. You may find [Poedit](http://www.poedit.net/) rather useful for translation and creation of PO and MO files.

If you'd like to see what the plug-in looks like in action, you can [see the plug-in working here](http://www.martintod.org.uk/2012/05/29/new-twitter-plugin-to-show-tweets-in-rotation/).

== Installation ==
= Installation =
1. Upload the contents of `rotatingtweets.zip` to the `/wp-content/plugins/` directory or use the Wordpress installer
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the [My applications page](https://dev.twitter.com/apps) on the Twitter website to set up your website as a new Twitter 'application'. You may need to log-in using your Twitter user name and password.
1. If you don't already have a suitable 'application' to use for your website, set one up on the [Create an Application page](https://dev.twitter.com/apps/new). It's normally best to use the name, description and website URL of the website where you plan to use Rotating Tweets. You don't need a Callback URL.
1. After clicking **Create your Twitter application**, on the following page, click on **Create my access token**.
1. Copy the **Consumer key**, **Consumer secret**, **Access token** and **Access token secret** from your Twitter application page into the Rotating Tweets settings page. Hit save. If there is a problem, you will see an error message.
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
* `official_format` = `'1'` or `'2'` - show official format - optional - default is `'0'`
* `timeout` = time that each tweet is shown in milliseconds - optional - default is `'4000'` (i.e. 4 seconds)
* `no_show_count` = `'0'` or `'1'` - remove the follower count from the Twitter follow button - optional - default is `'0'`
* `no_show_screen_name` = `'0'` or `'1'` - remove the screen name from the Twitter follow button - optional - default is `'0'`
* `show_meta_timestamp` = `'0'` or `'1'` - show the time and date of each tweet - optional - default is `'1'`
* `show_meta_screen_name` = `'0'` or `'1'` - show who posted each tweet - optional - default is `'1'`
* `show_meta_via` = `'0'` or `'1'` - show how each tweet was posted - optional - default is `'1'`
* `show_meta_reply_retweet_favorite` = `'0'` or `'1'` - show 'reply', 'retweet' and 'favorite' buttons - optional - default is `'0'`
* `links_in_new_window` = `'0'` or `'1'` - show links in a new tab or window - default is `'0'`
* `rotation_type` = any of the options listed on the [jQuery.cycle website](http://jquery.malsup.com/cycle/browser.html) - default is `'scrollUp'`
* `url_length` = sets the length that the URL should be trimmed to...

although the only one you *have* to have is `screen_name`.

== Credits ==
Most of this is my own work, but special thanks are owed to:

* The [jQuery](http://jquery.com/) team
* [Mike Alsup](http://jquery.malsup.com/cycle/) for [jQuery.Cycle](http://jquery.malsup.com/cycle/)
* [Abraham Williams](http://abrah.am) for [TwitterOAuth](https://github.com/abraham/twitteroauth)
* [Liam Gaddy](http://profiles.wordpress.org/lgladdy/) at [Storm Consultancy](http://www.stormconsultancy.co.uk/) for [his work](http://www.stormconsultancy.co.uk/blog/development/tools-plugins/oauth-twitter-feed-for-developers-library-and-wordpress-plugin/) on [oAuth Twitter Feed for Developers](http://wordpress.org/extend/plugins/oauth-twitter-feed-for-developers/) (although I ended up using it for inspiration rather than plugging it in directly).
* All the people who have given advice and suggested improvements

== Frequently Asked Questions ==
= How often does the plug-in call Twitter =
In most cases, each use (or "instance") of this plug-in gets data from Twitter every 2 minutes. The exception is when two or more instances share the same settings (screen name etc.), in which case they share the same data rather than each calling it separately.

= How can I add a Twitter bird to the left of my tweets? =
You can do this by going to the `rotatingtweets/css` directory and renaming `yourstyle-sample.css` to `yourstyle.css`.  This displays a Twitter bird to the left of your tweets.  Any CSS you put into `yourstyle.css` won't be overwritten when the plug-in is upgraded to the latest version.

= The Rotating Tweets are not rotating. What can I do? =
This normally happens if there is more than one copy of jQuery installed on a page - or more than one copy of jQuery.cycle.
To see if this is the case, you can search the HTML on your website to see if either script is called more than once.  The quickest way is to search the page for `jquery` and look out for lines that contain `jquery.min.js` or `jquery.cycle.all.min.js`.
The problem is that the second (or third) copy of the script overwrites all previous versions and the scripts that go with them.  This is particularly likely to happen with old templates or plug-ins.
If this is the case, the first thing to check is that you have upgraded your template or your plug-in to the latest version.

== Upgrade notice ==
= 1.3.10 =
Includes an important upgrade needed for Rotating Tweets to keep working after March 2013. Supports version 1.1 of the Twitter API. Fixed problem with hashtags.

== Changelog ==
= 1.3.10 =
Fixed hashtag links

= 1.3.9 =
Moved to [Semantic Versioning](http://semver.org/)

= 0.712 (1.3.8) =
Fixed bug with `console.log` javascript on IE.

= 0.711 (1.3.7) =
Fixed up a significant problem with cacheing.

= 0.709 (1.3.6) =
Tidying up error reporting.

= 0.707 (1.3.5) =
Fixes major bug resulting from upgrade to handle Twitter API v 1.1

= 0.706 (1.3.4) =
Change to JavaScript to improve width handling for tweets.

= 0.703 (1.3.3) =
Minor code tidying to improve debugging and increase speed!

= 0.702 (1.3.2) =
Adjustment to javascript and CSS to cope with long links or long words

= 0.701 (1.3.1) =
Very minor mistake in rendering code

= 0.700 (1.3.0) =
**Important upgrade needed for Rotating Tweets to keep working after March 2013. Supports version 1.1 of the Twitter API.**

= 0.625 (1.2.4) =
Enabled users to make all links open in a new tab or window

= 0.623 (1.2.3) =
Fixed a problem where a short name fitted inside a long one - e.g. @rotary and @rotarycrocus

= 0.622 (1.2.2) =
Escaped title tags

= 0.621 (1.2.1) =
Fixed timezone problem.

= 0.620 (1.2.0) =
Added option to show links in a new window
Fix problem with selection of 20 second rotating speed.

= 0.613 (1.1.6) =
Fixed instructions in plug-ins list.

= 0.612 (1.1.5) =
Fixed error message caused by last fix causing tweets to repeat.

= 0.611 (1.1.4) =
Finally ran with debug and removed all the error messages.

= 0.610 (1.1.3) =
Starts to add options to allow for different length URLs

= 0.602 (1.1.2) =
Fixes bug with Javascript

= 0.601 (1.1.1) =
Fixes problem with stylesheet

= 0.600 (1.1.0) =
Now includes options consistent with Twitter display options
Tidied up code.

= 0.505 (1.0.0) =
Minimised Javascript. Set-up for I18n.

= 0.502 (0.4.1) =
Javascript fix for zero height tweets problem

= 0.500 (0.4.0) =
Adds options for how tweet information is displayed and how the tweet rotates.

= 0.492 (0.3.1) =
Solves `Cannot use string offset as an array` error on line 232

= 0.491 (0.3.0) =
Lets you customise the Twitter 'follow' button. Fixes problem with media links. Sorts problem of overlong links reshaping widgets.

= 0.48 (0.2.6) =
More detailed error messages for Wordpress installations unable to access Twitter.
Fixes problem on the zeeBizzCard template and sets up fix for other templates that use their own install of the `jquery-cycle` javascript.

= 0.471 (0.2.5) = 
Making sure that cache never gets overwritten unless new, valid twitter data has been downloaded.
Dealing with the problem that someone in a long conversation may not get enough valid tweets to show by asking for only 20 tweets from Twitter.

= 0.46 (0.2.4) = 
Properly handles rate-limiting by Twitter

= 0.44 (0.2.3) = 
Removes follow button if Twitter has returned an empty value

= 0.43 (0.2.2) = 
Improved error checking if Twitter has returned an empty value

= 0.42 (0.2.1) =
Fixed major bug causing crashes when Twitter goes down

= 0.40 (0.2.0) =
Added ability to alter speed of rotation

= 0.30 (0.1.8) =
Fixes bug - problem with `get_object_vars()` on line 193

= 0.29 (0.1.7) =
Better handling of retweets. No longer cuts off the end of the text on longer RTs.

= 0.28 (0.1.6) =
Properly fixes flaw in how flags are handled.

= 0.27 (0.1.5) =
Fixed flaw in how flags are handled.

= 0.26 (0.1.4) =
Stops display and cacheing of non-existent twitter feeds

= 0.25 (0.1.3) =
Stops display and cacheing of faulty twitter feeds

= 0.21 (0.1.2) =
Replaced a missing `</div>` in the follow-button code (with thanks to [jacobp](http://wordpress.org/support/profile/jacobp) for spotting it and suggesting a fix)

= 0.2 (0.1.1) =
Fixed a problem with cacheing

= 0.1 (0.1.0) =
First published version

== Screenshots ==
1. This animation shows rotating tweets inserted into a blog-post via a short code. It is slightly faster than the default setting, but gives a sense of what you get.
2. You can add rotating tweets via a Widget:
3. Or by using a shortcode:
