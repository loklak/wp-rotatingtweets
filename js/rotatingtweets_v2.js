/*
 Add some transitions
*/
(function($) {
"use strict";

$.fn.cycle2.transitions.scrollDown = {
    before: function( opts, curr, next, fwd ) {
        opts.API.stackSlides( opts, curr, next, fwd );
        var width = opts.container.css('overflow','visible').width();
        var height = opts.container.css('overflow','hidden').height();
        opts.cssBefore = { top: fwd ? -height : height, left: 0, opacity: 1, display: 'block' ,width:width };
        opts.animIn = { top: 0 };
        opts.animOut = { top: fwd ? height : -height };
    }
};
$.fn.cycle2.transitions.scrollUp = {
    before: function( opts, curr, next, fwd ) {
        opts.API.stackSlides( opts, curr, next, fwd );
        var width = opts.container.css('overflow','visible').width();
        var height = opts.container.css('overflow','hidden').height();
        opts.cssBefore = { top: fwd ? height : -height, left: 0, opacity: 1, display: 'block' ,width:width };
        opts.animIn = { top: 0 };
        opts.animOut = { top: fwd ? -height : height };
    }
};
$.fn.cycle2.transitions.scrollLeft = {
    before: function( opts, curr, next, fwd ) {
        opts.API.stackSlides( opts, curr, next, fwd );
        var width = opts.container.css('overflow','hidden').width();
        opts.cssBefore = { width: width, left : width+20, top: 0, opacity: 1, display: 'block' };
        opts.animIn = { left: 0 };
        opts.animOut = { left : -width-20,width:width };
    }
};

$.fn.cycle2.transitions.scrollRight = {
    before: function( opts, curr, next, fwd ) {
        opts.API.stackSlides( opts, curr, next, fwd );
        var width = opts.container.css('overflow','hidden').width();
        opts.cssBefore = { width: width, left : -width-20, top: 0, opacity: 1, display: 'block' };
        opts.animIn = { left: 0 };
        opts.animOut = { left : width+20 };
    }
};

$.fn.cycle2.transitions.toss = {
    before: function( opts, curr, next, fwd ) {
        opts.API.stackSlides( opts, curr, next, fwd );
        var width = opts.container.css('overflow','visible').width();
		var height = opts.container.css('overflow','visible').height();
        opts.cssBefore = { left: 0, top: 0, opacity: 1, display: 'block',width:width };
        opts.animIn = { left: 0 };
        opts.animOut = { left : width*2, top:-height/2 , opacity:0, width:width, display:'block' };
    }
};

$.fn.cycle2.transitions.scrollLeftGap = {
    before: function( opts, curr, next, fwd ) {
        opts.API.stackSlides( opts, curr, next, fwd );
        var width = opts.container.css('overflow','hidden').width();
        opts.cssBefore = { width: width, left : width+100, top: 0, opacity: 1, display: 'block' };
        opts.animIn = { left: 0 };
        opts.animOut = { left : -width-100,width:width };
    }
};

})(jQuery);
/*
 Script to cycle the rotating tweets
*/
jQuery(document).ready(function() {
	// Not at all sure we need this
	jQuery('.rotatingtweets').cycle2();
	// Script to show mouseover effects when going over the Twitter intents
	var rtw_src,
		clearOutHovers = /_hover.png$/,
		srcReplacePattern = /.png$/;
	jQuery('.rtw_intents a').hover(function() {
		rtw_src = jQuery(this).find('img').attr('src');
		jQuery(this).find('img').attr('src',rtw_src.replace(clearOutHovers,".png"));
		rtw_src = jQuery(this).find('img').attr('src');
		jQuery(this).find('img').attr('src',rtw_src.replace(srcReplacePattern,"_hover.png"));
	},function() {
		rtw_src = jQuery(this).find('img').attr('src');
		jQuery(this).find('img').attr('src',rtw_src.replace(clearOutHovers,".png"));
	});
	jQuery('.rotatingtweets').children().not('.cycle-carousel-wrap').has('.rtw_wide').find('.rtw_wide .rtw_intents').hide();
	jQuery('.rotatingtweets').children().not('.cycle-carousel-wrap').has('.rtw_wide').find('.rtw_expand').show();
	jQuery('.rotatingtweets').children().not('.cycle-carousel-wrap').has('.rtw_wide').hover(function() {
		jQuery(this).find('.rtw_intents').show();
	},function() {
		jQuery(this).find('.rtw_intents').hide();
	});
});
/* And call the Twitter script while we're at it! */
/* Standard script to call Twitter */
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");