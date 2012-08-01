/*
 Script to cycle the rotating tweets (adjusted for the zeebizzcard template
 For some reason we need to fix the minimum height of the rotating tweet area - since without it it defaults to zero
*/
jQuery(document).ready(function() {
	jQuery('.rotatingtweets').each(function() {
		var rotate_id = "#"+this.id
		var timeoutdelay = rotate_id.split('_');
		jQuery(rotate_id).cycle({
			pause: 1,
			height: '6em',
			timeout: timeoutdelay[1],
			fx: 'scrollUp'
		});
	});
});