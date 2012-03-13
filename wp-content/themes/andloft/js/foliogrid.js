
// FolioGrid Pro js

// popout links

function animateTopLink(){
	var right = jQuery("#top_link").css("right");
	if(right == "-20px"){
		jQuery("#top_link").animate({right: "-100px"},500);
	}else{
		jQuery("#top_link").animate({right: "-20px"},500);
	}
}

function animateRSS(){
	var right = jQuery("#rss_link").css("right");
	if(right == "-20px"){
		jQuery("#rss_link").animate({right: "-131px"},500);
	}else{
		jQuery("#rss_link").animate({right: "-20px"},500);
	}
}

function animateSearch(){
	var right = jQuery("#searchBox").css("right");
	if(right == "-20px"){
		jQuery("#searchBox").animate({right: "-250px"},500);
	}else{
		jQuery("#searchBox").animate({right: "-20px"},500);
	}
}

// end popout links

/* Copyright (c) 2008 Kean Loong Tan http://www.gimiti.com/kltan
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * Copyright notice and license must remain intact for legal use
 * jFade
 * Version: 1.0 (Jun 30, 2008)
 * Requires: jQuery 1.2.6+
 *
 *
 * Original Code Copyright (c) 2008 by Michael Leigeber
 * Website: http://www.leigeber.com
 *
 *
 */

(function($) {

	jQuery.fn.jFade = function(options) {
		// merge users option with default options
		var opts = jQuery.extend({}, jQuery.fn.jFade.defaults, options);
		var startrgb,endrgb,er,eg,eb,rint,gint,bint,step;
		var target = this;
	
		//var obj = this;
	
		var init = function() {
			var tgt = target;
			opts.steps = opts.steps || 20;
			opts.duration = opts.duration || 20;
			//clear everything + reset
			clearInterval(tgt.timer);
			endrgb = colorConv(opts.end);
			er = endrgb[0];
			eg = endrgb[1];
			eb = endrgb[2];
		
			if(!tgt.r) {
				//convert to usable rgb value
				startrgb = colorConv(opts.start);
				r = startrgb[0];
				g = startrgb[1];
				b = startrgb[2];
				tgt.r = r;
				tgt.g = g;
				tgt.b = b;
			}
			//process red
			rint = Math.round(Math.abs(tgt.r-er)/opts.steps);
			//process green
			gint = Math.round(Math.abs(tgt.g-eg)/opts.steps);
			//process blue
			bint = Math.round(Math.abs(tgt.b-eb)/opts.steps);
			if(rint == 0) { rint = 1 }
			if(gint == 0) { gint = 1 }
			if(bint == 0) { bint = 1 }
		
			tgt.step = 1;
			tgt.timer = setInterval( function() { animateColor(tgt,opts.property,opts.steps,er,eg,eb,rint,gint,bint) }, opts.duration);
		
			function animateColor(obj,property,steps,er,eg,eb,rint,gint,bint) {
				var tgt = obj;
				var color;
				if(tgt.step <= steps) { // for each loop
					var r = tgt.r;
					var g = tgt.g;
					var b = tgt.b;
					if(r >= er) {
						r = r - rint;
					}
					else {
					r = parseInt(r) + parseInt(rint);
					}
					if(g >= eg) {
						g = g - gint;
					}
					else {
						g = parseInt(g) + parseInt(gint);
					}
					if(b >= eb) {
					b = b - bint;
					}
					else {
						b = parseInt(b) + parseInt(bint);
					}
					color = 'rgb(' + r + ',' + g + ',' + b + ')';
					
					jQuery(obj).css(property, color);
					
					tgt.r = r;
					tgt.g = g;
					tgt.b = b;
					tgt.step = tgt.step + 1;
				}
				else {// last loop
				
					clearInterval(tgt.timer);
					color = 'rgb(' + er + ',' + eg + ',' + eb + ')';
					jQuery(obj).css(property, color);
				}
			}
			
			// convert the color to rgb from hex
			function colorConv(color) {
				//covert 0-2 position hex into decimal in rgb[0]
				//covert 2-4 position hex into decimal in rgb[1]
				//covert 4-6 position hex into decimal in rgb[2]
				var rgb = [parseInt(color.substring(0,2),16),
				parseInt(color.substring(2,4),16),
				parseInt(color.substring(4,6),16)];
				//return array containing rgb[0], rgb[1], rgb[2]
				return rgb;
			}
		};
		if (opts.trigger == "load")
			init();
		else
			jQuery(this).bind(opts.trigger, function(){
				target = this;
				init();
			});
		
		return this;
	};

	jQuery.fn.jFade.defaults = {
		trigger: "load",
		property: 'background',
		start: 'FFFFFF',
		end: '000000',
		steps: 5,
		duration: 30
	};
})(jQuery);

/*******

	***	Anchor Slider by Cedric Dugas   ***
	*** Http://www.position-absolute.com ***
	
	Never have an anchor jumping your content, slide it.

	Don't forget to put an id to your anchor !
	You can use and modify this script for any project you want, but please leave this comment as credit.
	
*****/

jQuery(document).ready(function() {
	jQuery("a.anchorLink").anchorAnimate();
});

jQuery.fn.anchorAnimate = function(settings) {

 	settings = jQuery.extend({
		speed : 500
	}, settings);	
	
	return this.each(function(){
		var caller = this
		jQuery(caller).click(function (event) {	
			event.preventDefault()
			var locationHref = window.location.href
			var elementClick = jQuery(caller).attr("href")
			
			var destination = jQuery(elementClick).offset();
			jQuery("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination}, settings.speed, function() {
				window.location.hash = elementClick
			});
		  	return false;
		})
	})
}

jQuery(document).ready(function() {
	jQuery('#foliogrid .post').click(function(){
		href = jQuery(this).children('div').children("a[rel='bookmark']").attr('href');
		if (href != undefined)
		{
			window.location = href; 
		}
	}).hover(function(){
		jQuery(this).css('cursor', 'pointer');
	}, function(){
		jQuery(this).css('cursor', 'default');        
	});
});