<?php


function frogs_header()
{
	?>
	<script type="text/javascript">
	
		// need to change enctype to allow us to upload images...
		jQuery(document).ready(function()
		{
			jQuery('form#post').attr('enctype','multipart/form-data');
			jQuery('form#post').attr('encoding','multipart/form-data');
		});
	
	</script>
	<?php
}

/* Add header stuff to admin */
add_action('admin_head', 'frogs_header');

/* Add a new meta box to the admin menu. */
add_action('admin_menu', 'frog_create_meta_box');

/* Saves the meta box data. */
add_action('save_post', 'frog_save_meta_data');

/**
 * Function for adding meta boxes to the admin.
 * Separate the post and page meta boxes.
*/
function frog_create_meta_box() 
{
	global $theme_name;
	add_meta_box( 'post-meta-boxes', __('FrogsThemes Custom Post options'), 'post_meta_boxes', 'post', 'normal', 'high' );
}

function post_meta_boxes()
{	
	global $post;
	
	?>

	<span class="options1">Homepage Snippet Options</span>

	<table class="form-table">
		<tr>
			<th style="width:240px; padding:13px 10px 10px 10px;">
				<label for="columns">Width of homepage box (column span)</label>
			</th>
			<td>
				<select name="columns" id="columns">
				<?php 
				
				$options = array('One', 'Two', 'Three');
				
				foreach($options as $option)
				{
					?>
					<option <?php if ( htmlentities( get_post_meta( $post->ID, 'columns', true ), ENT_QUOTES ) == $option ) echo ' selected="selected"'; ?>>
						<?php echo $option; ?>
					</option>
					<?php 
				}
				?>
				</select>
				<input type="hidden" name="columns_noncename" id="columns_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
			</td>
		</tr>	
	</table>
	
	<div id="tabsWrap"> 
		
		<span class="tabsChoose">Choose: </span>
		<div id="tabs"> 
			<a id="image-link" <?php if(get_post_meta( $post->ID, 'media_use', true )=='image' || get_post_meta( $post->ID, 'media_use', true )==''){echo 'class="active"';} ?> href="#image-tab">Image Thumbnail</a> 
			<a id="video-link" <?php if(get_post_meta( $post->ID, 'media_use', true )=='video'){echo 'class="active"';} ?> href="#video-tab">Video</a>
		</div>
		
		<div id='image-link-tab' class='tab-div' <?php if(get_post_meta( $post->ID, 'media_use', true )=='video'){echo 'style="display: none;"';} ?>>
			
			<div <?php /*class="useThis"*/ ?>>
				Use featured image in post <input type="radio" class="useThisRadio" name="media_use" value="image" <?php if(get_post_meta( $post->ID, 'media_use', true )=='image' || get_post_meta( $post->ID, 'media_use', true )==''){echo 'checked="checked"';} ?> />
			</div>
			
		</div>
		
		<div id='video-link-tab' class='tab-div' <?php if(get_post_meta( $post->ID, 'media_use', true )=='image' || get_post_meta( $post->ID, 'media_use', true )==''){echo 'style="display: none;"';} ?>>	
			
			<div class="chooseVid">Choose a video hosting website:</div>
			
			<div id="tabsInner"> 
				<a id="vid1-link" <?php if(get_post_meta( $post->ID, 'video_use', true )=='youtube' || get_post_meta( $post->ID, 'video_use', true )==''){echo 'class="active"';} ?> href="#vid-1-tabInner"><img src="<?=get_bloginfo('template_directory');?>/admin/images/youtube.png" alt="YouTube" /></a> 
				<a id="vid2-link" <?php if(get_post_meta( $post->ID, 'video_use', true )=='vimeo'){echo 'class="active"';} ?> href="#vid-2-tabInner"><img src="<?=get_bloginfo('template_directory');?>/admin/images/vimeo.png" alt="Vimeo" /></a> 
				<a id="vid3-link" <?php if(get_post_meta( $post->ID, 'video_use', true )=='yahoo'){echo 'class="active"';} ?> href="#vid-3-tabInner"><img src="<?=get_bloginfo('template_directory');?>/admin/images/yahoo.png" alt="Yahoo!" /></a> 
				<a id="vid4-link" <?php if(get_post_meta( $post->ID, 'video_use', true )=='myspace'){echo 'class="active"';} ?> href="#vid-4-tabInner"><img src="<?=get_bloginfo('template_directory');?>/admin/images/myspace.png" alt="myspace" /></a> 
				<a id="vid5-link" <?php if(get_post_meta( $post->ID, 'video_use', true )=='dailymotion'){echo 'class="active"';} ?> href="#vid-5-tabInner"><img src="<?=get_bloginfo('template_directory');?>/admin/images/dailymotion.png" alt="Daily Motion" /></a> 
				<a id="vid6-link" <?php if(get_post_meta( $post->ID, 'video_use', true )=='revver'){echo 'class="active"';} ?> href="#vid-6-tabInner"><img src="<?=get_bloginfo('template_directory');?>/admin/images/revver.png" alt="Revver" /></a> 
				<a id="vid7-link" <?php if(get_post_meta( $post->ID, 'video_use', true )=='metacafe'){echo 'class="active"';} ?> href="#vid-7-tabInner"><img src="<?=get_bloginfo('template_directory');?>/admin/images/metacafe.png" alt="metacafe" /></a> 
				<a id="vid8-link" <?php if(get_post_meta( $post->ID, 'video_use', true )=='break'){echo 'class="active"';} ?> href="#vid-8-tabInner"><img src="<?=get_bloginfo('template_directory');?>/admin/images/break.png" alt="Break" /></a> 
				<a id="vid9-link" <?php if(get_post_meta( $post->ID, 'video_use', true )=='blip'){echo 'class="active"';} ?> href="#vid-9-tabInner"><img src="<?=get_bloginfo('template_directory');?>/admin/images/blip.png" alt="blip.tv" /></a> 
				<a id="vid10-link" <?php if(get_post_meta( $post->ID, 'video_use', true )=='viddler'){echo 'class="active"';} ?> href="#vid-10-tabInner"><img src="<?=get_bloginfo('template_directory');?>/admin/images/viddler.png" alt="Viddler" /></a>
			</div>
			
			<div id='vid1-link-tabInner' class='tabInner-div' <?php if(get_post_meta( $post->ID, 'video_use', true )=='youtube' || get_post_meta( $post->ID, 'video_use', true )==''){echo 'style="display: block;"';}else{echo 'style="display: none;"';} ?>>
				YouTube Video ID <input type="text" name="youtube_ID" id="youtube_ID" value="<?php echo get_post_meta( $post->ID, 'youtube_ID', true ); ?>" size="30" tabindex="30" />
				<input type="hidden" name="youtube_ID_noncename" id="youtube_ID_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<input type="radio" name="video_use" value="youtube" <?php if(get_post_meta( $post->ID, 'video_use', true )=='youtube'){echo 'checked="checked"';} ?> />
			</div>
			
			<div id='vid2-link-tabInner' class='tabInner-div' <?php if(get_post_meta( $post->ID, 'video_use', true )=='vimeo'){echo 'style="display: block;"';}else{echo 'style="display: none;"';} ?>>
				Vimeo Video ID <input type="text" name="vimeo_ID" id="vimeo_ID" value="<?php echo get_post_meta( $post->ID, 'vimeo_ID', true ); ?>" size="30" tabindex="30" />
				<input type="hidden" name="vimeo_ID_noncename" id="vimeo_ID_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<input type="radio" name="video_use" value="vimeo" <?php if(get_post_meta( $post->ID, 'video_use', true )=='vimeo'){echo 'checked="checked"';} ?> />
			</div>
			
			<div id='vid3-link-tabInner' class='tabInner-div'  <?php if(get_post_meta( $post->ID, 'video_use', true )=='yahoo'){echo 'style="display: block;"';}else{echo 'style="display: none;"';} ?>>
				Yahoo! Video ID <input type="text" name="yahoo_ID" id="yahoo_ID" value="<?php echo get_post_meta( $post->ID, 'yahoo_ID', true ); ?>" size="30" tabindex="30" />
				<input type="hidden" name="yahoo_ID_noncename" id="yahoo_ID_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<input type="radio" name="video_use" value="yahoo" <?php if(get_post_meta( $post->ID, 'video_use', true )=='yahoo'){echo 'checked="checked"';} ?> />
			</div>
			
			<div id='vid4-link-tabInner' class='tabInner-div'  <?php if(get_post_meta( $post->ID, 'video_use', true )=='myspace'){echo 'style="display: block;"';}else{echo 'style="display: none;"';} ?>>
				myspace Video ID <input type="text" name="myspace_ID" id="myspace_ID" value="<?php echo get_post_meta( $post->ID, 'myspace_ID', true ); ?>" size="30" tabindex="30" />
				<input type="hidden" name="myspace_ID_noncename" id="myspace_ID_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<input type="radio" name="video_use" value="myspace" <?php if(get_post_meta( $post->ID, 'video_use', true )=='myspace'){echo 'checked="checked"';} ?> />
			</div>
			
			<div id='vid5-link-tabInner' class='tabInner-div'  <?php if(get_post_meta( $post->ID, 'video_use', true )=='dailymotion'){echo 'style="display: block;"';}else{echo 'style="display: none;"';} ?>>
				Daily Motion Video ID <input type="text" name="dailymotion_ID" id="dailymotion_ID" value="<?php echo get_post_meta( $post->ID, 'dailymotion_ID', true ); ?>" size="30" tabindex="30" />
				<input type="hidden" name="dailymotion_ID_noncename" id="dailymotion_ID_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<input type="radio" name="video_use" value="dailymotion" <?php if(get_post_meta( $post->ID, 'video_use', true )=='dailymotion'){echo 'checked="checked"';} ?> />
			</div>
			
			<div id='vid6-link-tabInner' class='tabInner-div'  <?php if(get_post_meta( $post->ID, 'video_use', true )=='revver'){echo 'style="display: block;"';}else{echo 'style="display: none;"';} ?>>
				Revver Video ID <input type="text" name="revver_ID" id="revver_ID" value="<?php echo get_post_meta( $post->ID, 'revver_ID', true ); ?>" size="30" tabindex="30" />
				<input type="hidden" name="revver_ID_noncename" id="revver_ID_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<input type="radio" name="video_use" value="revver" <?php if(get_post_meta( $post->ID, 'video_use', true )=='revver'){echo 'checked="checked"';} ?> />
			</div>
			
			<div id='vid7-link-tabInner' class='tabInner-div'  <?php if(get_post_meta( $post->ID, 'video_use', true )=='metacafe'){echo 'style="display: block;"';}else{echo 'style="display: none;"';} ?>>
				metacafe Video ID <input type="text" name="metacafe_ID" id="metacafe_ID" value="<?php echo get_post_meta( $post->ID, 'metacafe_ID', true ); ?>" size="30" tabindex="30" />
				<input type="hidden" name="metacafe_ID_noncename" id="metacafe_ID_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<input type="radio" name="video_use" value="metacafe" <?php if(get_post_meta( $post->ID, 'video_use', true )=='metacafe'){echo 'checked="checked"';} ?> />
			</div>
			
			<div id='vid8-link-tabInner' class='tabInner-div'  <?php if(get_post_meta( $post->ID, 'video_use', true )=='break'){echo 'style="display: block;"';}else{echo 'style="display: none;"';} ?>>
				Break Video ID <input type="text" name="break_ID" id="break_ID" value="<?php echo get_post_meta( $post->ID, 'break_ID', true ); ?>" size="30" tabindex="30" />
				<input type="hidden" name="break_ID_noncename" id="break_ID_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<input type="radio" name="video_use" value="break" <?php if(get_post_meta( $post->ID, 'video_use', true )=='break'){echo 'checked="checked"';} ?> />
			</div>
			
			<div id='vid9-link-tabInner' class='tabInner-div'  <?php if(get_post_meta( $post->ID, 'video_use', true )=='blip'){echo 'style="display: block;"';}else{echo 'style="display: none;"';} ?>>
				blip.tv Video ID <input type="text" name="blip_ID" id="blip_ID" value="<?php echo get_post_meta( $post->ID, 'blip_ID', true ); ?>" size="30" tabindex="30" />
				<input type="hidden" name="blip_ID_noncename" id="blip_ID_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<input type="radio" name="video_use" value="blip" <?php if(get_post_meta( $post->ID, 'video_use', true )=='blip'){echo 'checked="checked"';} ?> />
			</div>
			
			<div id='vid10-link-tabInner' class='tabInner-div'  <?php if(get_post_meta( $post->ID, 'video_use', true )=='viddler'){echo 'style="display: block;"';}else{echo 'style="display: none;"';} ?>>
				Viddler Video ID <input type="text" name="viddler_ID" id="viddler_ID" value="<?php echo get_post_meta( $post->ID, 'viddler_ID', true ); ?>" size="30" tabindex="30" />
				<input type="hidden" name="viddler_ID_noncename" id="viddler_ID_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<input type="radio" name="video_use" value="viddler" <?php if(get_post_meta( $post->ID, 'video_use', true )=='viddler'){echo 'checked="checked"';} ?> />
			</div>
			
			<div class="useThis">
				Use Video in post <input type="radio" name="media_use" class="useThisRadio" value="video" <?php if(get_post_meta( $post->ID, 'media_use', true )=='video'){echo 'checked="checked"';} ?> />
			</div>
			
		</div>
	
	</div>
	
	<span class="options2">Post Page Options</span>
	
	<table class="form-table">
		<?php
		frog_pt_inner_custom_box();
		?>
	</table>
	<?php
}


