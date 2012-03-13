<?php

/*
*
* Author: FrogsThemes
* File: admin options functions
*
*
*/

/* add options page to the theme admin */
function frogs_add_admin() {
 
	global $themename, $shortname, $options;
	 
	add_menu_page($themename, $themename, 'administrator', basename(__FILE__), 'frogs_admin', get_bloginfo('template_directory').'/images/icon.png');
}

/* initialise scripts and css */
function frogs_add_init() {

	$file_dir=get_bloginfo('template_directory');
	wp_enqueue_style("functions", $file_dir."/admin/admin.css", false, "1.0", "all");
	#wp_enqueue_script("jQuery", $file_dir."/js/jquery.js", false, "1.0");
	wp_enqueue_script("ajax_upload", $file_dir."/admin/ajaxupload/ajaxupload.js", false, "1.0");
	wp_enqueue_script("ft_script", $file_dir."/admin/admin.js", false, "1.0");
}

/* build the admin page */
function frogs_admin() {
 
	global $themename, $shortname, $options;
	$i=0;

	?>
	<script type="text/javascript">
	
	jQuery(document).ready(function(){
		
		jQuery('#message').hide(); // hide notification to start with
		
		//AJAX Upload
		jQuery('.image_upload_button').each(function(){
		
		var clickedObject = jQuery(this);
		var clickedID = jQuery(this).attr('id');	
		new AjaxUpload(clickedID, {
			  action: '<?php echo admin_url("admin-ajax.php"); ?>',
			  name: clickedID, // File upload name
			  data: { // Additional data to send
					action: 'frogs_ajax_submit',
					type: 'upload_image',
					data: clickedID },
			  autoSubmit: true, // Submit file after selection
			  responseType: false,
			  onChange: function(file, extension){},
			  onSubmit: function(file, extension){
					clickedObject.text('Uploading'); // change button text, when user selects file	
					this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
					interval = window.setInterval(function(){
						var text = clickedObject.text();
						if (text.length < 13){	clickedObject.text(text + '.'); }
						else { clickedObject.text('Uploading'); } 
					}, 200);
			  },
			  onComplete: function(file, response) {
			   
			  	window.clearInterval(interval);
				clickedObject.text('Upload Image');	
				this.enable(); // enable upload button
				
				// If there was an error
			  	if(response.search('Upload Error') > -1){
					var buildReturn = '<span class="upload-error">' + response + '</span>';
					jQuery(".upload-error").remove();
					clickedObject.parent().after(buildReturn);
				
				}
				else{
					var buildReturn = '<img class="hide" id="image_'+clickedID+'" src="'+response+'" alt="" />';
					jQuery(".upload-error").remove();
					jQuery("#image_" + clickedID).remove();	
					clickedObject.parent().after(buildReturn);
					jQuery('img#image_'+clickedID).fadeIn();
					clickedObject.next('span').fadeIn();
					clickedObject.parent().prev('input').val(response);
				}
			  }
			});
		
		});
		
		//AJAX Remove (clear option value)
		jQuery('.image_reset_button').click(function(){
		
			var clickedObject = jQuery(this);
			var clickedID = jQuery(this).attr('id');
			var theID = jQuery(this).attr('title');	
			
			var image_to_remove = jQuery('#image_' + theID);
			var button_to_hide = jQuery('#reset_' + theID);
			image_to_remove.fadeOut(500,function(){ jQuery(this).remove(); });
			button_to_hide.fadeOut();
			clickedObject.parent().prev('input').val('');
			
			return false; 
			
		}); 

	
		//Save everything else
		jQuery('#fg_form').submit(function(){
			
			function newValues() {
			  var serializedValues = jQuery("#fg_form").serialize();
			  return serializedValues;
			}
			jQuery(":checkbox, :radio").click(newValues);
			jQuery("select").change(newValues);
			jQuery('.ajax-loading-img').fadeIn();
			var serializedReturn = newValues();
			 
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
		
			var data = {
				type: 'update_options',
				action: 'frogs_ajax_submit',
				data: serializedReturn
			};
			
			jQuery.post(ajax_url, data, function(response) {
				var success = jQuery('#message');
				var loading = jQuery('.ajax-loading-img');
				loading.fadeOut();  
				success.fadeIn();
				window.setTimeout(function(){
				   success.fadeOut();   
				}, 2000);
			});
			
			return false; 
			
		});
	});
	
	</script>	
	
	<div class="wrap ft_wrap">
	
		<h2><?php echo $themename; ?> Settings</h2>

		<iframe src="http://www.frogsthemes.com/adminheaders/FGP/FGP.html" height="170px" width="740px" style="padding:0; margin:0;"></iframe>
		
		<div id="message" class="updated fade" style="position:absolute;"><p><strong><?=$themename;?> settings saved.</strong></p></div>

		<div class="ft_opts">
	
			<form method="post" id="fg_form">
			
			<?php 
			
			foreach ($options as $value) 
			{	
				switch ( $value['type'] ) 
				{	 
					case "close":
						
						?> 
							</div>
						</div>
						<br />
						<?php 
					
					break;
					 
					case "title":
						
						?>
						<p class="instruction">To easily use the <?php echo $themename;?> theme, please use the options below.</p>
						<?php 
						
					break;
					 
					case 'text':
						
						?>
						<div class="ft_input ft_text">
						
							<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
							<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" />
							<small><?php echo $value['desc']; ?></small>
							<div class="clearfix"></div>
						
						</div>
					<?php
					break;
					 
					case 'textarea':
						
						?>
						<div class="ft_input ft_textarea">
							
							<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
							<textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea>
							<small><?php echo $value['desc']; ?></small>
							<div class="clearfix"></div>
						 
						 </div>
						<?php
					
					break;
					 
					case 'select':
						
						?>
						<div class="ft_input ft_select">
							
							<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
							<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
							<?php foreach ($value['options'] as $option) { ?>
									<option <?php if (get_settings( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?>
							</select>
							<small><?php echo $value['desc']; ?></small>
							<div class="clearfix"></div>
						
						</div>
						<?php
					
					break;
					 
					case "checkbox":
		
						?>
						<div class="ft_input ft_checkbox">
						
							<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
							<?php 
							if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
							<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
							<small><?php echo $value['desc']; ?></small>					
							<div class="clearfix"></div>
						
						</div>
						<?php 		
					
					break; 
					
					case "section":
					
						$i++;
						?>
						<div class="ft_section">
							
							<div class="ft_title">
							
								<h3><img src="<?php bloginfo('template_directory')?>/admin/images/trans.png" class="inactive" alt="" /><?php echo $value['name']; ?></h3>
								<span class="submit"><input name="save<?php echo $i; ?>" type="submit" value="Save changes" /></span>
								<div class="clearfix"></div>
								
							</div>
						
							<div class="ft_options">
						<?php 
		
					break;
					
					case "image":
					
						?>
						
						<div class="ft_input ft_text">						
							
							<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
							
							<div class="ft_image">
							
								<input type="text" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" name="<?php echo $value['id']; ?>">
								
								<div class="upload_button_div">
									<span id="<?php echo $value['id']; ?>" class="button image_upload_button">Upload Image</span>
									<span title="<?php echo $value['id']; ?>" id="reset_<?php echo $value['id']; ?>" class="button image_reset_button hide">Remove</span>
									<?php
									if(stripslashes(get_settings($value['id']))!='')
									{
										?><img class="hide" id="image_<?php echo $value['id']; ?>" src="<?php echo stripslashes(get_settings($value['id'])); ?>" alt="" /><?php
									}
									?>
								</div>
							
							</div>
							
							<small><?php echo $value['desc']; ?></small>
							<div class="clearfix"></div>
							
						</div>
						
						<?php		
					
					break;
					
					}
				}
				?>
				<input type="hidden" name="action" value="save" />
			</form>
	<?php
}

add_action('wp_ajax_frogs_ajax_submit', 'frogs_ajax_submit');

/* function to handle the image uploading and submitting of the form via ajax */
function frogs_ajax_submit() {
	
	global $wpdb;
	
	// upload image or submit form and update options
	if($_POST['type'] == 'upload_image')
	{	
		$clickedID = $_POST['data'];
		$filename = $_FILES[$clickedID];
		$override['test_form'] = false;
	    $override['action'] = 'wp_handle_upload';
	    $uploaded_file = wp_handle_upload($filename,$override);
		
		$upload_tracking[] = $clickedID;
		update_option($clickedID , $uploaded_file['url']);
		
		if(!empty($uploaded_file['error']))
		{
			echo 'Upload Error: ' . $uploaded_file['error'];
		}
		else
		{
			echo $uploaded_file['url'];
		}
	}
	elseif($_POST['type'] == 'update_options')
	{
		$data = $_POST['data'];
		parse_str($data,$output);
		
		// loop through options to update
		foreach($output as $id => $value)
		{	
			update_option($id,stripslashes($value));
		}
	}
  	die();
}


?>