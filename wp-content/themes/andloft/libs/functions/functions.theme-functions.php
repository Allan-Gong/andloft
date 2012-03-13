<?php

/* determine column width */
function frogs_column_width($postID)
{	
	switch(get_post_meta($postID, 'columns', true))
	{
		case 'One':
		
			return 'post';
		
		break;
		
		case 'Two':
		
			return 'twocols';
		
		break;
		
		case 'Three':
		
			return 'threecols';
		
		break;
		
		default:
		
			return 'post';
		
		break;
	}
}

/* determine media type */
function frogs_media($postID)
{	
	switch(get_post_meta($postID, 'media_use', true))
	{
		case 'image':
		
			frogs_image($postID);
		
		break;
		
		case 'video':
		
			// what size video do we need?
			if(get_post_meta($postID, 'columns', true) == 'One')
			{
				$width = '200';
				$height = '200';
			}
			else if(get_post_meta($postID, 'columns', true) == 'Two')
			{
				$width = '430';
				$height = '340';
			}
			else if(get_post_meta($postID, 'columns', true) == 'Three')
			{
				$width = '660';
				$height = '510';
			}
			
			frogs_video($postID, $width, $height, 'false');
		
		break;
		
		default:
		
		break;
	}
}

function cssifysize($img) { 
$dimensions = getimagesize($img); 
$dimensions = str_replace("=\"", ":", $dimensions['3']); 
$dimensions = str_replace("\"", "px;", $dimensions); 
return $dimensions; 
} 

/* get image to insert into post header */
function frogs_image($postID)
{
	if(has_post_thumbnail())
	{
		$image_id = get_post_thumbnail_id();  
		$image_url = wp_get_attachment_image_src($image_id,frogs_column_width($postID));
		//$image_url = wp_get_attachment_image_src($image_id, 'thumbnail-' . frogs_column_width($postID));  
		$image_url = $image_url[0]; 

		echo '<a href="'.get_permalink($postID).'"><img src="'.$image_url.'" alt="" style="'.cssifysize($image_url).'" /></a>';
	}
}

