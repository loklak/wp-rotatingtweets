<?php
/*
Plugin Name: Rotating Tweets (Twitter widget & shortcode)
Description: Replaces a shortcode such as [rotatingtweets screen_name='your_twitter_name'], or a widget, with a rotating tweets display 
Version: 1.3.10
Text Domain: rotatingtweets
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
 * Replaces a shortcode such as [rotatingtweets screen_name='your_twitter_name'], or a widget, with a rotating tweets display 
 *
 * @package WordPress
 * @since 3.3.2
 *
 */
require_once('lib/wp_twitteroauth.php');
/**
 * rotatingtweets_Widget_Class
 * Shows tweets sequentially for a given user
 */
class rotatingtweets_Widget extends WP_Widget {
    /** constructor */
    function rotatingtweets_Widget() {
        parent::WP_Widget(false, $name = 'Rotating Tweets',array('description'=>__('A widget to show tweets for a particular user in rotation.','rotatingtweets')));	
		if ( is_active_widget( false, false, $this->id_base ) )
			rotatingtweets_enqueue_scripts(); 
		}

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
		extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		$positive_variables = array('screen_name','include_rts','exclude_replies','links_in_new_window','tweet_count','show_follow','timeout','rotation_type','show_meta_reply_retweet_favorite','official_format');
		foreach($positive_variables as $var) {
			$newargs[$var] = @$instance['tw_'.$var];
		}
		$negative_variables = array('meta_timestamp','meta_screen_name','meta_via');
		foreach($negative_variables as $var) {
			$newargs['show_'.$var] = !$instance['tw_hide_'.$var];
		}
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
		$instance['tw_links_in_new_window'] = absint($new_instance['tw_links_in_new_window']);
		$instance['tw_exclude_replies'] = absint($new_instance['tw_exclude_replies']);
		$instance['tw_tweet_count'] = max(1,intval($new_instance['tw_tweet_count']));
		$instance['tw_show_follow'] = absint($new_instance['tw_show_follow']);
		# Complicated way to ensure the defaults remain as they were before the 0.500 upgrade - i.e. showing meta timestamp, screen name and via, but not reply, retweet, favorite
		$instance['tw_hide_meta_timestamp'] = !$new_instance['tw_show_meta_timestamp'];
		$instance['tw_hide_meta_screen_name'] = !$new_instance['tw_show_meta_screen_name'];
		$instance['tw_hide_meta_via'] = !$new_instance['tw_show_meta_via'];
		$instance['tw_official_format'] = absint($new_instance['tw_official_format']);
		$instance['tw_show_meta_reply_retweet_favorite'] = absint($new_instance['tw_show_meta_reply_retweet_favorite']);
		$instance['tw_timeout'] = max(min(intval($new_instance['tw_timeout']/1000)*1000,20000),3000);
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
		$tw_official_format = absint($instance['tw_official_format']);
		$tw_links_in_new_window = absint($instance['tw_links_in_new_window']);
		$metaoption['tw_show_meta_timestamp'] = !$instance['tw_hide_meta_timestamp'];
		$metaoption['tw_show_meta_screen_name'] = !$instance['tw_hide_meta_screen_name'];
		$metaoption['tw_show_meta_via'] = !$instance['tw_hide_meta_via'];
		$metaoption['tw_show_meta_reply_retweet_favorite'] = absint($instance['tw_show_meta_reply_retweet_favorite']);
		$tw_timeout = intval($instance['tw_timeout']);
# If values not set, set default values
		if(empty($tw_timeout)) $tw_timeout = 4000;
		if(empty($tw_tweet_count)) $tw_tweet_count = 5;
        ?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','rotatingtweets'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('tw_screen_name'); ?>"><?php _e('Twitter name:','rotatingtweets'); ?> <input class="widefat" id="<?php echo $this->get_field_id('tw_screen_name'); ?>" name="<?php echo $this->get_field_name('tw_screen_name'); ?>"  value="<?php echo $tw_screen_name; ?>" /></label></p>
		<p><input id="<?php echo $this->get_field_id('tw_include_rts'); ?>" name="<?php echo $this->get_field_name('tw_include_rts'); ?>" type="checkbox" value="1" <?php if($tw_include_rts==1): ?>checked="checked" <?php endif; ?>/><label for="<?php echo $this->get_field_id('tw_include_rts'); ?>"> <?php _e('Include retweets?','rotatingtweets'); ?></label></p>
		<p><input id="<?php echo $this->get_field_id('tw_exclude_replies'); ?>" name="<?php echo $this->get_field_name('tw_exclude_replies'); ?>" type="checkbox" value="1" <?php if($tw_exclude_replies==1): ?>checked="checked" <?php endif; ?>/><label for="<?php echo $this->get_field_id('tw_exclude_replies'); ?>"> <?php _e('Exclude replies?','rotatingtweets'); ?></label></p>
		<p><input id="<?php echo $this->get_field_id('tw_links_in_new_window'); ?>" name="<?php echo $this->get_field_name('tw_links_in_new_window'); ?>" type="checkbox" value="1" <?php if($tw_links_in_new_window==1): ?>checked="checked" <?php endif; ?>/><label for="<?php echo $this->get_field_id('tw_links_in_new_window'); ?>"> <?php _e('Open all links in new window or tab?','rotatingtweets'); ?></label></p>
		<p><label for="<?php echo $this->get_field_id('tw_tweet_count'); ?>"><?php _e('How many tweets?','rotatingtweets'); ?> <select id="<?php echo $this->get_field_id('tw_tweet_count'); ?>" name="<?php echo $this->get_field_name('tw_tweet_count');?>">
		<?php 
		for ($i=1; $i<31; $i++) {
			echo "\n\t<option value='$i' ";
		if($tw_tweet_count==$i): ?>selected="selected" <?php endif; 
			echo ">$i</option>";
		}			
		?></select></label></p>
		<p><label for="<?php echo $this->get_field_id('tw_timeout'); ?>"><?php _e('Speed','rotatingtweets'); ?> <select id="<?php echo $this->get_field_id('tw_timeout'); ?>" name="<?php echo $this->get_field_name('tw_timeout');?>">
		<?php 
		$timeoutoptions = array (
							"3000" => __("Faster (3 seconds)",'rotatingtweets'),
							"4000" => __("Normal (4 seconds)",'rotatingtweets'),
							"5000" => __("Slower (5 seconds)",'rotatingtweets'),
							"6000" => __("Slowest (6 seconds)",'rotatingtweets'),
							"20000" => __("Ultra slow (20 seconds)",'rotatingtweets'),
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
			'scrollUp' => __('Scroll Up','rotatingtweets'),
			'scrollDown' => __('Scroll Down','rotatingtweets'),
			'scrollLeft' => __('Scroll Left','rotatingtweets'),
			'scrollRight' => __('Scroll Right','rotatingtweets'),
			'fade' => __('Fade','rotatingtweets')
		);
		asort($rotationoptions);
		?>
		<p><label for="<?php echo $this->get_field_id('tw_rotation_type'); ?>"><?php _e('Type of rotation','rotatingtweets'); ?> <select id="<?php echo $this->get_field_id('tw_rotation_type'); ?>" name="<?php echo $this->get_field_name('tw_rotation_type');?>">
		<?php 		
		foreach ($rotationoptions as $val => $words) {
			echo "\n\t<option value='$val' ";
		if($tw_rotation_type==$val): ?>selected="selected" <?php endif; 
			echo ">$words</option>";
		}			
		?></select></label></p>
		<?php /* Ask about which Tweet details to show */ ?>
		<p><?php _e('Display format','rotatingtweets'); ?></p>
<?php
		$officialoptions = array (
			0 => __('Original rotating tweets layout','rotatingtweets'),
			1 => __("<a target='_blank' href='https://dev.twitter.com/terms/display-guidelines'>Official Twitter guidelines</a> (regular)",'rotatingtweets'),
			2 => __("<a target='_blank' href='https://dev.twitter.com/terms/display-guidelines'>Official Twitter guidelines</a> (wide)",'rotatingtweets'),
		);
		foreach ($officialoptions as $val => $html) {
			echo "<input type='radio' value='$val' id='".$this->get_field_id('tw_official_format_'.$val)."' name= '".$this->get_field_name('tw_official_format')."'";
			if($tw_official_format==$val): ?> checked="checked" <?php endif; 
			echo " class='rtw_ad_official'><label for='".$this->get_field_id('tw_official_format_'.$val)."'> $html</label><br />";
		};
		unset($hideStr);
		if($tw_official_format > 0) $hideStr = ' style = "display:none;" ';
		?>
		<p /><div class='rtw_ad_tw_det' <?=$hideStr;?>><p><?php _e('Show tweet details?','rotatingtweets'); ?></p><p>
		<?php
		$tweet_detail_options = array(
			'tw_show_meta_timestamp' => __('Time/date of tweet','rotatingtweets'),
			'tw_show_meta_screen_name' => __('Name of person tweeting','rotatingtweets'),
			'tw_show_meta_via' => __('Source of tweet','rotatingtweets'),
			'tw_show_meta_reply_retweet_favorite' => __("'reply &middot; retweet &middot; favorite' links",'rotatingtweets')
		);
		$tw_br='';
		foreach ($tweet_detail_options as $field => $text):
		echo $tw_br;
		?>
		<input id="<?php echo $this->get_field_id($field); ?>" name="<?php echo $this->get_field_name($field); ?>" type="checkbox" value="1" <?php if($metaoption[$field]==1): ?>checked="checked" <?php endif; ?>/><label for="<?php echo $this->get_field_id($field); ?>"> <?php echo $text; ?></label>
		<?php 
		$tw_br = "<br />";
		endforeach; ?></p></div>
		<p><?php _e('Show follow button?','rotatingtweets'); ?></p>
<?php
		$showfollowoptions = array (
			0 => _x('None','Show follow button?','rotatingtweets'),
			1 => __("Show name and number of followers",'rotatingtweets'),
			2 => __("Show name only",'rotatingtweets'),
			3 => __("Show followers only",'rotatingtweets'),
			4 => __("Show button only",'rotatingtweets')
		);

