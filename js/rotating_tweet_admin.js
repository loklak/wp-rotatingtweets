/*
 Script to cycle the rotating tweets
*/
jQuery(document).ready(function() {
	// Script to show or hide display options on start up
	jQuery('input.rtw_ad_official:checked').each(function() {
		var response = jQuery(this).attr('value');	
		if( response == 0) {
			jQuery(this).parent().find('.rtw_ad_tw_det').show('fast');
		} else {
			jQuery(this).parent().find('.rtw_ad_tw_det').hide('fast');
		}
	});
	// Script to show or hide display options when box is checked
	jQuery('.rtw_ad_official').change(function() {
		var response = jQuery(this).attr('value');
		if( response == 0) {
			jQuery(this).parent().find('.rtw_ad_tw_det').show('fast');
		} else {
			jQuery(this).parent().find('.rtw_ad_tw_det').hide('fast');			
		}
	});
});