/* function to insert video into post if there is one... */
function frogs_video($postID, $width, $height, $post)
{
	// get the URL to the video on the host site
	switch(get_post_meta($postID, 'video_use', true))
	{
		case 'youtube':	
		
			$url = 'http://www.youtube.com/v/'.get_post_meta($postID, 'youtube_ID', true);
			
			$object  = '<object width="'.$width.'" height="'.$height.'">';
			$object .= '<param name="movie" value="'.$url.'"></param>';
			$object .= '<param name="wmode" value="transparent"></param>';
			$object .= '<param name="allowFullScreen" value="true"></param>';
			$object .= '<param name="allowscriptaccess" value="always"></param>';
			$object .= '<embed src="'.$url.'" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="always" allowfullscreen="true" width="'.$width.'" height="'.$height.'"></embed>';
			$object .= '</object>';
		
		break;
		
		case 'vimeo':	
			
			$object  = '<object width="'.$width.'" height="'.$height.'">';
			$object  .= '<param name="allowfullscreen" value="true" />';
			$object  .= '<param name="allowscriptaccess" value="always" />';
			$object .= '<param name="wmode" value="transparent" />';
			$object  .= '<param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='.get_post_meta($postID, 'vimeo_ID', true).'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=D76B00&amp;fullscreen=1" />';
			$object  .= '<embed src="http://vimeo.com/moogaloop.swf?clip_id='.get_post_meta($postID, 'vimeo_ID', true).'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=D76B00&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'.$width.'" height="'.$height.'" wmode="transparent"></embed>';
			$object  .= '</object>';
		
		break;
		
		case 'yahoo':	
			
			$idParts = explode('/', get_post_meta($postID, 'yahoo_ID', true));
			
			$object  .= '<object width="'.$width.'" height="'.$height.'">';
			$object  .= '<param name="movie" value="http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46" />';
			$object  .= '<param name="allowFullScreen" value="true" />';
			$object  .= '<param name="AllowScriptAccess" VALUE="always" />';
			$object  .= '<param name="bgcolor" value="#000000" />';
			$object  .= '<param name="flashVars" value="id='.$idParts[1].'&vid='.$idParts[0].'&lang=en-us&intl=usembed=1" />';
			$object  .= '<embed src="http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" allowFullScreen="true" AllowScriptAccess="always" bgcolor="#000000" flashVars="id='.$idParts[1].'&vid='.$idParts[0].'&lang=en-us&intl=usembed=1" wmode="transparent" ></embed>';
			$object  .= '</object>';

		break;
		
		case 'myspace':	
			
			$object  = '<object width="'.$width.'" height="'.$height.'" >';
			$object  .= '<param name="allowFullScreen" value="true"/>';
			$object  .= '<param name="wmode" value="transparent"/>';
			$object  .= '<param name="movie" value="http://mediaservices.myspace.com/services/media/embed.aspx/m='.get_post_meta($postID, 'myspace_ID', true).',t=1,mt=video"/>';
			$object  .= '<embed src="http://mediaservices.myspace.com/services/media/embed.aspx/m='.get_post_meta($postID, 'myspace_ID', true).',t=1,mt=video" width="'.$width.'" height="'.$height.'" allowFullScreen="true" type="application/x-shockwave-flash" wmode="transparent"></embed>';
			$object  .= '</object>';
		
		break;
		
		case 'dailymotion':	
		
			$object  = '<object width="'.$width.'" height="'.$height.'">';
			$object  .= '<param name="movie" value="http://www.dailymotion.com/swf/'.get_post_meta($postID, 'dailymotion_ID', true).'"></param>';
			$object  .= '<param name="allowfullscreen" value="true"></param>';
			$object  .= '<embed src="http://www.dailymotion.com/swf/'.get_post_meta($postID, 'dailymotion_ID', true).'" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" allowfullscreen="true" wmode="transparent"></embed>';
			$object  .= '</object>';
		
		break;
		
		case 'revver':	

			$object  = '<object width="'.$width.'" height="'.$height.'" data="http://flash.revver.com/player/1.0/player.swf?mediaId='.get_post_meta($postID, 'revver_ID', true).'" type="application/x-shockwave-flash" id="revvervideoa17743d6aebf486ece24053f35e1aa23">';
			$object  .= '<param name="Movie" value="http://flash.revver.com/player/1.0/player.swf?mediaId='.get_post_meta($postID, 'revver_ID', true).'"></param>';
			$object  .= '<param name="FlashVars" value="allowFullScreen=true"></param>';
			$object  .= '<param name="AllowFullScreen" value="true"></param>';
			$object  .= '<param name="AllowScriptAccess" value="always"></param>';
			$object  .= '<param name="wmode" value="transparent"></param>';
			$object  .= '<embed type="application/x-shockwave-flash" src="http://flash.revver.com/player/1.0/player.swf?mediaId='.get_post_meta($postID, 'revver_ID', true).'" pluginspage="http://www.macromedia.com/go/getflashplayer" allowScriptAccess="always" flashvars="allowFullScreen=true" allowfullscreen="true" height="'.$height.'" width="'.$width.'" wmode="transparent"></embed>';
			$object  .= '</object>';
		
		break;
		
		case 'metacafe':	
		
			$idParts = explode('/', get_post_meta($postID, 'metacafe_ID', true));
			
			$object  .= '<embed flashVars="playerVars=showStats=yes|autoPlay=no|" src="http://www.metacafe.com/fplayer/'.$idParts[0].'/'.$idParts[1].'.swf" width="'.$width.'" height="'.$height.'" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_'.$idParts[0].'" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent"></embed>';
			
		break;

		case 'break':	
			
			$object  = '<object width="'.$width.'" height="'.$height.'" id="1825893" type="application/x-shockwave-flash" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" alt="">';
			$object  .= '<param name="movie" value="http://embed.break.com/'.get_post_meta($postID, 'break_ID', true).'"></param>
						<param name="allowScriptAccess" value="always"></param>';
			$object  .= '<embed src="http://embed.break.com/'.get_post_meta($postID, 'break_ID', true).'" type="application/x-shockwave-flash" allowScriptAccess=always width="'.$width.'" height="'.$height.'" wmode="transparent"></embed>';
			$object  .= '</object>';
		
		break;

		case 'blip':	
		
			$object  = '<embed src="http://blip.tv/play/'.get_post_meta($postID, 'blip_ID', true).'" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"></embed>';
		
		break;
	
		case 'viddler':	
		
			$object  = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$width.'" height="'.$height.'" id="viddler">';
			$object  .= '<param name="movie" value="http://www.viddler.com/player/'.get_post_meta($postID, 'viddler_ID', true).'/" />';
			$object  .= '<param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" />';
			$object  .= '<param name="flashvars" value="fake=1"/>';
			$object  .= '<embed src="http://www.viddler.com/player/'.get_post_meta($postID, 'viddler_ID', true).'/" width="'.$width.'" height="'.$height.'" type="application/x-shockwave-flash" allowScriptAccess="always" allowFullScreen="true" flashvars="fake=1" name="viddler" wmode="transparent"></embed>';
			$object  .= '</object>';
		
		break;

		default:
		
			$url = 'http://www.youtube.com/v/'.get_post_meta($postID, 'youtube_ID', true);
			
			$object  = '<object width="'.$width.'" height="'.$height.'">';
			$object .= '<param name="movie" value="'.$url.'"></param>';
			$object .= '<param name="wmode" value="transparent"></param>';
			$object .= '<param name="allowFullScreen" value="true"></param>';
			$object .= '<param name="allowscriptaccess" value="always"></param>';
			$object .= '<embed src="'.$url.'" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="always" allowfullscreen="true" width="'.$width.'" height="'.$height.'"></embed>';
			$object .= '</object>';
		
		break;
	}
	
	if($post == 'true')
	{
		echo '<p>'.$object.'</p>';
	}
	else
	{
		echo $object;	
	}
}

