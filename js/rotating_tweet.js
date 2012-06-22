/*
 Script to cycle the rotating tweets
*/
jQuery(document).ready(function() {
	jQuery('.rotatingtweets').each(function() {
		var rotate_id = "#"+this.id
		var timeoutdelay = rotate_id.split('_');
		jQuery(rotate_id).cycle({
			pause: 1,
			timeout: timeoutdelay[1],
			fx: 'scrollUp'
		});
	});
});