/**
 * Loops through each meta box's set of variables.
 * Saves them to the database as custom fields.
 *
 */
function frog_save_meta_data( $post_id )
{	
	global $post;
	
	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
  	
	// to do anything
  	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
	{
    	return $post_id;
  	}
	
	$postOptions = array('columns', 'youtube_ID', 'vimeo_ID', 'yahoo_ID', 'myspace_ID', 'dailymotion_ID', 'revver_ID', 'metacafe_ID', 'break_ID', 'blip_ID', 'viddler_ID', 'video_use', 'media_use');
	
	foreach($postOptions as $optionName)
	{
		if($_POST[$optionName] != '')
		{
			if ( get_post_meta( $post_id, $optionName) == '' )
			{
				add_post_meta( $post_id, $optionName, stripslashes($_POST[$optionName]), true );
			}
			elseif (stripslashes($_POST[$optionName]) != get_post_meta( $post_id, $optionName, true ) )
			{
				update_post_meta( $post_id, $optionName, stripslashes($_POST[$optionName]));
			}
			/*
			elseif (stripslashes($_POST[$optionName]) == '' )
			{
				delete_post_meta( $post_id, $optionName, get_post_meta( $post_id, $optionName, true ) );
			}
			*/
		}
	
	}
}


//	This function scans the template files of the active theme, 
//	and returns an array of [Template Name => {file}.php]
if(!function_exists('frog_get_post_templates')) 
{
	function frog_get_post_templates() {
		
		$themes = get_themes();
		$theme = get_current_theme();
		$templates = $themes[$theme]['Template Files'];
		$post_templates = array();
	
		$base = array(trailingslashit(get_template_directory()), trailingslashit(get_stylesheet_directory()));
		
		foreach ((array)$templates as $template)
		{
			$template = WP_CONTENT_DIR . str_replace(WP_CONTENT_DIR, '', $template); 
			$basename = str_replace($base, '', $template);
	
			// don't allow template files in subdirectories
			if (false !== strpos($basename, '/'))
				continue;
	
			$template_data = implode('', file( $template ));
			
			$name = '';
			if (preg_match( '|Single Post Template:(.*)$|mi', $template_data, $name))
				$name = _cleanup_header_comment($name[1]);
	
			if (!empty($name)) 
			{
				if(basename($template) != basename(__FILE__))
					$post_templates[trim($name)] = $basename;
			}
		}
		return $post_templates;
	
	}
}