		foreach ($showfollowoptions as $val => $html) {
			echo "<input type='radio' value='$val' id='".$this->get_field_id('tw_tweet_count_'.$val)."' name= '".$this->get_field_name('tw_show_follow')."'";
			if($tw_show_follow==$val): ?> checked="checked" <?php endif; 
			echo "><label for='".$this->get_field_id('tw_tweet_count_'.$val)."'> $html</label><br />";
		}
		# This is an appalling hack to deal with the problem that jQuery gets broken when people hit save - as per http://lists.automattic.com/pipermail/wp-hackers/2011-March/037997.html - but it works!
//		echo "<script type='text/javascript' src='".plugins_url('js/rotating_tweet_admin.js', __FILE__)."'></script>";
		echo "\n<script type='text/javascript'>\n";
		$rtw_admin_script_original = file_get_contents(plugin_dir_path(__FILE__).'js/rotating_tweet_admin.js');
		$rtw_admin_script_final = str_replace('.rtw_ad_official','[name="'.$this->get_field_name('tw_official_format').'"]',$rtw_admin_script_original);
		echo $rtw_admin_script_final;
		echo "\n</script>";
	}
} // class rotatingtweets_Widget

// register rotatingtweets_Widget widget
add_action('widgets_init', create_function('', 'return register_widget("rotatingtweets_Widget");'));

