<?php
/*
Plugin Name: Rotating Tweets (Twitter widget & shortcode)
Description: Replaces a shortcode such as [rotatingtweets userid='your_twitter_name'], or a widget, with a rotating tweets display 
Version: 0.502
Author: Martin Tod
Author URI: http://www.martintod.org.uk
License: GPL2
*/
/*  Copyright 2012 Martin Tod email : martin@martintod.org.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * Replaces a shortcode such as [rotatingtweets userid='your_twitter_name'], or a widget, with a rotating tweets display 
 *
 * @package WordPress
 * @since 3.3.2
 *
 */

/**
 * rotatingtweets_Widget_Class
 * Shows tweets sequentially for a given user
 */
class rotatingtweets_Widget extends WP_Widget {
    /** constructor */
    function rotatingtweets_Widget() {
        parent::WP_Widget(false, $name = 'Rotating Tweets',array('description'=>'A widget to show tweets for a particular user in rotation.'));	
		if ( is_active_widget( false, false, $this->id_base ) )
			wp_enqueue_script( 'jquery' );
			# Get Stylesheet
			$style = get_stylesheet();
			switch ($style):
				case 'zeebizzcard':
					wp_enqueue_script( 'rotating_tweet', plugins_url('js/rotating_tweet.js', __FILE__),array('jquery','zee_jquery-cycle'),FALSE,FALSE );
					break;
				default:
					wp_enqueue_script( 'jquery-cycle', plugins_url('js/jquery.cycle.all.js', __FILE__),array('jquery'),FALSE,FALSE );
					wp_enqueue_script( 'rotating_tweet', plugins_url('js/rotating_tweet.js', __FILE__),array('jquery','jquery-cycle'),FALSE,FALSE );
					break;
			endswitch;
			wp_enqueue_style( 'rotating_tweet', plugins_url('css/style.css', __FILE__));
		}

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		$newargs['screen_name'] = $instance['tw_screen_name'];
		$newargs['include_rts'] = $instance['tw_include_rts'];
		$newargs['exclude_replies'] = $instance['tw_exclude_replies'];
		$newargs['tweet_count'] = $instance['tw_tweet_count'];
		$newargs['show_follow'] = $instance['tw_show_follow'];
		$newargs['timeout'] = $instance['tw_timeout'];
		$newargs['show_meta_timestamp'] = !$instance['tw_hide_meta_timestamp'];
		$newargs['show_meta_screen_name'] = !$instance['tw_hide_meta_screen_name'];
		$newargs['show_meta_via'] = !$instance['tw_hide_meta_via'];
		$newargs['rotation_type'] = $instance['tw_rotation_type'];
		$newargs['show_meta_reply_retweet_favorite'] = $instance['tw_show_meta_reply_retweet_favorite'];
		switch($newargs['show_follow']) {
		case 2: 
			$newargs['no_show_count'] = TRUE;
			$newargs['no_show_screen_name'] = FALSE;
			break;
		case 3: 
			$newargs['no_show_count'] = FALSE;
			$newargs['no_show_screen_name'] = TRUE;
			break;
		case 4:
			$newargs['no_show_count'] = TRUE;
			$newargs['no_show_screen_name'] = TRUE;
			break;
		default: 
			$newargs['no_show_count'] = FALSE;
			$newargs['no_show_screen_name'] = FALSE;
			break;
		}
		if(empty($newargs['timeout'])) $newargs['timeout'] = 4000;
		$tweets = rotatingtweets_get_tweets($newargs['screen_name'],$newargs['include_rts'],$newargs['exclude_replies']);
        ?>
              <?php echo $before_widget; 
						if ( $title )
							echo $before_title . $title . $after_title; 
						rotating_tweets_display($tweets,$newargs,TRUE);
					echo $after_widget;
#					echo "<!-- ";print_r($newargs);echo " -->";
					?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['tw_screen_name'] = strip_tags(trim($new_instance['tw_screen_name']));
		$instance['tw_rotation_type'] = strip_tags(trim($new_instance['tw_rotation_type']));
		$instance['tw_include_rts'] = absint($new_instance['tw_include_rts']);
		$instance['tw_exclude_replies'] = absint($new_instance['tw_exclude_replies']);
		$instance['tw_tweet_count'] = max(1,intval($new_instance['tw_tweet_count']));
		$instance['tw_show_follow'] = absint($new_instance['tw_show_follow']);
		# Complicated way to ensure the defaults remain as they were before the 0.500 upgrade - i.e. showing meta timestamp, screen name and via, but not reply, retweet, favorite
		$instance['tw_hide_meta_timestamp'] = !$new_instance['tw_show_meta_timestamp'];
		$instance['tw_hide_meta_screen_name'] = !$new_instance['tw_show_meta_screen_name'];
		$instance['tw_hide_meta_via'] = !$new_instance['tw_show_meta_via'];
		$instance['tw_show_meta_reply_retweet_favorite'] = absint($new_instance['tw_show_meta_reply_retweet_favorite']);
		$instance['tw_timeout'] = max(min(intval($new_instance['tw_timeout']/1000)*1000,6000),3000);
	return $instance;
    }
	
    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        $tw_screen_name = esc_attr(trim($instance['tw_screen_name']));
		$tw_rotation_type = $instance['tw_rotation_type'];
		if(empty($tw_rotation_type)) $tw_rotation_type = 'scrollUp';
        $tw_include_rts = absint($instance['tw_include_rts']);
		$tw_exclude_replies = absint($instance['tw_exclude_replies']);
        $tw_tweet_count = intval($instance['tw_tweet_count']);
		$tw_show_follow = absint($instance['tw_show_follow']);
		$metaoption['tw_show_meta_timestamp'] = !$instance['tw_hide_meta_timestamp'];
		$metaoption['tw_show_meta_screen_name'] = !$instance['tw_hide_meta_screen_name'];
		$metaoption['tw_show_meta_via'] = !$instance['tw_hide_meta_via'];
		$metaoption['tw_show_meta_reply_retweet_favorite'] = absint($instance['tw_show_meta_reply_retweet_favorite']);
		$tw_timeout = intval($instance['tw_timeout']);
