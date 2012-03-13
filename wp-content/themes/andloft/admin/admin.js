jQuery(document).ready(function()
{
	jQuery('.ft_options').slideUp(); // have options boxes closed as default
	
	// opening and closing of options boxes on admin page
	jQuery('.ft_section h3').click(function()
	{		
		if(jQuery(this).parent().next('.ft_options').css('display')=='none')
		{	jQuery(this).removeClass('inactive');
			jQuery(this).addClass('active');
			jQuery(this).children('img').removeClass('inactive');
			jQuery(this).children('img').addClass('active');
		}
		else
		{	jQuery(this).removeClass('active');
			jQuery(this).addClass('inactive');		
			jQuery(this).children('img').removeClass('active');			
			jQuery(this).children('img').addClass('inactive');
		}
		jQuery(this).parent().next('.ft_options').slideToggle('slow');	
	});	
	
	
	
	
	// used for centering notification
	jQuery.fn.center = function () {
		this.animate({"top":( jQuery(window).height() - this.height() - 200 ) / 2+jQuery(window).scrollTop() + "px"},100);
		this.css("left", 400 );
		return this;
	}
	jQuery('#message').center();
	jQuery(window).scroll(function() { 	
		jQuery('#message').center();
	});




	// tabs on post pages
	jQuery( '#tabs a' ).click( function() {
		// switch tabs
		jQuery( '.tab-div' ).hide();
		jQuery( '#' + this.id + '-tab' ).show();
 
		jQuery( '#tabs a' ).removeClass( 'active' );
		jQuery( '#' + this.id ).addClass( 'active' );
		this.blur();
		return false;
	});
	
	jQuery( '#tabsInner a' ).click( function() {
		// switch tabs
		jQuery( '.tabInner-div' ).hide();
		jQuery( '#' + this.id + '-tabInner' ).show();
 
		jQuery( '#tabsInner a' ).removeClass( 'active' );
		jQuery( '#' + this.id ).addClass( 'active' );
		this.blur();
		return false;
	});
	
	
	
	
	// used on post page for video/image selecting
	jQuery('.useThis').css('display', 'none'); // hide radio buttons from view
	// switch radio button selection
	jQuery('#tabs a').click( function() {
		jQuery('#'+jQuery(this).attr('id')+'-tab .useThisRadio').attr('checked', 'checked');
	});
	
	// used on post page for video service selecting
	jQuery('input[name=video_use]').css('display', 'none'); // hide radio buttons from view
	// switch radio button selection
	jQuery('#tabsInner a').click( function() {
		jQuery('#'+jQuery(this).attr('id')+'-tabInner input[name=video_use]').attr('checked', 'checked');
	});
	
});