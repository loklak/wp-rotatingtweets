/*
 Script to cycle the rotating tweets
*/
jQuery(document).ready(function() {
	jQuery('.rotatingtweets').each(function() {
		var rotate_id = "#"+this.id
		jQuery(rotate_id).cycle({
			pause: 1,
			fx: 'scrollUp'
		});
	});
});