/* goes into wp_head(); */
function frogs_wp_head()
{
	// stylesheet selector
	if($_REQUEST['style'])
	{
		$style = $_REQUEST['style'];
		$_SESSION['style'] = $style;
	}
	else
	{
		$style = $_SESSION['style'];
	}
	
	
	if ($style != '') 
	{
		$GLOBALS['stylesheet'] = $style;
		echo '<link href="'. get_bloginfo('template_directory') .'/styles/'. $GLOBALS['stylesheet'] .'/'.strtolower($GLOBALS['stylesheet']).'.css" rel="stylesheet" type="text/css" />'."\n";
	} 
	else 
	{ 
		$GLOBALS['stylesheet'] = get_option('fgp_color_scheme');
		
		if($GLOBALS['stylesheet'] != '')
		{
			echo '<link href="'. get_bloginfo('template_directory') .'/styles/'. $GLOBALS['stylesheet'] .'/'.strtolower($GLOBALS['stylesheet']).'.css" rel="stylesheet" type="text/css" />'."\n";
		}
		else
		{
			echo '<link href="'. get_bloginfo('template_directory') .'/styles/Dark/dark.css" rel="stylesheet" type="text/css" />'."\n";
		}
	} 
	
	// custom favicon
	if(get_option('fgp_favicon') != '') 
	{
		echo '<link rel="shortcut icon" href="'.  get_option('fgp_favicon')  .'"/>'."\n";
	}
	
	// custom css
	if(get_option('fgp_custom_css')!='')
	{
		echo "\n<style type=\"text/css\">\n" . get_option('fgp_custom_css') . "</style>\n";
	}
	
}

/* initialise scripts and css */
function frogs_init() {

	$file_dir=get_bloginfo('template_directory');
	
	if(!is_admin()):
		wp_deregister_script( 'jquery' );
	    //wp_register_script( 'jquery', $file_dir."/js/jquery.masonry.js");
	
		wp_enqueue_script("jquery", $file_dir."/js/jquery.js", false, "1.4.2");
		wp_enqueue_script("jquery_masonry", $file_dir."/js/jquery.masonry.js", false, "1.2.0");
	endif;
}


/* goes into wp_footer(); */
function frogs_wp_footer()
{	
	if(get_option('fgp_ga_code'))
	{
		echo stripslashes(get_option('fgp_ga_code'));	
	}
	
	// stylesheet selector
	if($_REQUEST['style'])
	{
		$style = $_REQUEST['style'];
		$_SESSION['style'] = $style;
	}
	else
	{
		$style = $_SESSION['style'];
	}
	
	if ($style == '')
	{ 
		$GLOBALS['stylesheet'] = get_option('fgp_color_scheme');
		
		if($GLOBALS['stylesheet'] != '')
		{
			echo '<script type="text/javascript" src="'. get_bloginfo('template_directory') .'/styles/'. $GLOBALS['stylesheet'] .'/'.strtolower($GLOBALS['stylesheet']).'.js"></script>'."\n";
		}
		else
		{
			echo '<script type="text/javascript" src="'. get_bloginfo('template_directory') .'/styles/Dark/dark.js"></script>'."\n";
		}
	}
	else
	{
		echo '<script type="text/javascript" src="'. get_bloginfo('template_directory') .'/styles/'. $GLOBALS['stylesheet'] .'/'.strtolower($GLOBALS['stylesheet']).'.js"></script>'."\n";
	}
}