# Converts Tweet timestamp into a time description
function rotatingtweets_contextualtime($small_ts, $large_ts=false) {
  if(!$large_ts) $large_ts = time();
  $n = $large_ts - $small_ts;
  if($n <= 1) return __('less than a second ago','rotatingtweets');
  if($n < (60)) return sprintf(__('%d seconds ago','rotatingtweets'),$n);
  if($n < (60*60)) { $minutes = round($n/60); return sprintf(_n('about a minute ago','about %d minutes ago',$minutes,'rotatingtweets'),$minutes); }
  if($n < (60*60*16)) { $hours = round($n/(60*60)); return sprintf(_n('about an hour ago','about %d hours ago',$hours,'rotatingtweets'),$hours); }
  if($n < (time() - strtotime('yesterday'))) return __('yesterday','rotatingtweets');
  if($n < (60*60*24)) { $hours = round($n/(60*60)); return sprintf(_n('about an hour ago','about %d hours ago',$hours,'rotatingtweets'),$hours); }
  if($n < (60*60*24*6.5)) { $days = round($n/(60*60*24)); return sprintf(_n('about a day ago','about %d days ago',$days,'rotatingtweets'),$days); }
  if($n < (time() - strtotime('last week'))) return __('last week','rotatingtweets');
  if($n < (60*60*24*7*3.5)) { $weeks = round($n/(60*60*24*7)); return sprintf(_n('about a week ago','about %d weeks ago',$weeks,'rotatingtweets'),$weeks); } 
  if($n < (time() - strtotime('last month'))) return __('last month','rotatingtweets');
  if($n < (60*60*24*7*4*11.5)) { $months = round($n/(60*60*24*7*4)) ; return sprintf(_n('about a month ago','about %d months ago',$months,'rotatingtweets'),$months);}
  if($n < (time() - strtotime('last year'))) return __('last year','rotatingtweets');
  if($n >= (60*60*24*7*4*12)){$years=round($n/(60*60*24*7*52)) ;return sprintf(_n('about a year ago','about %d years ago',$years,'rotatingtweets'),$years);}
  return false;
}
# Converts Tweet timestamp into a short time description - as specified by Twitter
function rotatingtweets_contextualtime_short($small_ts, $large_ts=false) {
  if(!$large_ts) $large_ts = time();
  $n = $large_ts - $small_ts;
  if($n < (60)) return sprintf(_x('%ds','abbreviated timestamp in seconds','rotatingtweets'),$n);
  if($n < (60*60)) { $minutes = round($n/60); return sprintf(_x('%dm','abbreviated timestamp in minutes','rotatingtweets'),$minutes); }
  if($n < (60*60*24)) { $hours = round($n/(60*60)); return sprintf(_x('%dh','abbreviated timestamp in hours','rotatingtweets'),$hours); }
  if($n < (60*60*24*364)) return date(_x('j M','short date format as per http://uk.php.net/manual/en/function.date.php','rotatingtweets'),$small_ts);
  return date(_x('j M Y','slightly longer date format as per http://uk.php.net/manual/en/function.date.php','rotatingtweets'),$small_ts);
}
# Get reply,retweet,favorite intents - either words-only (option 0) or icons only (option 1) or both (option 2)
function rotatingtweets_intents($twitter_object,$lang, $icons = 1,$targetvalue='') {
	$addstring = array();
	$types = array (
		array ( 'link'=>'https://twitter.com/intent/tweet?in_reply_to=', 'icon'=>'images/reply.png', 'text' => __('reply', 'rotatingtweets')),
		array ( 'link'=>'https://twitter.com/intent/retweet?tweet_id=', 'icon'=>'images/retweet.png', 'text' => __('retweet', 'rotatingtweets')),
		array ( 'link'=>'https://twitter.com/intent/favorite?tweet_id=', 'icon'=>'images/favorite.png', 'text' => __('favorite', 'rotatingtweets'))
	);
	foreach($types as $type) {
		$string = "\n\t\t\t<a href='".$type['link'].$twitter_object['id_str']."' title='".esc_attr($type['text'])."' lang='{$lang}'{$targetvalue}>";
		switch($icons) {
		case 2:
			$addstring[] = $string."<img src='".plugins_url($type['icon'],__FILE__)."' width='16' height='16' alt='".esc_attr($type['text'])."' /> {$type['text']}</a>";
			$glue = ' ';		
			break;
		case 1:
			$addstring[] = $string."<img src='".plugins_url($type['icon'],__FILE__)."' width='16' height='16' alt='".esc_attr($type['text'])."' /></a>";
			$glue = '';
			break;
		case 0:
		default:
			$addstring[] = $string.$type['text'].'</a>';
			$glue = ' &middot; ';
			break;
		}
	}
	$string = implode($glue,$addstring);
	return($string);
}
// Produces a link to someone's name, icon or screen name (or to the text of your choice) using the 'intent' format for linking
function rotatingtweets_user_intent($person,$lang,$linkcontent,$targetvalue='') {
	$return = "<a href='https://twitter.com/intent/user?user_id={$person['id']}' title='".esc_attr($person['name'])."' lang='{$lang}'{$targetvalue}>";
	switch($linkcontent){
	case 'icon':
		$return .= "<img src='{$person['profile_image_url']}' alt='".esc_attr($person['name'])."' /></a>";
		break;
	case 'name':
		$return .= $person['name']."</a>";
		break;
	case 'screen_name':
		$return .= "@".$person['screen_name']."</a>";
		break;
	default:
		$return .= $linkcontent."</a>";
		break;
	}
	return ($return);
}
// Produces a linked timestamp for including in the tweet
function rotatingtweets_timestamp_link($twitter_object,$timetype = 'default',$targetvalue='') {
	$string = '<a '.$targetvalue.' href="https://twitter.com/twitterapi/status/'.$twitter_object['id_str'].'">';
	$tweettimestamp = strtotime($twitter_object['created_at'] );
	// echo "<!-- ".$twitter_object['created_at'] . " | " .get_option('timezone_string') ." | $tweettimestamp -->";
	switch($timetype) {
		case 'short':
			$string .= rotatingtweets_contextualtime_short($tweettimestamp);
			break;
		case 'long':
			$string .= date_i18n(get_option('time_format'),$tweettimestamp + ( get_option('gmt_offset') * 60 * 60 ) )." &middot; ".date_i18n(get_option('date_format') ,$tweettimestamp + ( get_option('gmt_offset') * 60 * 60 ) );
			break;
		default:
			$string .= ucfirst(rotatingtweets_contextualtime($tweettimestamp));
			break;
	}
	$string .= '</a>';
	return ($string);
}
# Wraps the shortcode
function rotatingtweets_display($atts) {
	rotatingtweets_display_shortcode($atts,null,'',TRUE);
};
#
function rotatingtweets_link_to_screenname($link) {
	$match = '%(http://|https://|)(www\.|)twitter\.com/(#!\/|)([0-9a-z\_]+)%i';
	if(preg_match($match,$link,$result)):
		return($result[4]);
	else:
		return FALSE;
	endif;
}
# Processes the shortcode 
function rotatingtweets_display_shortcode( $atts, $content=null, $code="", $print=FALSE ) {
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
			'screen_name' => '',
			'url' => 'http://twitter.com/twitter',
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
			'rotation_type' => 'scrollUp',
			'official_format' => FALSE,
			'links_in_new_window' => FALSE,
			'url_length' => 29,
			'ratelimit' => FALSE
		), $atts ) ;
	extract($args);
	if(empty($screen_name) && !empty($url)):
		$screen_name = rotatingtweets_link_to_screenname($url);
		$args['screen_name'] = $screen_name;
		if(WP_DEBUG) {
			echo "<!-- $url => $screen_name -->";
		}
	endif;
	if(empty($screen_name)) $screen_name = 'twitter';
	# Makes sure the scripts are listed
	rotatingtweets_enqueue_scripts(); 
	$tweets = rotatingtweets_get_tweets($screen_name,$include_rts,$exclude_replies);
	$returnstring = rotating_tweets_display($tweets,$args,$print);
	return $returnstring;
}
add_shortcode( 'rotatingtweets', 'rotatingtweets_display_shortcode' );

/*

Management page for the Twitter API options

*/
function rotatingtweets_settings_check() {
	$api = get_option('rotatingtweets-api-settings');
	$error = get_option('rotatingtweets_api_error');
	if(!empty($api)):
		$apistring = implode('',$api);
	endif;
	$optionslink = 'options-general.php?page=rotatingtweets';
	if(empty($apistring)):
		if(time() > strtotime('1-Apr-2013')):
			$msgString = __('Please update <a href="%2$s">your settings for Rotating Tweets</a>. The Twitter API <a href="%1$s">changed in March 2013</a> and new settings are needed for Rotating Tweets to continue working.','rotatingtweets');
		else:
			$msgString = __('Please update <a href="%2$s">your settings for Rotating Tweets</a>. The Twitter API will <a href="%1$s">be changing in March 2013</a> and new settings are needed for Rotating Tweets to continue working after the API changes.','rotatingtweets');
		endif;
		// add_settings_error( 'rotatingtweets_settings_needed', esc_attr('rotatingtweets_settings_needed'), sprintf($msgString,'https://dev.twitter.com/calendar',$optionslink), 'error');
		echo "<div class='error'><p><strong>".sprintf($msgString,'https://dev.twitter.com/calendar',$optionslink)."</strong></p></div>";
	elseif($error[0]['code'] == 32 ):
		// add_settings_error( 'rotatingtweets_settings_needed', esc_attr('rotatingtweets_settings_needed'), sprintf(__('Please update <a href="%1$s">your settings for Rotating Tweets</a>. Currently Twitter cannot authenticate you with the details you have given.','rotatingtweets'),$optionslink), 'error');
		echo "<div class='error'><p><strong>".sprintf(__('Please update <a href="%1$s">your settings for Rotating Tweets</a>. Currently Rotating Tweets cannot authenticate you with Twitter using the details you have given.','rotatingtweets'),$optionslink)."</strong></p></div>";
	endif;
};
add_action( 'admin_notices', 'rotatingtweets_settings_check' );