//	build the dropdown items
if(!function_exists('frog_post_templates_dropdown')) 
{
	function frog_post_templates_dropdown() {
		
		global $post;
		$post_templates = frog_get_post_templates();
		
		//loop through templates, make them options
		foreach ($post_templates as $template_name => $template_file) 
		{ 
			if ($template_file == get_post_meta($post->ID, '_wp_post_template', true)) 
			{ 
				$selected = ' selected="selected"'; } else { $selected = ''; 
			}
			$opt = '<option value="' . $template_file . '"' . $selected . '>' . $template_name . '</option>';
			echo $opt;
		}
	}
}

//	Filter the single template value, and replace it with
//	the template chosen by the user, if they chose one.
add_filter('single_template', 'frog_get_post_template');
if(!function_exists('frog_get_post_template'))
{
	function frog_get_post_template($template)
	{	
		global $post;
		
		$custom_field = get_post_meta($post->ID, '_wp_post_template', true);
		
		if(!empty($custom_field) && file_exists(TEMPLATEPATH . "/{$custom_field}")) 
		{ 
			$template = TEMPLATEPATH . "/{$custom_field}"; 
		}
		
		return $template;
	}
}

//	Everything below this is for adding the extra box
//	to the post edit screen so the user can choose a template

//	Adds a custom section to the Post edit screen
add_action('admin_menu', 'frog_pt_add_custom_box');
function frog_pt_add_custom_box() 
{
	if(frog_get_post_templates() && function_exists( 'add_meta_box' )) 
	{
		//add_meta_box( 'pt_post_templates', __( 'Single Post Template', 'pt' ), 'frog_pt_inner_custom_box', 'post', 'normal', 'high' ); //add the boxes under the post
	}
}
   
