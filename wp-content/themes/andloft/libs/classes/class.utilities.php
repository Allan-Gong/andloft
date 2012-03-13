<?php
/*
*
* File: Utilities class
*
*/

class Utilities {
	
	var $form_validator_prefix_error = "Please check"; // Default prefix for form validator errors.
	
	function Utilities() {
	
		if(substr_count($_SERVER['PHP_SELF'], "maintain/")>0) { $this->working_dir = "../"; } else { $this->working_dir = ""; }

		foreach ($_REQUEST as $k => $v){
		
			$this->SpamCleanup($v);
		
		}
	
	}
	
	
	/**
 	* UploadFile()
	*
	* Uploads a file (document etc) to the specified path
	* @param array $file The $_FILES array
	* @param string $location The destination folder
	* @param array $limit The filesize limit in bytes
	* @return string The new filename
 	*/
	function UploadFile($file, $location, $limit = NULL) { 
	
		$filename = str_replace(" ", "_", $file['name']);
		$extension = substr($filename, strrpos($filename, "."), (strlen($filename)-strrpos($filename, ".")));
	
		$filename = str_replace($extension, "", $filename);
		$filename = strip_symbols($filename);
		$filename = $filename.$extension;
	
		if(strlen($filename) > 0) { 
		
			## If the file already exists, loop through so many times adding
			## digits to the front of the file until it doesn't exist.
			if(file_exists($location.$filename)) { 
		
				for($i=2; $i<=999; $i++) { 
				
					if(!file_exists($location.$i."_".$filename)) { 
					
						## File doesn't exist, break out of the loop.
						$filename = $i."_".$filename;
						break;
					
					}
			
				}
		
			}
		
			## Check the location exists. If it doesn't, lets create it and CHMOD it.
			if(!is_dir($location)) {
				
				mkdir($location);
				chmod($location, 0777);
				
			}
		
			## Upload the file.
			if(move_uploaded_file($file['tmp_name'], $location.$filename)) { 
			
				return $filename;
		
			}
	
		}
	
		return false;

	}