add_action( 'admin_menu', 'rotatingtweets_menu' );

function rotatingtweets_menu() {
	add_options_page( __('Rotating Tweets: Twitter API settings','rotatingtweets'), 'Rotating Tweets', 'manage_options', 'rotatingtweets', 'rotatingtweets_call_twitter_API_options' );
}

function rotatingtweets_call_twitter_API_options() {
	echo '<div class="wrap">';
	screen_icon();
	echo '<h2>'.__('Rotating Tweets: Twitter API settings','rotatingtweets').'</h2>';	
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.','rotatingtweets' ) );
	}
	echo sprintf(__('<p>Twitter <a href="%s">recently announced</a> that they will be changing the way that they allow people to use the information in their tweets.</p><p>Please take the following steps to make sure that Rotating Tweets continues working:</p>','rotatingtweets'),'https://dev.twitter.com/blog/changes-coming-to-twitter-api');
	echo sprintf(__('<h3>Step 1:</h3><p>Go to the <a href="%s">My applications page</a> on the Twitter website to set up your website as a new Twitter \'application\'. You may need to log-in using your Twitter user name and password.</p>','rotatingtweets'),'https://dev.twitter.com/apps');
	echo sprintf(__('<h3>Step 2:</h3><p>If you don\'t already have a suitable \'application\' that you can use for your website, set one up on the <a href="%s">Create an Application page</a>.</p> <p>It\'s normally best to use the name, description and website URL of the website where you plan to use Rotating Tweets.</p><p>You don\'t need a Callback URL.</p>','rotatingtweets'),'https://dev.twitter.com/apps/new');
	_e('<h3>Step 3:</h3><p>After clicking <strong>Create your Twitter application</strong>, on the following page, click on <strong>Create my access token</strong>.</p>','rotatingtweets');
	_e('<h3>Step 4:</h3><p>Copy the <strong>Consumer key</strong>, <strong>Consumer secret</strong>, <strong>Access token</strong> and <strong>Access token secret</strong> from your Twitter application page into the settings below.</p>','rotatingtweets');
	_e('<h3>Step 5:</h3><p>Click on <strong>Save Changes</strong>. If there are any problems, you will get an error message from Twitter which should help diagnose the problem.</p>','rotatingtweets');
	_e('<p><em>Even though you are only entering one set of Twitter API data, Rotating Tweets will continue to support multiple widgets and shortcodes pulling from a variety of different Twitter accounts.</em></p>','rotatingtweets');
	echo '<form method="post" action="options.php">';
	settings_fields( 'rotatingtweets_options' );
	do_settings_sections('rotatingtweets_api_settings');
	submit_button(__('Save Changes','rotatingtweets'));
	echo '</form></div>';
}
add_action('admin_init', 'rotatingtweets_admin_init');

function rotatingtweets_admin_init(){
	register_setting( 'rotatingtweets_options', 'rotatingtweets-api-settings', 'rotatingtweets_api_validate' );
	add_settings_section('rotatingtweets_api_main', __('Twitter API Settings','rotatingtweets'), 'rotatingtweets_api_explanation', 'rotatingtweets_api_settings');
	add_settings_field('rotatingtweets_key', __('Twitter API Consumer Key','rotatingtweets'), 'rotatingtweets_option_show_key', 'rotatingtweets_api_settings', 'rotatingtweets_api_main');
	add_settings_field('rotatingtweets_secret', __('Twitter API Consumer Secret','rotatingtweets'), 'rotatingtweets_option_show_secret', 'rotatingtweets_api_settings', 'rotatingtweets_api_main');
	add_settings_field('rotatingtweets_token', __('Twitter API Access Token','rotatingtweets'), 'rotatingtweets_option_show_token', 'rotatingtweets_api_settings', 'rotatingtweets_api_main');
	add_settings_field('rotatingtweets_token_secret', __('Twitter API Access Token Secret','rotatingtweets'), 'rotatingtweets_option_show_token_secret', 'rotatingtweets_api_settings', 'rotatingtweets_api_main');
}
function rotatingtweets_option_show_key() {
	$options = get_option('rotatingtweets-api-settings');
	echo "<input id='rotatingtweets_api_key_input' name='rotatingtweets-api-settings[key]' size='70' type='text' value='{$options['key']}' />";
}
function rotatingtweets_option_show_secret() {
	$options = get_option('rotatingtweets-api-settings');
	echo "<input id='rotatingtweets_api_secret_input' name='rotatingtweets-api-settings[secret]' size='70' type='text' value='{$options['secret']}' />";
}
function rotatingtweets_option_show_token() {
	$options = get_option('rotatingtweets-api-settings');
	echo "<input id='rotatingtweets_api_token_input' name='rotatingtweets-api-settings[token]' size='70' type='text' value='{$options['token']}' />";
}
function rotatingtweets_option_show_token_secret() {
	$options = get_option('rotatingtweets-api-settings');
	echo "<input id='rotatingtweets_api_token_secret_input' name='rotatingtweets-api-settings[token_secret]' size='70' type='text' value='{$options['token_secret']}' />";
}
// Explanatory text
function rotatingtweets_api_explanation() {
	
};
// validate our options
function rotatingtweets_api_validate($input) {
	$options = get_option('rotatingtweets-api-settings');
	$error = 0;
	// Check 'key'
	$options['key'] = trim($input['key']);
	if(!preg_match('/^[a-z0-9]+$/i', $options['key'])) {
		$options['key'] = '';
		$error = 1;
		add_settings_error( 'rotatingtweets', esc_attr('rotatingtweets-api-key'), __('Error: Twitter API Consumer Key not correctly formatted.','rotatingtweets'));
	}
	// Check 'secret'
	$options['secret'] = trim($input['secret']);
	if(!preg_match('/^[a-z0-9]+$/i', $options['secret'])) {
		$options['secret'] = '';
		$error = 1;
		add_settings_error( 'rotatingtweets', esc_attr('rotatingtweets-api-secret'), __('Error: Twitter API Consumer Secret not correctly formatted.','rotatingtweets'));
	}
	// Check 'token'
	$options['token'] = trim($input['token']);
	if(!preg_match('/^[a-z0-9]+\-[a-z0-9]+$/i', $options['token'])) {
		$options['token'] = '';
		$error = 1;
		add_settings_error( 'rotatingtweets', esc_attr('rotatingtweets-api-token'), __('Error: Twitter API Access Token not correctly formatted.','rotatingtweets'));
	}
	// Check 'token_secret'
	$options['token_secret'] = trim($input['token_secret']);
	if(!preg_match('/^[a-z0-9]+$/i', $options['token_secret'])) {
		$options['token_secret'] = '';
		$error = 1;
		add_settings_error( 'rotatingtweets', esc_attr('rotatingtweets-api-token-secret'), __('Error: Twitter API Access Token Secret not correctly formatted.','rotatingtweets'));
	}
	// Now a proper test
	if(empty($error)):
		$test = rotatingtweets_call_twitter_API('statuses/user_timeline',NULL,$options);
		$error = get_option('rotatingtweets_api_error');
		if(!empty($error)):
			add_settings_error( 'rotatingtweets', esc_attr('rotatingtweets-api-'.$error[0]['code']), sprintf(__('Error message received from Twitter: %1$s. <a href="%2$s">Please check your API key, secret, token and secret token on the Twitter website</a>.','rotatingtweets'),$error[0]['message'],'https://dev.twitter.com/apps'), 'error' );
		endif;
	endif;
	return $options;
}
/*
And now the Twitter API itself!
*/

