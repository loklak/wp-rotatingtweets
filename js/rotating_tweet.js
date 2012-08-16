/*
 Script to cycle the rotating tweets
*/
jQuery(document).ready(function() {
	jQuery('.rotatingtweets').each(function() {
		/* Get the ID of the rotating tweets div - and parse it to get rotation speed and rotation fx */
		var rotate_id = "#"+this.id
		var rotate_id_split = rotate_id.split('_');
		var rotate_timeout = rotate_id_split[1];
		var rotate_fx = rotate_id_split[2];
		/* If the rotation type has not been set - then set it to scrollUp */
		if(rotate_fx == null){rotate_fx = 'scrollUp'};
		/* Call the rotation */
		jQuery(rotate_id).cycle({
			pause: 1,
			height: 'auto',
			timeout: rotate_timeout,
			fx: rotate_fx
		});
		/* If the height of the rotating tweet box is zero - kill the box and start again */
		var rt_height = jQuery(rotate_id).height();
		if(rt_height == 0) {
			var rt_children_id = rotate_id + ' .rotatingtweet';
			var rt_height = 0;
			/* Go through the tweets - get their height - and set the minimum height */
			jQuery(rt_children_id).each(function() {
				var rt_tweet_height = jQuery(this).height();
				if(rt_tweet_height > rt_height) {
					rt_height = rt_tweet_height;
				}
			});
			var rt_height_px = rt_height + 'px';
			jQuery(rotate_id).cycle('destroy');
			jQuery(rotate_id).cycle({
				pause: 1,
				height: rt_height_px,
				timeout: rotate_timeout,
				fx: rotate_fx
			});
		}		
/*
		jQuery(rotate_id).cycle({
			pause: 1,
			height: rotate_height,
			timeout: rotate_timeout,
			prev: jQuery(rotate_id).find('.rtw_prev'),
			next: jQuery(rotate_id).find('.rtw_next'),
			pager: rotate_id + '_nav',
	        pagerAnchorBuilder: function(idx, slide) {
				// return sel string for existing anchor
				return rotate_id + '_nav a:eq(' + (idx) + ')';
			},
			fx: rotate_fx
		});
*/
	});
});
/* And call the Twitter script while we're at it! */
/* Standard script to call Twitter */
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");