# If values not set, set default values
		if(empty($tw_timeout)) $tw_timeout = 4000;
		if(empty($tw_tweet_count)) $tw_tweet_count = 5;
        ?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('tw_screen_name'); ?>"><?php _e('Twitter name:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('tw_screen_name'); ?>" name="<?php echo $this->get_field_name('tw_screen_name'); ?>"  value="<?php echo $tw_screen_name; ?>" /></label></p>
		<p><input id="<?php echo $this->get_field_id('tw_include_rts'); ?>" name="<?php echo $this->get_field_name('tw_include_rts'); ?>" type="checkbox" value="1" <?php if($tw_include_rts==1): ?>checked="checked" <?php endif; ?>/><label for="<?php echo $this->get_field_id('tw_include_rts'); ?>"> <?php _e('Include retweets?'); ?></label></p>
		<p><input id="<?php echo $this->get_field_id('tw_exclude_replies'); ?>" name="<?php echo $this->get_field_name('tw_exclude_replies'); ?>" type="checkbox" value="1" <?php if($tw_exclude_replies==1): ?>checked="checked" <?php endif; ?>/><label for="<?php echo $this->get_field_id('tw_exclude_replies'); ?>"> <?php _e('Exclude replies?'); ?></label></p>
		<p><label for="<?php echo $this->get_field_id('tw_tweet_count'); ?>"><?php _e('How many tweets?'); ?> <select id="<?php echo $this->get_field_id('tw_tweet_count'); ?>" name="<?php echo $this->get_field_name('tw_tweet_count');?>">
		<?php 
		for ($i=1; $i<20; $i++) {
			echo "\n\t<option value='$i' ";
		if($tw_tweet_count==$i): ?>selected="selected" <?php endif; 
			echo ">$i</option>";
		}			
		?></select></label></p>
		<p><label for="<?php echo $this->get_field_id('tw_timeout'); ?>"><?php _e('Speed'); ?> <select id="<?php echo $this->get_field_id('tw_timeout'); ?>" name="<?php echo $this->get_field_name('tw_timeout');?>">
		<?php 
		$timeoutoptions = array (
							"3000" => "Faster (3 seconds)",
							"4000" => "Normal (4 seconds)",
							"5000" => "Slower (5 seconds)",
							"6000" => "Slowest (6 seconds)"
		);
		foreach ($timeoutoptions as $val => $words) {
			echo "\n\t<option value='$val' ";
		if($tw_timeout==$val): ?>selected="selected" <?php endif; 
			echo ">$words</option>";
		}			
		?></select></label></p>
		<?php
		# For reference, all the rotations that look good.
		# $goodRotations = array('blindX','blindY','blindZ','cover','curtainY','fade','growY','none','scrollUp','scrollDown','scrollLeft','scrollRight','scrollHorz','scrollVert','shuffle','toss','turnUp','turnDown','uncover');
		$rotationoptions = array (
			'scrollUp' => 'Scroll Up',
			'scrollDown' => 'Scroll Down',
			'scrollLeft' => 'Scroll Left',
			'scrollRight' => 'Scroll Right',
			'fade' => 'Fade'
		);
		asort($rotationoptions);
		?>
		<p><label for="<?php echo $this->get_field_id('tw_rotation_type'); ?>"><?php _e('Type of rotation'); ?> <select id="<?php echo $this->get_field_id('tw_rotation_type'); ?>" name="<?php echo $this->get_field_name('tw_rotation_type');?>">
		<?php 		
		foreach ($rotationoptions as $val => $words) {
			echo "\n\t<option value='$val' ";
		if($tw_rotation_type==$val): ?>selected="selected" <?php endif; 
			echo ">$words</option>";
		}			
		?></select></label></p>
		<?php /* Ask about which Tweet details to show */ ?>
		<p><?php _e('Show tweet details?'); ?></p><p>
		<?php 
		$tweet_detail_options = array(
			'tw_show_meta_timestamp' => 'Time/date of tweet',
			'tw_show_meta_screen_name' => 'Name of person tweeting',
			'tw_show_meta_via' => 'Source of tweet',
			'tw_show_meta_reply_retweet_favorite' => "'reply &middot; retweet &middot; favourite' links"
		);
		$tw_br='';
		foreach ($tweet_detail_options as $field => $text):
		echo $tw_br;
		?>
		<input id="<?php echo $this->get_field_id($field); ?>" name="<?php echo $this->get_field_name($field); ?>" type="checkbox" value="1" <?php if($metaoption[$field]==1): ?>checked="checked" <?php endif; ?>/><label for="<?php echo $this->get_field_id($field); ?>"> <?php _e($text); ?></label>
		<?php 
		$tw_br = "<br />";
		endforeach; ?></p>
		<p><?php _e('Show follow button?'); ?></p>