function rotatingtweets_call_twitter_API($command,$options = NULL,$api = NULL ) {
	if(empty($api)) $api = get_option('rotatingtweets-api-settings');
	if(!empty($api)):
		$connection = new wp_TwitterOAuth($api['key'], $api['secret'], $api['token'], $api['token_secret'] );
		//    $result = $connection->get('statuses/user_timeline', $options);
		if(WP_DEBUG):
			echo "\n<!-- Using OAuth - version 1.1 of API -- ".esc_attr($command)." -->\n";
		endif;
		$result = $connection->get($command , $options);
	else:
		// Construct old style API command
		unset($string);
		if($command == 'application/rate_limit_status'):
			$command = 'account/rate_limit_status';
			unset($options);
		endif;
		if(is_array($options)):
			foreach($options as $name => $val) {
				$string[] = $name . "=" . urlencode($val);
			}
		endif;
		$apicall = "http://api.twitter.com/1/".$command.".json";
		if(!empty($string)) $apicall .= "?".implode('&',$string);
		if(WP_DEBUG) echo "<!-- Using version 1 of API - calling string ".esc_attr($apicall)." -->";
		$result = wp_remote_request($apicall);
	endif;
	if(!is_wp_error($result)):
		$data = json_decode($result['body'],true);
		if(!empty($data['errors'])):
			update_option('rotatingtweets_api_error',$data['errors']);
		else:
			delete_option('rotatingtweets_api_error');
		endif;
	else:
		$errorstring = array();
		$errorstring[0]['code']= $result->get_error_code();
		$errorstring[0]['message']= $result->get_error_message();
		update_option('rotatingtweets_api_error',$errorstring);
	endif;
	return($result);
}

# Get the latest data from Twitter (or from a cache if it's been less than 2 minutes since the last load)
function rotatingtweets_get_tweets($tw_screen_name,$tw_include_rts,$tw_exclude_replies) {
	# Clear up variables
	$cache_delay = 120;
	if($tw_include_rts != 1) $tw_include_rts = 0;
	if($tw_exclude_replies != 1) $tw_exclude_replies = 0;
	# Get the option strong
	$stringname = $tw_screen_name.$tw_include_rts.$tw_exclude_replies;
	$optionname = "rotatingtweets-cache";
	$option = get_option($optionname);
	# Attempt to deal with 'Cannot use string offset as an array' error
	$timegap = $cache_delay + 1;
	if(is_array($option)):
		if(WP_DEBUG):
			echo "<!-- var option is an array -->";
		endif;
		if(isset($option[$stringname])):
			if(WP_DEBUG) echo "<!-- option[$stringname] exists -->";
			if(is_array($option[$stringname]['json'][0])):
				if(WP_DEBUG) echo "<!-- option[$stringname]['json'][0] is an array -->";
				$latest_json = $option[$stringname]['json'];
				$latest_json_date = $option[$stringname]['datetime'];
				$timegap = time()-$latest_json_date;
			elseif(is_object($option[$stringname]['json'][0])):
				if(WP_DEBUG) echo "<!-- option[$stringname]['json'][0] is an object -->";
				unset($option[$stringname]);
			else:
				if(WP_DEBUG) echo "<!-- option[$stringname]['json'][0] is neither an object nor an array! -->";
				unset($option[$stringname]);
			endif;
		endif;
	else:
		unset($option);
	endif;
	# Checks if it is time to call Twitter directly yet or if it should use the cache
	if($timegap > $cache_delay):
		$apioptions = array('screen_name'=>$tw_screen_name,'include_entities'=>1,'count'=>70,'include_rts'=>$tw_include_rts,'exclude_replies'=>$tw_exclude_replies);
		$twitterdata = rotatingtweets_call_twitter_API('statuses/user_timeline',$apioptions);
		if(!is_wp_error($twitterdata)):
			$twitterjson = json_decode($twitterdata['body'],TRUE);
			if(WP_DEBUG):
				echo "<!-- Rotating Tweets - got new data -->";
			endif;
		else:
			set_transient('rotatingtweets_wp_error',$twitterdata->get_error_messages(), 120);
		endif;
	elseif(WP_DEBUG):
		echo "<!-- Rotating Tweets - used cache -->";
	endif;
	# Checks for errors in the reply
	if(!empty($twitterjson['errors'])):
		# If there's an error, reset the cache timer to make sure we don't hit Twitter too hard and get rate limited.
//		print_r($twitterjson);
		$option[$stringname]['datetime']=time();
		update_option($optionname,$option);
	elseif(!empty($twitterjson['error'])):
		# If Twitter is being rate limited, delays the next load until the reset time
		# For some reason the rate limiting error has a different error variable!
		$rate = rotatingtweets_get_rate_data();
		if($rate && $rate['remaining_hits'] == 0):
			$option[$stringname]['datetime']= $rate['reset_time_in_seconds'] - $cache_delay + 1;
			update_option($optionname,$option);
		endif;
	elseif(!empty($twitterjson)):
		# If there's regular data, then update the cache and return the data
		unset($firstentry);
		if(is_array($twitterjson)) $firstentry = $twitterjson[0];
		if(!empty($firstentry['text'])):
			if(WP_DEBUG):
				echo "<!-- Storing cache entry for $stringname in $optionname -->";
			endif;
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
//	$callstring = "http://api.twitter.com/1/account/rate_limit_status.json";
//	$command = 'account/rate_limit_status';
	$ratedata = rotatingtweets_call_twitter_API('application/rate_limit_status',array('resources'=>'statuses'));
//	$ratedata = wp_remote_request($callstring);
	if(!is_wp_error($ratedata)):
		$rate = json_decode($ratedata['body'],TRUE);
		if($rate['resources']['statuses']['/statuses/user_timeline']['limit']>0):
			$newrate['hourly_limit']=$rate['resources']['statuses']['/statuses/user_timeline']['limit'];
			$newrate['remaining_hits']=$rate['resources']['statuses']['/statuses/user_timeline']['remaining'];
			$newrate['reset_time_in_seconds']=$rate['resources']['statuses']['/statuses/user_timeline']['reset'];
			return($newrate);
		else:
			return($rate);
		endif;
	else:
		set_transient('rotatingtweets_wp_error',$ratedata->get_error_messages(), 120);
		return(FALSE);
	endif;
}