/* excerpt reloaded functions */
function frog_wp_the_excerpt_reloaded($args='')
{
	parse_str($args);
	if(!isset($excerpt_length)) $excerpt_length = 120; // length of excerpt in words. -1 to display all excerpt/content
	if(!isset($allowedtags)) $allowedtags = '<a>'; // HTML tags allowed in excerpt, 'all' to allow all tags.
	if(!isset($filter_type)) $filter_type = 'none'; // format filter used => 'content', 'excerpt', 'content_rss', 'excerpt_rss', 'none'
	if(!isset($use_more_link)) $use_more_link = 1; // display
	if(!isset($more_link_text)) $more_link_text = "(more...)";
	if(!isset($force_more)) $force_more = 1;
	if(!isset($fakeit)) $fakeit = 1;
	if(!isset($fix_tags)) $fix_tags = 1;
	if(!isset($no_more)) $no_more = 0;
	if(!isset($more_tag)) $more_tag = 'div';
	if(!isset($more_link_title)) $more_link_title = 'Continue reading this entry';
	if(!isset($showdots)) $showdots = 1;

	return frog_the_excerpt_reloaded($excerpt_length, $allowedtags, $filter_type, $use_more_link, $more_link_text, $force_more, $fakeit, $fix_tags, $no_more, $more_tag, $more_link_title, $showdots);
}

function frog_the_excerpt_reloaded($excerpt_length=120, $allowedtags='<a>', $filter_type='none', $use_more_link=true, $more_link_text="(more...)", $force_more=true, $fakeit=1, $fix_tags=true, $no_more=false, $more_tag='div', $more_link_title='Continue reading this entry', $showdots=true)
{
	if(preg_match('%^content($|_rss)|^excerpt($|_rss)%', $filter_type)) 
	{
		$filter_type = 'the_' . $filter_type;
	}
	echo frog_get_the_excerpt_reloaded($excerpt_length, $allowedtags, $filter_type, $use_more_link, $more_link_text, $force_more, $fakeit, $no_more, $more_tag, $more_link_title, $showdots);
}

function frog_get_the_excerpt_reloaded($excerpt_length, $allowedtags, $filter_type, $use_more_link, $more_link_text, $force_more, $fakeit, $no_more, $more_tag, $more_link_title, $showdots) 
{
	global $post;

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) { // and it doesn't match cookie
			if(is_feed()) { // if this runs in a feed
				$output = __('There is no excerpt because this is a protected post.');
			} else {
	            $output = get_the_password_form();
			}
		}
		return $output;
	}

	if($fakeit == 2) { // force content as excerpt
		$text = $post->post_content;
	} elseif($fakeit == 1) { // content as excerpt, if no excerpt
		$text = (empty($post->post_excerpt)) ? $post->post_content : $post->post_excerpt;
	} else { // excerpt no matter what
		$text = $post->post_excerpt;
	}

	if($excerpt_length < 0) 
	{
		$output = $text;
	} 
	else 
	{
		if(!$no_more && strpos($text, '<!--more-->')) 
		{
		    $text = explode('<!--more-->', $text, 2);
			$l = count($text[0]);
			$more_link = 1;
		} 
		else 
		{
			$text = explode(' ', $text);
			if(count($text) > $excerpt_length) 
			{
				$l = $excerpt_length;
				$ellipsis = 1;
			} 
			else 
			{
				$l = count($text);
				$more_link_text = '';
				$ellipsis = 0;
			}
		}
		for ($i=0; $i<$l; $i++)
				$output .= $text[$i] . ' ';
	}

	if('all' != $allowed_tags) 
	{
		$output = strip_tags($output, $allowedtags);
	}

	//	$output = str_replace(array("\r\n", "\r", "\n", "  "), " ", $output);
	$output = rtrim($output, "\s\n\t\r\0\x0B");
	$output = ($fix_tags) ? $output : balanceTags($output);
	$output .= ($showdots && $ellipsis) ? '...' : '';

	switch($more_tag) 
	{
		case('div') :
			$tag = 'div';
		break;
		case('span') :
			$tag = 'span';
		break;
		case('p') :
			$tag = 'p';
		break;
		default :
			$tag = 'span';
	}

	if ($use_more_link && $more_link_text)
	{
		if($force_more)
		{
			$output .= ' <' . $tag . ' class="more-link"><a href="'. get_permalink($post->ID) . '#more-' . $post->ID .'" title="' . $more_link_title . '">' . $more_link_text . '</a></' . $tag . '>' . "\n";
		} 
		else 
		{
			$output .= ' <' . $tag . ' class="more-link"><a href="'. get_permalink($post->ID) . '" title="' . $more_link_title . '">' . $more_link_text . '</a></' . $tag . '>' . "\n";
		}
	}

	$output = apply_filters($filter_type, $output);

	return $output;
}
?>