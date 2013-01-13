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
		/* If we're displaying an 'official' tweet, reset all the heights */
		var rt_height_px = 'auto';
//		var rt_official_child = rotate_id + ' .twitter-tweet';
//		var rt_official_num = jQuery(rt_official_child).length;
//		if (rt_official_num > 0) rt_height_px = '211px';
		/* Call the rotation */
		jQuery(rotate_id).cycle({
			pause: 1,
			height: rt_height_px,
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
		/* Only do this if we're showing the official tweets - the first select is the size of the info box at the top of the tweet */
		var rt_children_id = rotate_id + ' .rtw_info';
		/* This shows the width of the icon on 'official version 2' - i.e. the one where the whole tweet is indented */
		var rt_icon_id = rotate_id + ' .rtw_wide_icon a img';
		/* This shows the width of the block containing the icon on 'official version 2' - i.e. the one where the whole tweet is indented */
		var rt_block_id = rotate_id + ' .rtw_wide_block';
		var rt_official_num = jQuery(rt_children_id).length;
		if(rt_official_num > 0) {
			/* Now run through and make sure all the boxes are the right size */
			if(jQuery(rt_icon_id).length > 0) {
//				console.log('Rotating Tweets container: ' + jQuery(this).width());
//				console.log('Width of the icon container: ' + jQuery(rt_icon_id).show().width());
				var rt_icon_width = 0;
				jQuery(rt_icon_id).each( function() {
					newiconsize = jQuery(this).width();
					if(newiconsize>rt_icon_width) {
						rt_icon_width = newiconsize;
					}
				});
//				console.log('Iconsize: '+rt_icon_width);
				if(rt_icon_width > 0) {
					jQuery(rt_block_id).each( function() {
						jQuery(this).css('padding-left', ( rt_icon_width + 10 ) + 'px');
					});
				}
			}
			/* Now get the padding-left dimension (if it exists) and subtract it from the max width	*/
//			console.log ('leftpadding - text : '+ jQuery(rt_block_id).css('padding-left') + ' and value: ' +parseInt(jQuery(rt_block_id).css('padding-left')));
			var rt_max_width = jQuery(rotate_id).width();
			if( typeof jQuery(rt_block_id).css('padding-left') != 'undefined' ) {
				rt_max_width = rt_max_width - parseInt(jQuery(rt_block_id).css('padding-left'));
//				console.log('Padding is not undefined');
			} else {
//				console.log('Padding IS undefined - leave width unchanged');
			}
//			console.log('Rotating Tweets container minus the indent: ' + rt_max_width);			
			/* Go through the tweets - and set the minimum width */
			jQuery(rt_children_id).each(function() {
				jQuery(this).width(rt_max_width);
			});
			var rt_children_id = rotate_id + ' .rtw_meta';
			/* Go through the tweets - and set the minimum width */
			jQuery(rt_children_id).each(function() {
				jQuery(this).width(rt_max_width);
			});
		};

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
	// Script to show mouseover effects when going over the Twitter intents
	jQuery('.rtw_intents a').hover(function() {
		var rtw_src = jQuery(this).find('img').attr('src');
		var clearOutHovers = /_hover.png$/;
		jQuery(this).find('img').attr('src',rtw_src.replace(clearOutHovers,".png"));
		var rtw_src = jQuery(this).find('img').attr('src');
		var srcReplacePattern = /.png$/;
		jQuery(this).find('img').attr('src',rtw_src.replace(srcReplacePattern,"_hover.png"));
	},function() {
		var rtw_src = jQuery(this).find('img').attr('src');
		var clearOutHovers = /_hover.png/;
		jQuery(this).find('img').attr('src',rtw_src.replace(clearOutHovers,".png"));
	});
	jQuery('.rtw_wide .rtw_intents').hide();
	jQuery('.rtw_expand').show();
	jQuery('.rotatingtweets').has('.rtw_wide').hover(function() {
		jQuery(this).find('.rtw_intents').show();
//		jQuery(this).find('.rtw_expand').css('color','blue');
	},function() {
		jQuery(this).find('.rtw_intents').hide();
//		jQuery(this).find('.rtw_expand').css('color','red');
	});
});
/*
jQuery(window).ready(function() {
	// Experimenting with resizing the size of the box when using displayed tweets
	twttr.ready(function(twttr) {
		jQuery('blockquote').each( function() {
			console.log(jQuery(this).prop('class'));
			console.log(jQuery(this).height());			
		});
	});
});
*/
/* And call the Twitter script while we're at it! */
/* Standard script to call Twitter */
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");