	/**
 	* CleanFilename()
	*
	* Cleans a string to make it safe for use as a filename
	* @param string $str The string to clean
	* @return string The new, clean string
 	*/
	function CleanFilename($str){
	
		$allowed_chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9','_','-','.');
	
		if(empty($str)) return false;
	
		$len = strlen($str);
	
		// Rebuild filename, removing any dodgy characters
		for ($x = 0; $x<=$len; $x++){	
			if (in_array($str[$x], $allowed_chars)) $new_str .= $str[$x];
		}
	
		return $new_str;

	}
	
	
	/**
 	* UploadImage()
	*
	* Uploads a file (document etc) to the specified path
	* 
	* Usage:
	* $attr = array();
	* $attr['folder'] = "uploads/images";
	* $attr['name'] = "thenewimagename";
	* $attr['thumb_width'] = 100;
	* $attr['width'] = 220;
	* $attr['large_width'] = 450;
	* $attr['thumb_height'] = 100;
	* $attr['height'] = 220;
	* $attr['large_height'] = 450;
	* 
	* $img = UploadImage($_FILES['elementname'], $attr);
	* @param array $images The $_FILES array AND the element name
	* @param string $location The destination folder
	* @param integer $limit The filesize limit in bytes
	* @return string The new filename
 	*/
	function UploadImage($image, $attribute) { 
		
		$dir = str_replace("index.php", "", $_SERVER['PHP_SELF']);
		if(substr_count($dir, "maintain/")) {
			$dir = str_replace("maintain/", "", $dir);
		}
		
		$images = $this->working_dir;
		
		$dir = $images.$attribute['folder']."/";
		
		$filename = $image['name'];
		$tmp_name = $image['tmp_name'];
		
		$filename = substr($filename, strrpos($filename, "."), (strlen($filename)-strrpos($filename, ".")));
		$extension = $filename;		
		
		$filename = $attribute['name'].$extension;
		$filename = strtolower($filename);
		
		## Thumbnail version
		if(!file_exists($dir)) { mkdir($dir); mkdir($dir."thumbs/"); chmod($dir, 0777); chmod($dir."thumbs/", 0777); }
		if(!file_exists($dir."thumbs/")) { mkdir($dir."thumbs/"); chmod($dir, 0777); chmod($dir."thumbs/", 0777); }
		
		## Large version
		if(!file_exists($dir)) { mkdir($dir); mkdir($dir."large/"); chmod($dir, 0777); chmod($dir."large/", 0777); }
		if(!file_exists($dir."large/")) { mkdir($dir."large/"); chmod($dir, 0777); chmod($dir."large/", 0777); }
		
		if(file_exists($dir.$filename)) { 
		
			for($i=1; $i<=999; $i++) { 
			
				if(!file_exists($dir.$i."_".$filename)) {
					$filename = $i."_".$filename; 
					break;
				}
			
			}
		
		}
		
		$whitelist = array(".jpg", ".jpeg", ".png", ".gif");
		
		if(in_array(strtolower($extension), $whitelist)) { 
			
			if(copy($tmp_name, $dir.$filename)) { 
				
				## Make large version
				if($attribute['large_width'] > 0) {
					
					list($width, $height) = getimagesize($dir.$filename);
					if($width > $attribute['large_width']) { 
						$multiplyer = $width/$attribute['large_width'];
						$new_width = $attribute['large_width'];
						$new_height = $height/$multiplyer;
					} else { 
						$new_width = $width;
						$new_height = $height;
					}
					
					if($attribute['large_height'] > 0) { 
					
						if($new_height > $attribute['large_height']) { 
						
							$multiplyer = $new_height/$attribute['large_height'];
							$new_height = $attribute['large_height'];
							$new_width = $new_width/$multiplyer;
						
						}
					
					}
					
					$newimg = imagecreatetruecolor($new_width, $new_height);
					
					if(preg_match("/jpg|jpeg/", strtolower($extension))) {
						$im = imagecreatefromjpeg($dir.$filename);
						imagecopyresampled($newimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						imagejpeg($newimg, $dir."/large/".$filename,100);
					}
					if(preg_match("/png/", strtolower($extension))) {
						$im = imagecreatefrompng($dir.$filename);
						imagecopyresampled($newimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						imagepng($newimg, $dir."/large/".$filename);
					}
					if(preg_match("/gif/", strtolower($extension))) {
						$im = imagecreatefromgif($dir.$filename);
						imagecopyresampled($newimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						imagegif($newimg, $dir."/large/".$filename);
					}
					
					imagedestroy($newimg);
					
				}
				
				## Resize normal version
				if(!isset($attribute['width'])) {
					$image_size = getimagesize($dir.$filename);
					$attribute['width'] = $image_size[0];
				}
							
				if(preg_match("/jpg|jpeg|gif|png/", strtolower($extension))) {
					
					## Resize image (width)
					list($width, $height) = getimagesize($dir.$filename);
					if($width > $attribute['width']) { 
						$multiplyer = $width/$attribute['width'];
						$new_width = $attribute['width'];
						$new_height = $height/$multiplyer;
					} else { 
						$new_width = $width;
						$new_height = $height;
					}
					
					if($attribute['height'] > 0) { 
					
						if($new_height > $attribute['height']) { 
						
							$multiplyer = $new_height/$attribute['height'];
							$new_height = $attribute['height'];
							$new_width = $new_width/$multiplyer;
						
						}
					
					}
					
					$newimg = imagecreatetruecolor($new_width, $new_height);
					
					if(preg_match("/jpg|jpeg/", strtolower($extension))) {
						$im = imagecreatefromjpeg($dir.$filename);
						imagecopyresampled($newimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						imagejpeg($newimg, $dir.$filename,100);
					}
					if(preg_match("/png/", strtolower($extension))) {
						$im = imagecreatefrompng($dir.$filename);
						imagecopyresampled($newimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						imagepng($newimg, $dir.$filename);
					}
					if(preg_match("/gif/", strtolower($extension))) {
						$im = imagecreatefromgif($dir.$filename);
						imagecopyresampled($newimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						imagegif($newimg, $dir.$filename);
					}
					
					## Make thumbnail
					if($attribute['thumb_width'] > 0) {
						
						list($width, $height) = getimagesize($dir.$filename);
						if($width > $attribute['thumb_width']) { 
							$multiplyer = $width/$attribute['thumb_width'];
							$new_width = $attribute['thumb_width'];
							$new_height = $height/$multiplyer;
						} else { 
							$new_width = $width;
							$new_height = $height;
						}
						
						if($attribute['thumb_height'] > 0) { 
						
							if($new_height > $attribute['thumb_height']) { 
							
								$multiplyer = $new_height/$attribute['thumb_height'];
								$new_height = $attribute['thumb_height'];
								$new_width = $new_width/$multiplyer;
							
							}
						
						}
						
						$newimg = imagecreatetruecolor($new_width, $new_height);
						
						if(preg_match("/jpg|jpeg/", strtolower($extension))) {
							$im = imagecreatefromjpeg($dir.$filename);
							imagecopyresampled($newimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
							imagejpeg($newimg, $dir."/thumbs/".$filename,100);
						}
						if(preg_match("/png/", strtolower($extension))) {
							$im = imagecreatefrompng($dir.$filename);
							imagecopyresampled($newimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
							imagepng($newimg, $dir."/thumbs/".$filename);
						}
						if(preg_match("/gif/", strtolower($extension))) {
							$im = imagecreatefromgif($dir.$filename);
							imagecopyresampled($newimg, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
							imagegif($newimg, $dir."/thumbs/".$filename);
						}
						
						imagedestroy($newimg);
						
					}
					
					
				}
				
				return $filename;
				
			}
			
			
		}
		
	}
	
	
	/**
	*
 	* SpamCleanup() checks a string for any occurence of a spam term
	* 
	* @param string $dirty String to be cleaned
	* @return string Cleaned string 
 	*/
	function SpamCleanup($dirty) {

		$hacked=0;
	
		// SPAM TERMS, DON'T USE COLONS FOR MORE UNUSUAL TERMS TO CATCH MORE SPAM
		$spam_terms=array();
		$spam_terms[]='bcc:';
		$spam_terms[]='cc:';
		$spam_terms[]='content-type';
		$spam_terms[]='mime-version';
		$spam_terms[]='content-transfer-encoding';
		$spam_terms[]='[url=';
		$spam_terms[]='[/url]';
		$spam_terms[]='a href';
		$spam_terms[]='viagra';
		$spam_terms[]=' cialis ';
		$spam_terms[]='viagara';
		$spam_terms[]='erection';
		$spam_terms[]='erecxction';
		$spam_terms[]='erexction';
		$spam_terms[]='erextion';
		$spam_terms[]='erexion';
		$spam_terms[]='v1agra';
		$spam_terms[]='c1alis';
		$spam_terms[]='c1al1s';
		$spam_terms[]='ninki';
		$spam_terms[]='erectile dysfunction';
		$spam_terms[]='penis enlargement';
		$spam_terms[]='pen1s enlargement';
		$spam_terms[]='phentermine';
	
		// CHECK INPUT FOR SPAM TERMS
		foreach ($spam_terms as $val) {
	
			if (stristr($dirty,$val)) {
				$hacked++;
				$terms[] = $val;
			}
			
		}
		
		// IF IN MAINTAIN, DISREGARD SPAM CHECK.
		$current_location = $_SERVER['PHP_SELF'];
		if(substr_count($current_location, "/maintain/") > 0) $hacked = 0;
	
		// IF SPAMMED - DIE
		if ($hacked>0) {
			
			foreach ($terms as $term){
			
				$termstring .= $term . ', ';
			
			}
			
			die('<p>This page has been stopped due to a potential spam threat.(' . $termstring . ')</p><p>If you are seeing this message in error please email&nbsp;&nbsp; <span style="font-weight:bold;">technical@<span style="display:none;">**REMOVE**</span>9xb.com</span></p>');
	
		} else {
			$clean = $dirty;
			// RETURN CLEAN VARIABLE READY TO SEND IN EMAIL
			return $clean;
		
		}
	
	}


	/**
 	* csv_download()
	*
	* Generates CSV download from 2-D array
	* NOTE: if you wish to dump a database table into a CSV file, use $Db->table_array($table_name);
	* 
	* @param array $data 2-D array to convert to CSV
	* @param string $download_filename The name you wish to give to the CSV download
	* @param string $button_text [OPTIONAL] Alternative text to use for the CSV download button
	* @return string $html HTML form generated providing link to download - form has CSS class 'csv_download'
 	*/
	function csv_download($data,$download_filename,$button_text='Download CSV &#187;') {
		$html='';
		$html.="\n\n" . '<form action="csv_download.html" method="post" class="csv_download" id="csvdownload" name="csvdownload" >';
		$html.="\n" . '<input type="hidden" name="csv_download_data" value="' . $this->array_to_csv_text($data) . '" />';
		$html.="\n" . '<input type="hidden" name="filename" value="' . $download_filename . '" />';
		$html.="\n" . '<input type="submit" value="' . $button_text . '" />';
		$html.="\n" . '</form>' . "\n\n";
		return $html;
	}
	/**
 	* format_csv_field()
	*
	* Formats a field, as used in csv_download()
	* USES |DOUBLE QUOTE| as markup for double quotes (to get around HTML quotes)
	* 
	* @param string $input Text you wish to reformat
	* @return string $output Reformatted text output
 	*/
	function format_csv_field($input) {
		$output='';
		$output='|DOUBLE QUOTE|' . str_replace(array('"',"\r","\n"),array('|DOUBLE QUOTE||DOUBLE QUOTE|','',' [NEW LINE] '),$input) . '|DOUBLE QUOTE|';
		return $output;
	}
	/**
 	* array_to_csv_text()
	*
	* Rearranges the array into a string used for the CSV download, as used in csv_download()
	* 
	* @param array $input Array to reformat
	* @return string $output Reformatted text output
 	*/
	function array_to_csv_text($input) {
		$output='';
		// INPUT NOT ARRAY
		if (!is_array($input)) {
			$output.='Invalid input - \'Array\' required.';
		}
		// LOOP ARRAY TO GRAB DATA
		else {
			// RETRIEVE EACH LINE
			foreach ($input as $line) {
				// STRING
				if (!is_array($line)) {
					$output.=$this->format_csv_field($line);
				}
				// ARRAY
				else {
					for ($i=0;$i<count($line);$i++) {
						$output.=$this->format_csv_field($line[$i]);
						// SEPARATE FIELDS WHEN NOT LAST IN LINE
						if ($i<(count($line)-1)) {
							$output.=',';
						}
					}
				}
				// END OF LINE
				$output.="\r\n";
			}
		}
		// ESCAPE SINGLE QUOTES AS THESE ARE USED ON HTML FORM
		return $output;
	}

	/**
	*
 	* array_sort() Sorts an array by field
	* 
	* @param array $array Array to be sorted
	* @param array $field Field to sort by
	* @param bool $reverse Reverse it or not?
	* @return string Cleaned string 
 	*/
	function array_sort($array, $field, $reverse=false) {

  	 	$hash = array();
   		foreach($array as $key => $value) {
  	      	$hash[$value[$field].$key] = $value;
   	 	}
    	($reverse)? krsort($hash) : ksort($hash);
    	$array = array();
    	foreach($hash as $value) {
    	    $array []= $value;
    	}
    	return $array;
	
	}

	
	/**
 	* db_date()
 	*
	* Converts an SQL DATETIME string into a given date() format
	* eg. 2007-01-21 becomes 21/01/07
 	*
	* @param $format string PHP date() format required
	* @return $date string DATETIME to convert
	* @return $date string The converted date
 	*/
	function db_date($format, $date){
	
		$timestamp = strtotime($date);
		return date($format, $timestamp);
	
	}
	
	
	/**
 	* date_db()
 	*
	* Converts a dd/mm/yy formatted date into SQL DATETIME
	* eg. 21/01/07 becomes 2007-01-21
 	*
	* @param $date string Date to be converted
	* @return $date string The converted date
 	*/
	function date_db($date){
	
		$parts = explode('/', $date);
		$new = $parts[1] . '/' . $parts[0] . '/' . $parts[2];
		$date = date('Y-m-d', strtotime($new));
		return $date;
	
	}
	
	
	/**
 	* random_char()
 	*
	* Generates a random character and returns it
 	*
	* @return $char string Character
 	*/
	function random_char(){
		
		$charset = "23456789abcdefghjkmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTWXYZ23456789";
		$length = strlen($charset);
		$position = mt_rand(0, $length - 1);
		$char = $charset[$position];
		return $char;
			
	}
	
	
	/**
 	* random_string()
 	*
	* Generates a random string
 	*
	* @param $length int Length of string
	* @return $string string Generated string
 	*/
	function random_string($length){
	
		mt_srand((double)microtime() * 1000000);
		for ($x = 0; $x < $length; $x++){
			$string .= $this->random_char();
		}
		return $string;
		
	}
	
	
	/**
 	* cut_str_length()
 	*
	* Cuts a string and adds ... - checks that it isn't mid word.
 	*
	* @param $string str string to cut
	* @param $max_length int length to cut to
	* @return $string str cut string
 	*/
	function cut_str_length($string, $max_length){
	   if (strlen($string) > $max_length){
		   $string = substr($string, 0, $max_length);
		   $pos = strrpos($string, " ");
		   if($pos === false) {
				   return substr($string, 0, $max_length)."...";
			   }
		   return substr($string, 0, $pos)."...";
	   }else{
		   return $string;
	   }
	}


	/**
 	* is_email()
 	*
	* Checks a given email address against a regex
 	*
	* @param $input string The email address to check
	* @return bool
 	*/
	function is_email ($input) { 

		if(eregi("^[\'+\\./0-9A-Z^_\`a-z{|}~\-]+@[a-zA-Z0-9_\-]+(\.[a-zA-Z0-9_\-]+){1,4}$", $input)) {
			return true;
		} else { 
			return false;
		}
	
	}
	
	
	/**
 	* form_validate()
 	*
	* Does validation on passed fields
 	*
	* @param $req Muti Dimensional Array of required fields
	* @array(array('name'=>'fieldname', 'type'=>'select|text|radiobutton|textarea|checkbox'), 'special'=>'check_email'=>'Y|N', 'check_number'=>'Y|N', 'check_length'=>'Length of field', 'check_chars'=>'Array of Allowed Chars', 'custom_name'=>'fieldname to show user', 'custom_error'=>'Your custom error message');
	* @return array
 	*/
	function form_validate($req) {
	
		$errors = array();
		
		foreach($req as $fields) { // Loop through required fields.
		
			$error_msg = $this->form_validator_prefix_error; // Start the error message string.
			$field_key = $fields['name']; // Get the field name.
			
			// If a custom error message is available for this field, use that.
			if(isset($fields['custom_error']) && empty($fields['custom_error']) == false) { 
				$error_msg = $fields['custom_error'];
			}
			
			// If a custom name for this field is available, use that.
			if(isset($fields['custom_name']) && empty($fields['custom_name']) == false) { 
				$field_key = $fields['custom_name'];
			}
			
			$invalid_length = (empty($_REQUEST[$fields['name']])) ? true : false;
			
			// Switch the type on input.
			switch ($fields['type']) {
				case 'text':
				case 'textarea':
					if((!isset($_REQUEST[$fields['name']])) || $invalid_length)  {
						$errors[] = $error_msg." ".$field_key;
					}
					break;
				case 'select':
					if((!isset($_REQUEST[$fields['name']])) || ($_REQUEST[$fields['name']] == '0') || $invalid_length) { 
						$errors[] = $error_msg." ".$field_key;
					}
					break;
				case 'checkbox':
				case 'radiobutton':
					if((!isset($_REQUEST[$fields['name']])) || $invalid_length) { 
						$errors[] = $error_msg." ".$field_key;
					}
					break;
				default;
					break;
			}
			
			if(!$invalid_length) {
			
				// If the field is marked as an email address, check it's valid.
				if(isset($fields['special']['check_email']) && strtoupper($fields['special']['check_email']) == true) {
					$is_email = $this->is_email($_REQUEST[$fields['name']]);
					if($is_email == false) { // Is the value a valid email address?
						$errors[] = 'Please enter a valid '.$field_key; // It isn't.
					}
				}
				
				// If the field is marked as an integer, check it's valid.
				if(isset($fields['special']['check_number']) && strtoupper($fields['special']['check_number']) == true) {
					if(is_numeric($_REQUEST[$fields['name']]) == false) { // Is the value numeric?
						$errors[] = 'Please only enter numbers in '.$field_key; // It isn't.
					}
				}
				
				// Check for illegal characters.
				if(is_array($fields['special']['check_chars']) && count($fields['special']['check_chars']) > 0) {
				
					$str_len = strlen($_REQUEST[$fields['name']]);
				
					for($i=0; $i<$str_len; $i++) { // Loop through each character.
					
						$bit = substr($_REQUEST[$fields['name']], $i, $str_len); // Get current character.
						if(!in_array($bit, $fields['special']['check_chars'])) { // Check the character is in the allowed list.
							$errors[] = 'You have entered an illegal character in '.$field_key; // It isn't.
						}
					
					}
				
				}
				
				// Check if the field is of a certain length.
				if(isset($fields['special']['check_length']) && $fields['special']['check_length'] > 0) {
					if(strlen($_REQUEST[$fields['name']]) < $fields['special']['check_length']) { // Is the string long enough?
						$errors[] = 'Please check the length of '.$field_key; // It isn't.
					}
				}
			
			}
		
		}
		
		return $errors;
	
	}

}

?>