//	Prints the inner fields for the custom post/page section
function frog_pt_inner_custom_box()
{	
	global $post;
	
	// Use nonce for verification
	echo '<tr>';
	echo '<th style="width:190px; padding:13px 10px 10px 10px;">';
	echo '<label for="post_template">' . __("Choose a template for your post", 'pt' ) . '</label><br />';
	echo '</th>';
	echo '<td>';
	echo '<input type="hidden" name="pt_noncename" id="pt_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	// The actual fields for data entry
	echo '<select name="_wp_post_template" id="post_template" class="dropdown">';
	echo '<option value="">Default</option>';
	frog_post_templates_dropdown(); //get the options
	echo '</select>';
	echo '</td>';
	echo '</tr>';
}

//	When the post is saved, saves our custom data
add_action('save_post', 'frog_pt_save_postdata', 1, 2); // save the custom fields

function frog_pt_save_postdata($post_id, $post)
{
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['pt_noncename'], plugin_basename(__FILE__) )) 
	{
		return $post->ID;
	}

	// Is the user allowed to edit the post or page?
	if ( 'page' == $_POST['post_type'] ) 
	{
		if ( !current_user_can( 'edit_page', $post->ID ))
		return $post->ID;
	} 
	else 
	{
		if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	}

	// OK, we're authenticated: we need to find and save the data
	
	// We'll put the data into an array to make it easier to loop though and save
	$mydata['_wp_post_template'] = $_POST['_wp_post_template'];
	// Add values of $mydata as custom fields
	foreach ($mydata as $key => $value) { //Let's cycle through the $mydata array!
		if( $post->post_type == 'revision' ) return; //don't store custom data twice
		$value = implode(',', (array)$value); //if $value is an array, make it a CSV (unlikely)
		if(get_post_meta($post->ID, $key, FALSE)) { //if the custom field already has a value...
			update_post_meta($post->ID, $key, $value); //...then just update the data
		} else { //if the custom field doesn't have a value...
			add_post_meta($post->ID, $key, $value);//...then add the data
		}
		if(!$value) delete_post_meta($post->ID, $key); //and delete if blank
	}
}
?>