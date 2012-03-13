<?php

/*
*
* Author: FrogsThemes
* File: theme specific options that are displayed on admin page
*
*
*/

$themename = "FolioGrid Pro";
$shortname = "fgp";

// array of options for admin
$options = array (
 
	array("name" => $themename." Options",
		  "type" => "title"),
	 	
	array("name" => "General",
		  "type" => "section"),
	 
	array("name" => "Colour Scheme",
		  "desc" => "Select the colour scheme for the theme",
		  "id" => $shortname."_color_scheme",
		  "type" => "select",
		  "options" => array("Dark", "Light", "Yellow", "Red", "Purple", "Blue", "Green"),
		  "std" => "Dark"),
	
	array("name" => "Custom Header Image",
		  "desc" => "Paste the URL of the header image you wish to use or select an image to upload from your computer.",
		  "id" => $shortname."_header_image",
		  "type" => "image",
		  "std" => ""),
	
	array("name" => "Custom CSS",
		  "desc" => "Want to add any custom CSS code? Put in here, and the rest is taken care of. This overrides any other stylesheets. eg: a.button{color:green}",
		  "id" => $shortname."_custom_css",
		  "type" => "textarea",
		  "std" => ""),		
			
	array("type" => "close"),
	
	array("name" => "Footer",
		  "type" => "section"),
		
	array("name" => "Footer copyright text",
		  "desc" => "Enter text used in the right side of the footer. It can be HTML",
		  "id" => $shortname."_footer_text",
		  "type" => "text",
		  "std" => ""),
		
	array("name" => "Google Analytics Code",
		  "desc" => "You can paste your Google Analytics or other tracking code in this box. This will be automatically added to the footer.",
		  "id" => $shortname."_ga_code",
		  "type" => "textarea",
		  "std" => ""),	
		
	array("name" => "Custom Favicon",
		  "desc" => "A favicon is a 16x16 pixel icon that represents your site; paste the URL to a .ico image that you want to use as the image",
		  "id" => $shortname."_favicon",
		  "type" => "text",
		  "std" => get_bloginfo('url') ."/favicon.ico"),	
		
	array("name" => "Feedburner URL",
		  "desc" => "Feedburner is a Google service that takes care of your RSS feed. Paste your Feedburner URL here to let readers see it in your website",
		  "id" => $shortname."_feedburner",
		  "type" => "text",
		  "std" => get_bloginfo('rss2_url')),
		 
	array("type" => "close")
);

update_option($shortname.'_themename',$themename);   
update_option($shortname.'_shortname',$shortname);

?>