# Gets the language options
# Once a day finds out what language options Twitter has.  If there's any issue, pushes back the next attempt by another day.
function rotatingtweets_get_twitter_language() {
	$cache_delay = 60*60*24;
	$fallback = array ('id','da','ru','msa','ja','no','ur','nl','fa','hi','de','ko','sv','tr','fr','it','en','fil','pt','he','zh-tw','fi','pl','ar','es','hu','th','zh-cn');
	$optionname = 'rotatingtweets-twitter-languages';
	$option = get_option($optionname);
	# Attempt to deal with 'Cannot use string offset as an array' error
	if(is_array($option)):
		$latest_languages = $option['languages'];
		$latest_date = $option['datetime'];
		$timegap = time()-$latest_date;
	else:
		$latest_languages = $fallback;
		$timegap = $cache_delay + 1;
		$option['languages'] = $fallback;
		$option['datetime'] = time();
	endif;
	if($timegap > $cache_delay):
//		$callstring = "https://api.twitter.com/1/help/languages.json";
//		$twitterdata = wp_remote_request($callstring);
		$twitterdata = rotatingtweets_call_twitter_API('help/languages');
		if(!is_wp_error($twitterdata)):
			$twitterjson = json_decode($twitterdata['body'],TRUE);
			if(!empty($twitterjson['errors'])||!empty($twitterjson['error'])):
				# If there's an error, reset the cache timer to make sure we don't hit Twitter too hard and get rate limited.
				$option['datetime']=time();
				update_option($optionname,$option);
			else:
				# If there's regular data, then update the cache and return the data
				$latest_languages = array();
				if(is_array($twitterjson)):
					foreach($twitterjson as $langarray):
						$latest_languages[] = $langarray['code'];
					endforeach;
				endif;
				if(!empty($latest_languages)):
					$option['languages']=$latest_languages;
					$option['datetime']=time();
					update_option($optionname,$option);
				endif;
			endif;
		else:
			$option['datetime']=time();
			update_option($optionname,$option);
		endif;
	endif;
	if(empty($latest_languages)) $latest_languages = $fallback;
	return($latest_languages);
}

# This function is used for debugging what happens when the site is rate-limited - best not used otherwise!
function rotatingtweets_trigger_rate_limiting() {
//	$callstring = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=twitter";
	$apidata = array('screen_name'=>'twitter');
	for ($i=1; $i<150; $i++) {
//		$ratedata = wp_remote_request($callstring);
		$ratedata = rotatingtweets_call_twitter_API('statuses/user_timeline',$apidata);
	}
}