<?php
		$showfollowoptions = array (
			0 => 'None',
			1 => "Show name and number of followers",
			2 => "Show name only",
			3 => "Show followers only",
			4 => "Show button only"
		);

		foreach ($showfollowoptions as $val => $html) {
			echo "<input type='radio' value='$val' id='".$this->get_field_id('tw_tweet_count_'.$val)."' name= '".$this->get_field_name('tw_show_follow')."'";
			if($tw_show_follow==$val): ?> checked="checked" <?php endif; 
			echo "><label for '".$this->get_field_id('tw_tweet_count_'.$val)."'> $html</label><br />";
		}
	}
} // class rotatingtweets_Widget

// register rotatingtweets_Widget widget
add_action('widgets_init', create_function('', 'return register_widget("rotatingtweets_Widget");'));

function rotatingtweets_contextualtime($small_ts, $large_ts=false) {
  if(!$large_ts) $large_ts = time();
  $n = $large_ts - $small_ts;
  if($n <= 1) return 'less than 1 second ago';
  if($n < (60)) return $n . ' seconds ago';
  if($n < (60*60)) { $minutes = round($n/60); return 'about ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago'; }
  if($n < (60*60*16)) { $hours = round($n/(60*60)); return 'about ' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago'; }
  if($n < (time() - strtotime('yesterday'))) return 'yesterday';
  if($n < (60*60*24)) { $hours = round($n/(60*60)); return 'about ' . $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago'; }
  if($n < (60*60*24*6.5)) return 'about ' . round($n/(60*60*24)) . ' days ago';
  if($n < (time() - strtotime('last week'))) return 'last week';
  if(round($n/(60*60*24*7))  == 1) return 'about a week ago';
  if($n < (60*60*24*7*3.5)) return 'about ' . round($n/(60*60*24*7)) . ' weeks ago';
  if($n < (time() - strtotime('last month'))) return 'last month';
  if(round($n/(60*60*24*7*4))  == 1) return 'about a month ago';
  if($n < (60*60*24*7*4*11.5)) return 'about ' . round($n/(60*60*24*7*4)) . ' months ago';
  if($n < (time() - strtotime('last year'))) return 'last year';
  if(round($n/(60*60*24*7*52)) == 1) return 'about a year ago';
  if($n >= (60*60*24*7*4*12)) return 'about ' . round($n/(60*60*24*7*52)) . ' years ago'; 
  return false;
}

# Processes the shortcode 
function rotatingtweets_display( $atts, $content=null, $code="" ) {
	// $atts    ::= twitter_id,include_rts,exclude_replies, $tweet_count,$show_follow
/**
	Possible values for get_cforms_entries()
	$screen_name :: [text]	Twitter user name
	$include_rts :: [boolean] include RTS - optional
	$exclude_replies :: [boolean] exclude replies - optional
	$tweet_count :: [integer] number of tweets to show - optional - default 5
	$show_follow :: [boolean] show follow button
	$no_show_count :: [boolean] remove count from follow button
	$no_show_screen_name :: [boolean] remove screen name from follow button
*/
	$args = shortcode_atts( array(
			'screen_name' => 'twitter',
			'include_rts' => FALSE,
			'exclude_replies' => FALSE,
			'tweet_count' => 5,
			'show_follow' => FALSE,
			'timeout' => 4000,
			'no_show_count' => FALSE,
			'no_show_screen_name' => FALSE,
			'show_meta_timestamp' => TRUE,
			'show_meta_screen_name' => TRUE,
			'show_meta_via' => TRUE,
			'show_meta_reply_retweet_favorite' => FALSE,
			'rotation_type' => 'scrollUp'
		), $atts ) ;
	extract($args);
	$tweets = rotatingtweets_get_tweets($screen_name,$include_rts,$exclude_replies);
	$returnstring = rotating_tweets_display($tweets,$args,FALSE);
	return $returnstring;
}
add_shortcode( 'rotatingtweets', 'rotatingtweets_display' );

# Get the latest data from Twitter (or from a cache if it's been less than 2 minutes since the last load)
function rotatingtweets_get_tweets($tw_screen_name,$tw_include_rts,$tw_exclude_replies) {
	# Clear up variables
	$cache_delay = 120;
	if($tw_include_rts != 1) $tw_include_rts = 0;
	if($tw_exclude_replies != 1) $tw_exclude_replies = 0;
#	$tw_tweet_count = max(1,intval($tw_tweet_count));
	# Get the option strong
	$stringname = $tw_screen_name.$tw_include_rts.$tw_exclude_replies;
	$optionname = "rotatingtweets-cache";
	$option = get_option($optionname);
	# Attempt to deal with 'Cannot use string offset as an array' error
	if(is_array($option)):
		$latest_json = $option[$stringname]['json'];
		$latest_json_date = $option[$stringname]['datetime'];
		$timegap = time()-$latest_json_date;
	else:
		# Clears the cache and forces a reload
		$timegap = $cache_delay + 1;
		unset($option);
	endif;
	$timegap = time()-$latest_json_date;
	if($timegap > $cache_delay):
		$callstring = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=".urlencode($tw_screen_name)."&include_entities=1&count=70&include_rts=".$tw_include_rts."&exclude_replies=".$tw_exclude_replies;
		$twitterdata = wp_remote_request($callstring);
		if(!is_wp_error($twitterdata)):
			$twitterjson = json_decode($twitterdata['body']);
		else:
			set_transient('rotatingtweets_wp_error',$twitterdata->get_error_messages(), 120);
		endif;
	endif;
	if(!empty($twitterjson->errors)):
		# If there's an error, reset the cache timer to make sure we don't hit Twitter too hard and get rate limited.
		$option[$stringname]['datetime']=time();
		update_option($optionname,$option);
	elseif(!empty($twitterjson->error)):
		# If Twitter is being rate limited, delays the next load until the reset time
		# For some reason the rate limiting error has a different error variable!
		$rate = rotatingtweets_get_rate_data();
		if($rate && $rate->remaining_hits == 0):
			$option[$stringname]['datetime']= $rate->reset_time_in_seconds - $cache_delay + 1;
			update_option($optionname,$option);
		endif;
	elseif(!empty($twitterjson)):
		# If there's regular data, then update the cache and return the data
		unset($firstentry);
		if(is_array($twitterjson)) $firstentry = $twitterjson[0];
		if(!empty($firstentry->text)):
			$latest_json = $twitterjson;
			$option[$stringname]['json']=$latest_json;
			$option[$stringname]['datetime']=time();
			update_option($optionname,$option);
		endif;
	endif;
	return($latest_json);
}

# Gets the rate limiting data to see how long it will be before we can tweet again
function rotatingtweets_get_rate_data() {
	$callstring = "http://api.twitter.com/1/account/rate_limit_status.json";
	$ratedata = wp_remote_request($callstring);
	if(!is_wp_error($ratedata)):
		$rate = json_decode($ratedata['body']);
		return($rate);
	else:
		set_transient('rotatingtweets_wp_error',$ratedata->get_error_messages(), 120);
		return(FALSE);
	endif;
}

# This function is used for debugging what happens when the site is rate-limited - best not used otherwise!
function rotatingtweets_trigger_rate_limiting() {
	$callstring = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=twitter";
	for ($i=1; $i<150; $i++) {
		$ratedata = wp_remote_request($callstring);
	}
}

# Displays the tweets
function rotating_tweets_display($json,$args,$print=TRUE) {
	unset($result);
	$tweet_count = max(1,intval($args['tweet_count']));
	$timeout = max(intval($args['timeout']),0);
	# Check that the rotation type is valid. If not, leave it as 'scrollUp'
	$rotation_type = 'scrollUp';
	# All the valid rotations - if people to use one that looks weird, that's their business!
	$possibleRotations = array('blindX','blindY','blindZ','cover','curtainX','curtainY','fade','fadeZoom','growX','growY','none','scrollUp','scrollDown','scrollLeft','scrollRight','scrollHorz','scrollVert','shuffle','slideX','slideY','toss','turnUp','turnDown','turnLeft','turnRight','uncover','wipe','zoom');
	foreach($possibleRotations as $possibleRotation):
		if(strtolower($args['rotation_type']) == strtolower($possibleRotation)) $rotation_type = $possibleRotation;
	endforeach;
	# Create an ID that has all the relevant info in - rotation type and speed of rotation
	$id = uniqid('rotatingtweets_'.$timeout.'_'.$rotation_type.'_');
	$result = "\n<div class='rotatingtweets' id='$id'>";
	if(empty($json)):
		$result .= "\n\t<div class = 'rotatingtweet'><p class='rtw_main'>Problem retrieving data from Twitter.</p></div>";
		$rate = rotatingtweets_get_rate_data();
		# Check if the problem is rate limiting
		if($rate && $rate->remaining_hits == 0):
			$result .= "\n\t<div class = 'rotatingtweet' style='display:none'><p class='rtw_main'>This website is currently <a href='https://dev.twitter.com/docs/rate-limiting/faq'>rate-limited by Twitter</a>.</p></div>";
			$waittimevalue = intval(($rate->reset_time_in_seconds-time - time())/60);
			$waittime = $waittimevalue." minutes";
			if($waittimevalue == 1) $waittime = "1 minute";
			if($waittimevalue == 0) $waittime = "less than a minute";
			$result .= "\n\t<div class = 'rotatingtweet' style='display:none'><p class='rtw_main'>Next attempt to get data will be in {$waittime}.</p></div>";
		else:
			$error_messages = get_transient('rotatingtweets_wp_error');
			if($error_messages):
				foreach($error_messages as $error_message):
					$result .= "\n\t<div class = 'rotatingtweet' style='display:none'><p class='rtw_main'>Wordpress error message: ".$error_message.".</p></div>";
				endforeach;
			endif;
			$result .= "\n\t<div class = 'rotatingtweet' style='display:none'><p class='rtw_main'>Please check the Twitter name used in the settings.</p></div>";
		endif;
	else:
		$tweet_counter = 0;
		foreach($json as $twitter_object):
			$tweet_counter++;
			if($tweet_counter <= $tweet_count):
				if($tweet_counter == 1):
					$result .= "\n\t<div class = 'rotatingtweet'>";
				else:
					$result .= "\n\t<div class = 'rotatingtweet' style='display:none'>";				
				endif;
				$main_text = $twitter_object->text;
				if(!empty($main_text)):
					$user = $twitter_object->user;
					# Now the substitutions
					$entities = $twitter_object->entities;
					# Fix up retweets, links, hashtags and use names
					unset($before);
					unset($after);
					# First clean up the retweets
					$rt_data = $twitter_object->retweeted_status;
					if(!empty($rt_data)):
						$rt_user = $rt_data->user;
						$main_text = "RT @".$rt_user->screen_name . " " . $rt_data->text;
						$before[] = "*@".$rt_user->screen_name."*i";
						$after[] = "<a href='http://twitter.com/".$rt_user->screen_name."' title='".$rt_user->name."'>@".$rt_user->screen_name."</a>";
						$entities = $rt_data->entities;
					endif;
					# First the user mentions
					$user_mentions = $entities->user_mentions;
					if(!empty($user_mentions)):
						foreach($user_mentions as $user_mention):
							$before[] = "*@".$user_mention->screen_name."*i";
							$after[] = "<a href='http://twitter.com/".$user_mention->screen_name."' title='".$user_mention->name."'>@".$user_mention->screen_name."</a>";
						endforeach;
						# Clearing up duplicates to avoid strange result (possibly risky?)
						$before = array_unique($before);
						$after = array_unique($after);
					endif;
					# Now the URLs
					$urls = $entities->urls;
					if(!empty($urls)):
						foreach($urls as $url):
							$before[] = "*".$url->url."*";
							$displayurl = $url->display_url;
							if(strlen($displayurl)>29) $displayurl = substr($displayurl,0,29)."&hellip;";
							$after[] = "<a href='".$url->url."' title='".$url->expanded_url."'>".esc_html($displayurl)."</a>";
						endforeach;
					endif;
					$media = $entities->media;
					if(!empty($media)):
						foreach($media as $medium):
							$before[] = "*".$medium->url."*";
							$displayurl = $medium->display_url;
							if(strlen($displayurl)>30) $displayurl = substr($displayurl,0,30)."&hellip;";
							$after[] = "<a href='".$medium->url."' title='".$medium->expanded_url."'>".esc_html($displayurl)."</a>";
						endforeach;			
					endif;
					$before[]="%#(\w+)%";
					$after[]='<a href="http://search.twitter.com/search?q=%23$1" title="#$1">#$1</a>';
					$main_text = preg_replace($before,$after,$main_text);
					$result .= "\n\t\t<p class='rtw_main'>$main_text</p>";
					unset($meta);
					if($args['show_meta_timestamp']):
						$meta .= "<a href='http://twitter.com/".$user->screen_name."/status/".$twitter_object->id_str."'>".ucfirst(rotatingtweets_contextualtime(strtotime($twitter_object->created_at)))."</a>";
					endif;
					if($args['show_meta_screen_name']):
						if(!empty($meta)) $meta .= ' ';
						$meta .="from <a target='_BLANK' href='http://twitter.com/".$user->screen_name."' title=\"".$user->name."\">".$user->name."'s Twitter</a>";
					endif;
					if($args['show_meta_via']):
						if(!empty($meta)) $meta .= ' ';
						$meta .="via ".$twitter_object->source;
					endif;
					if($args['show_meta_reply_retweet_favorite']):
						/* Code for $meta showing graphics
						$meta .= ' <a href="http://twitter.com/intent/tweet?in_reply_to='.$twitter_object->id_str.'" title="Reply"><img src="'.plugins_url('images/reply.png', __FILE__).'" width="16" height="16" alt="Reply" /></a> <a href="http://twitter.com/intent/retweet?tweet_id='.$twitter_object->id_str.'" title="Retweet" ><img src="'.plugins_url('images/retweet.png', __FILE__).'" width="16" height="16" alt="Retweet" /></a> <a href="http://twitter.com/intent/favorite?tweet_id='.$twitter_object->id_str.'" title="Favourite"><img src="'.plugins_url('images/favorite.png', __FILE__).'" alt="Favorite" width="16" height="16"  /></a></p>';	
						*/
						if(!empty($meta)) $meta .= ' &middot; ';
						$meta .= '<a href="http://twitter.com/intent/tweet?in_reply_to='.$twitter_object->id_str.'">reply</a> &middot; <a href="http://twitter.com/intent/retweet?tweet_id='.$twitter_object->id_str.'">retweet</a> &middot; <a href="http://twitter.com/intent/favorite?tweet_id='.$twitter_object->id_str.'">favorite</a>';
					endif;
					/* Experiment with Prev / Next buttons 
					if($args['show_meta_prev_next']):
						if(!empty($meta)) $meta .= ' &middot; ';
						$meta .= '<a href="#" class="rtw_prev">prev</a> &middot; <a href="#" class="rtw_next">next</a>';					
					endif;
					*/
					if(!empty($meta)):
						$result .= "\n\t\t<p class='rtw_meta'>".ucfirst($meta)."</p>";
					endif;
				else:
					$result .= "\n\t\t<p class='rtw_main'>Problem retrieving data from Twitter.</p></div>";
					$result .= "<!-- rotatingtweets plugin was unable to parse this data: ".print_r($json,TRUE)." -->";
					$result .= "\n\t\t<div class = 'rotatingtweet' style='display:none'><p class='rtw_main'>Please check the comments on this page's HTML to understand more.</p>";
				endif;
				$result .= "\n\t</div>";
			endif;
		endforeach;
	endif;
	$result .= "\n</div>";
/*
	if($args['show_meta_progress_blobs']):
		$result .= "<div id='".$id."_nav' class='rtw_nav'>";
		for ($i = 1; $i <= $tweet_count; $i++) {
			$result .= '<a href="#">&bull;</a> ';
		}
		$result .= "</div>";
	endif;
*/
	if($args['show_follow'] && !empty($user->screen_name)):
		unset($shortenvariables);
		if($args['no_show_count']) $shortenvariables = ' data-show-count="false"';
		if($args['no_show_screen_name']) $shortenvariables .= ' data-show-screen-name="false"';
		$result .= "\n<div class='follow-button'><a href='http://twitter.com/".$user->screen_name."' class='twitter-follow-button'{$shortenvariables} title='Follow @".$user->screen_name."'>Follow @".$user->screen_name."</a></div>";
	endif;
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-cycle', plugins_url('js/jquery.cycle.all.js', __FILE__),array('jquery'),FALSE,FALSE );
	wp_enqueue_script( 'rotating_tweet', plugins_url('js/rotating_tweet.js', __FILE__),array('jquery','jquery-cycle'),FALSE,FALSE );
	wp_enqueue_style( 'rotating_tweet', plugins_url('css/style.css', __FILE__));
	if($print) echo $result;
	return($result);
}
?>