# Displays the tweets
function rotating_tweets_display($json,$args,$print=TRUE) {
	unset($result);
	$tweet_count = max(1,intval($args['tweet_count']));
	$timeout = max(intval($args['timeout']),0);
	$defaulturllength = 29;
	if(isset($args['url_length'])):
		$urllength = intval($args['url_length']);
		if($urllength < 1):
			$urllength = $defaulturllength;
		endif;
	else: 
		$urllength = $defaulturllength;
	endif;
	# Check that the rotation type is valid. If not, leave it as 'scrollUp'
	$rotation_type = 'scrollUp';
	# Get Twitter language string
	$rtlocale = strtolower(get_locale());
	$rtlocaleMain = explode('_',$rtlocale);
	$possibleOptions = rotatingtweets_get_twitter_language();
	if(in_array($rtlocale,$possibleOptions)):
		$twitterlocale = $rtlocale;
	elseif(in_array($rtlocaleMain[0],$possibleOptions)):
		$twitterlocale = $rtlocaleMain[0];
	else:
		# Default
		$twitterlocale = 'en';
	endif;
	# All the valid rotations - if people to use one that looks weird, that's their business!
	$possibleRotations = array('blindX','blindY','blindZ','cover','curtainX','curtainY','fade','fadeZoom','growX','growY','none','scrollUp','scrollDown','scrollLeft','scrollRight','scrollHorz','scrollVert','shuffle','slideX','slideY','toss','turnUp','turnDown','turnLeft','turnRight','uncover','wipe','zoom');
	foreach($possibleRotations as $possibleRotation):
		if(strtolower($args['rotation_type']) == strtolower($possibleRotation)) $rotation_type = $possibleRotation;
	endforeach;
	# Create an ID that has all the relevant info in - rotation type and speed of rotation
	$id = uniqid('rotatingtweets_'.$timeout.'_'.$rotation_type.'_');
	if(WP_DEBUG):
		$result = "\n<div class='rotatingtweets wp_debug' id='$id'>";
		
	else:
		$result = "\n<div class='rotatingtweets' id='$id'>";
	endif;
	$error = get_option('rotatingtweets_api_error');
	if(!empty($error)):
		$result .= "\n<!-- Error: ".esc_attr($error[0]['code'])." - ".esc_attr($error[0]['message'])." -->";
	endif;
	if(empty($json)):
		$result .= "\n\t<div class = 'rotatingtweet'><p class='rtw_main'>". __('Problem retrieving data from Twitter','rotatingtweets'). "</p></div>";
		$rate = rotatingtweets_get_rate_data();
		# Check if the problem is rate limiting
		if($rate['hourly_limit']>0 && $rate['remaining_hits'] == 0):
			$result .= "\n\t<div class = 'rotatingtweet' style='display:none'><p class='rtw_main'>". sprintf(__('This website is currently <a href=\'%s\'>rate-limited by Twitter</a>.','rotatingtweets'),'https://dev.twitter.com/docs/rate-limiting-faq') . "</p></div>";
			$waittimevalue = intval(($rate['reset_time_in_seconds'] - time())/60);
			$waittime = sprintf(_n('Next attempt to get data will be in %d minute','Next attempt to get data will be in %d minutes',$waittimevalue,'rotatingtweets'),$waittimevalue);
			if($waittimevalue == 0) $waittime = __("Next attempt to get data will be in less than a minute",'rotatingtweets');
			$result .= "\n\t<div class = 'rotatingtweet' style='display:none'><p class='rtw_main'>{$waittime}.</p></div>";
		else:
			$error_messages = get_transient('rotatingtweets_wp_error');
			if($error_messages):
				foreach($error_messages as $error_message):
					$result .= "\n\t<div class = 'rotatingtweet' style='display:none'><p class='rtw_main'>". __('Wordpress error message','rotatingtweets').": ".$error_message.".</p></div>";
				endforeach;
			endif;
			$result .= "\n\t<div class = 'rotatingtweet' style='display:none'><p class='rtw_main'>". sprintf(__('Please check the Rotating Tweets settings or the <a href=\'%s\'>Twitter API status</a>.','rotatingtweets'),'https://dev.twitter.com/status')."</p></div>";
		endif;
	else:
		$tweet_counter = 0;
		/*
		$rate = rotatingtweets_get_rate_data();
		# Check if the problem is rate limiting
		if($rate['hourly_limit']>0 && $rate['remaining_hits'] == 0):
			$waittimevalue = intval(($rate['reset_time_in_seconds'] - time())/60);
			$result .= "<!-- Rate limited -- ";
			$result .= sprintf(_n('Next attempt to get data will be in %d minute','Next attempt to get data will be in %d minutes',$waittimevalue,'rotatingtweets'),$waittimevalue)." -->";
		endif;
		*/
		# Set up the link treatment
		if(isset($args['links_in_new_window']) && !empty($args['links_in_new_window']) ) {
			$targetvalue = ' target="_blank" ';
		} else {
			$targetvalue = '';
		}
		foreach($json as $twitter_object):
			$tweet_counter++;
			if($tweet_counter <= $tweet_count):
				if($tweet_counter == 1 ):
					$result .= "\n\t<div class = 'rotatingtweet'>";
				else:
					$result .= "\n\t<div class = 'rotatingtweet' style='display:none'>";				
				endif;
				# Now to process the text
				// print_r($twitter_object);
				$main_text = $twitter_object['text'];
				if(!empty($main_text)):
					$user = $twitter_object['user'];
					$tweetuser = $user;
					# Now the substitutions
					$entities = $twitter_object['entities'];
					# Fix up retweets, links, hashtags and use names
					unset($before);
					unset($after);
					unset($retweeter);
					# First clean up the retweets
					if(isset($twitter_object['retweeted_status'])):
						$rt_data = $twitter_object['retweeted_status'];
					else:
						unset($rt_data);
					endif;
					if(!empty($rt_data)):
						$rt_user = $rt_data['user'];
						if($args['official_format']):
							$main_text = $rt_data['text'];
							$retweeter = $user;
							$tweetuser = $rt_user;
						else:
							$main_text = "RT @".$rt_user['screen_name'] . " " . $rt_data['text'];
						endif;
						$before[] = "*@".$rt_user['screen_name']."\b*i";
						$after[] = rotatingtweets_user_intent($rt_user,$twitterlocale,'screen_name',$targetvalue);
						$entities = $rt_data['entities'];
					endif;
					# First the user mentions
					if(isset($entities['user_mentions'])):
						$user_mentions = $entities['user_mentions'];
					else:
						unset($user_mentions);
					endif;
					if(!empty($user_mentions)):
						foreach($user_mentions as $user_mention):
							$before[] = "*@".$user_mention['screen_name']."\b*i";
							$after[] = rotatingtweets_user_intent($user_mention,$twitterlocale,'screen_name',$targetvalue);
						endforeach;
						# Clearing up duplicates to avoid strange result (possibly risky?)
						$before = array_unique($before);
						$after = array_unique($after);
					endif;
					# Now the URLs
					if(isset($entities['urls'])):
						$urls = $entities['urls'];
					else:
						unset($urls);
					endif;
					if(!empty($urls)):
						foreach($urls as $url):
							$before[] = "*".$url['url']."*";
							$displayurl = $url['display_url'];
							if(strlen($displayurl)>$urllength):
								# PHP sometimes has a really hard time with unicode characters - this one removes the ellipsis
								$displayurl = str_replace(json_decode('"\u2026"'),"",$displayurl);
								$displayurl = substr($displayurl,0,$urllength)."&hellip;";
							endif;
							$after[] = "<a href='".$url['url']."' title='".$url['expanded_url']."'".$targetvalue.">".esc_html($displayurl)."</a>";
						endforeach;
					endif;
					if(isset($entities['media'])):
						$media = $entities['media'];
					else:
						unset($media);
					endif;
					if(!empty($media)):
						foreach($media as $medium):
							$before[] = "*".$medium['url']."*";
							$displayurl = $medium['display_url'];
							if(strlen($displayurl)>$urllength):
								$displayurl = str_replace(json_decode('"\u2026"'),"",$displayurl);
								$displayurl = substr($displayurl,0,$urllength)."&hellip;";
							endif;
							$after[] = "<a href='".$medium['url']."' title='".$medium['expanded_url']."'".$targetvalue.">".esc_html($displayurl)."</a>";
						endforeach;			
					endif;
					$before[]="%#([0-9]*[a-zA-Z]+\w*)%";
					$after[]='<a href="http://twitter.com/search?q=%23$1&src=hash" title="#$1"'.$targetvalue.'>#$1</a>';
					$main_text = preg_replace($before,$after,$main_text);

					# Now for the meta text
					switch ($args['official_format']) {
					case 0:
						# This is the original Rotating Tweets display routine
						$result .= "\n\t\t<p class='rtw_main'>$main_text</p>";
						$meta = '';
						if($args['show_meta_timestamp']):
							$meta .= rotatingtweets_timestamp_link($twitter_object,'default',$targetvalue);
						endif;
						if($args['show_meta_screen_name']):
							if(!empty($meta)) $meta .= ' ';
							$meta .= sprintf(__('from <a href=\'%1$s\' title=\'%2$s\'>%2$s\'s Twitter</a>','rotatingtweets'),'https://twitter.com/intent/user?user_id='.$user['id'],$user['name']);
						endif;
						if($args['show_meta_via']):
							if(!empty($meta)) $meta .= ' ';
							$meta .=sprintf(__("via %s",'rotatingtweets'),$twitter_object['source']);
						endif;
						if($args['show_meta_reply_retweet_favorite']):
							if(!empty($meta)) $meta .= ' &middot; ';
							$meta .= rotatingtweets_intents($twitter_object,$twitterlocale, 0,$targetvalue);
						endif;
						if(!empty($meta)) $result .= "\n\t\t<p class='rtw_meta'>".ucfirst($meta)."</p>";
						break;
					case 1:
						# This is an attempt to replicate the original Tweet
						$result .= "\n\t<div class='rtw_info'>";
						$result .= "\n\t\t<div class='rtw_twitter_icon'><img src='".plugins_url('images/bird_16_blue.png', __FILE__)."' alt='".__('Twitter','rotatingtweets')."' /></div>";
						$result .= "\n\t\t<div class='rtw_icon'>".rotatingtweets_user_intent($tweetuser,$twitterlocale,'icon',$targetvalue)."</div>";
						$result .= "\n\t\t<div class='rtw_name'>".rotatingtweets_user_intent($tweetuser,$twitterlocale,'name',$targetvalue)."</div>";
						$result .= "\n\t\t<div class='rtw_id'>".rotatingtweets_user_intent($tweetuser,$twitterlocale,'screen_name',$targetvalue)."</div>";
						$result .= "\n\t</div>";
						$result .= "\n\t<p class='rtw_main'>".$main_text."</p>";
						$result .= "\n\t<div class='rtw_meta'><div class='rtw_intents'>".rotatingtweets_intents($twitter_object,$twitterlocale, 1,$targetvalue).'</div>';
						$result .= rotatingtweets_timestamp_link($twitter_object,'long',$targetvalue);
						if(isset($retweeter)) {
							$result .= " &middot; ".rotatingtweets_user_intent($retweeter,$twitterlocale,sprintf(__('Retweeted by %s','rotatingtweets'),$retweeter['name']),$targetvalue);
						}
						$result .= "\n</div>";
						break;
					case 2:
						# This is a slightly adjusted version of the original tweet - designed for wide boxes - consistent with Twitter guidelines
						$result .= "\n\t\t<div class='rtw_wide'>";
						$result .= "\n\t\t<div class='rtw_wide_icon'>".rotatingtweets_user_intent($tweetuser,$twitterlocale,'icon',$targetvalue)."</div>";
						$result .= "\n\t\t<div class='rtw_wide_block'><div class='rtw_info'>";
						$result .= "\n\t\t\t<div class='rtw_time_short'>".rotatingtweets_timestamp_link($twitter_object,'short',$targetvalue).'</div>';
						$result .= "\n\t\t\t<div class='rtw_name'>".rotatingtweets_user_intent($tweetuser,$twitterlocale,'name',$targetvalue)."</div>";
						$result .= "\n\t\t\t<div class='rtw_id'>".rotatingtweets_user_intent($tweetuser,$twitterlocale,'screen_name',$targetvalue)."</div>";
						$result .= "\n\t\t</div>";
						$result .= "\n\t\t<p class='rtw_main'>".$main_text."</p>";
//						$result .= "\n\t\t<div class='rtw_meta'><div class='rtw_intents'>".rotatingtweets_intents($twitter_object,$twitterlocale, 1).'</div>';
						if(isset($retweeter)) {
							$result .= "\n\t\t<div class='rtw_rt_meta'>".rotatingtweets_user_intent($retweeter,$twitterlocale,"<img src='".plugins_url('images/retweet_on.png',__FILE__)."' width='16' height='16' alt='".sprintf(__('Retweeted by %s','rotatingtweets'),$retweeter['name'])."' />".sprintf(__('Retweeted by %s','rotatingtweets'),$retweeter['name']),$targetvalue)."</div>";
						}
						$result .= "\n\t\t<div class='rtw_meta'><span class='rtw_expand' style='display:none;'>".__('Expand','rotatingtweets')."</span><span class='rtw_intents'>".rotatingtweets_intents($twitter_object,$twitterlocale, 2,$targetvalue).'</span>';
						$result .= "</div></div></div>";
						break;
					case 3:
						# This one uses the twitter standard approach for embedding via their javascript API - unfortunately I can't work out how to make it work with the rotating tweet javascript!  If anyone can work out how to calculate the height of a oEmbed Twitter tweet, I will be very grateful! :-)
						$result .= '<blockquote class="twitter-tweet">';
						$result .= "<p>".$main_text."</p>";
						$result .= '&mdash; '.$user['name'].' (@'.$user['screen_name'].') <a href="https://twitter.com/twitterapi/status/'.$twitter_object['id_str'].'" data-datetime="'.date('c',strtotime($twitter_object['created_at'])).'"'.$targetvalue.'>'.date_i18n(get_option('date_format') ,strtotime($twitter_object['created_at'])).'</a>';
						$result .= '</blockquote>';
						break;
					}
				else:
					$result .= "\n\t\t<p class='rtw_main'>".__("Problem retrieving data from Twitter.",'rotatingtweets')."</p></div>";
					$result .= "<!-- rotatingtweets plugin was unable to parse this data: ".print_r($json,TRUE)." -->";
					$result .= "\n\t\t<div class = 'rotatingtweet' style='display:none'><p class='rtw_main'>".__("Please check the comments on this page's HTML to understand more.",'rotatingtweets')."</p>";
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
	if($args['show_follow'] && !empty($user['screen_name'])):
		$shortenvariables = '';
		if($args['no_show_count']) $shortenvariables = ' data-show-count="false"';
		if($args['no_show_screen_name']) $shortenvariables .= ' data-show-screen-name="false"';
		$followUserText = sprintf(__('Follow @%s','rotatingtweets'),$user['screen_name']);
		$result .= "\n<div class='rtw_follow follow-button'><a href='http://twitter.com/".$user['screen_name']."' class='twitter-follow-button'{$shortenvariables} title='".$followUserText."' data-lang='{$twitterlocale}'>".$followUserText."</a></div>";
	endif;
	rotatingtweets_enqueue_scripts();
	if($print) echo $result;
	return($result);
}
# Load the language files - needs to come after the widget_init line - and possibly the shortcode one too!
function rotatingtweets_init() {
	load_plugin_textdomain( 'rotatingtweets', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'rotatingtweets_init');

function rotatingtweets_enqueue_scripts() {
	wp_enqueue_script( 'jquery' ); 
	# Get Stylesheet
	$style = get_stylesheet();
	switch ($style):
		case 'bremen_theme':
		case 'zeebizzcard':
//		case 'zeeStyle':
			wp_dequeue_script( 'zee_jquery-cycle');
			wp_enqueue_script( 'zee_jquery-cycle', plugins_url('js/jquery.cycle.all.min.js', __FILE__),array('jquery'),FALSE,FALSE );
			wp_enqueue_script( 'rotating_tweet', plugins_url('js/rotating_tweet.js', __FILE__),array('jquery','zee_jquery-cycle'),FALSE,FALSE );
			break;
		default:
			wp_enqueue_script( 'jquery-cycle', plugins_url('js/jquery.cycle.all.min.js', __FILE__),array('jquery'),FALSE,FALSE );
			wp_enqueue_script( 'rotating_tweet', plugins_url('js/rotating_tweet.js', __FILE__),array('jquery','jquery-cycle'),FALSE,FALSE );
			break;
	endswitch;
	wp_enqueue_style( 'rotating_tweet', plugins_url('css/style.css', __FILE__));
	if(file_exists( plugin_dir_path(__FILE__).'css/yourstyle.css' )):
		wp_enqueue_style( 'rotating_tweet_yourstyle', plugins_url('css/yourstyle.css', __FILE__));
	endif;
}

function rotatingtweets_enqueue_admin_scripts($hook) {
		if( 'widgets.php' != $hook ) return;
		wp_enqueue_script( 'jquery' ); 
		wp_enqueue_script( 'rotating_tweet_admin', plugins_url('js/rotating_tweet_admin.js', __FILE__),array('jquery'),FALSE,FALSE );		
}
add_action( 'admin_enqueue_scripts', 'rotatingtweets_enqueue_admin_scripts' );
?>