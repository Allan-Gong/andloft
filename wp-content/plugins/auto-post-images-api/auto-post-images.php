<?PHP
/*
Plugin Name: Auto Post Images (API)
Plugin URI: http://www.karthikeyankarunanidhi.com
Version: 3.1.2
Author: <a href="http://www.karthikeyankarunanidhi.com">Karthikeyan Karunanidhi</a>
Description: Automatically adds images to posts without having to edit the post.

Copyright Karthikeyan Karunanidhi (email : wordpress [a t ] karunanidhi DOT com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	define ("API_RANDOM", 1);
	define ("API_POSTSLUG", 2);
	define ("API_POSTID", 3);
	define ("API_CATID", 4);
	define ("API_CATSLUG", 5);
	define ("API_TAGNAME", 6);

	if (!class_exists("KKAutoPostImages")) {

		require_once (dirname(__FILE__) . '/php/Filesystem.class.php');
		require_once (dirname(__FILE__) . '/php/ThumbCreator.class.php');

		/**
		* Auto Post Images (API) class
		*
		* Methods and variables for the Auto Post Images (API) plugin. Inserts images automatically into blog posts based on their POSTID
		*
		* @author Karthikeyan Karunanidhi <wordpress@karunanidhi.com>
		* @access public
		* @copyright GNU General Public License.
		* @package com.karunanidhi.karthikeyan
		*/
		class KKAutoPostImages {

			/**
			* The ID of the blog post. Set to $post->ID using global $post
			*/
			var $iPostId = FALSE;

			/**
			* The directory path to the images folder.
			*/
			var $sImageDirPath = '';

			/**
			* The directory path to the images folder.
			*/
			var $sImageThumbnailDirPath = '';

			/**
			* The directory path to the images folder.
			*/
			var $sImageCacheDir = '';

			/**
			* The URL to the images folder.
			*/
			var $sImageDirUrl = '';

			/**
			* The URL to the thumbnail images folder.
			*/
			var $sImageThumbnailDirUrl = '';

			/**
			* The name of the default image that is displayed on a blog post when images are not found for that blog post
			*/
			var $sDefaultImageName = 'noimage.jpg';

			/**
			* The URL of the default image that is displayed on a blog post when images are not found for that blog post
			*/
			var $sDefaultImageUrl = '';

			/**
			* Unique name to use for the plugin settings screen
			*/
			var $sAdminOptionsName = 'KK_API_ADMIN_OPTIONS';

			/**
			* Width of the thumbnails
			*/
			var $iThumbnailWidth = 100;

			/**
			* Style class to apply to images
			*/
			var $sImageClass = '';

			/**
			* Style class to apply the the image container
			*/
			var $sContainerClass = '';

			/**
			* Type of search to use when searching for images
			*/
			var $iSearchType = API_POSTID;

			/**
			* Type of search to use when searching for images
			*/
			var $iRandomCount = 1;

			/**
			* The length of the excerpt text
			*/
			var $iExcerptLength = 255;

			/**
			* Text to use to link to the full article when excerpt more is enabled
			*/
			var $sExcerptLinkText = 'more...';

			/**
			* Remove html tags from the post content when generating excerpts
			*/
			var $bExcerptStripTags = 'NO';

			/**
			* Enable for disable excerpt generation
			*/
			var $bEnableExcerpt = 'NO';

			/**
			* Plugin settings
			*/
			var $aAdminOptions = array (
											"bDisplayDefaultImage" => 'YES',
											"bUseThumbnails" => 'YES',
											"bSummaryView" => 'YES',
											"sImageDirPath" => '',
											"sImageThumbnailDirPath" => '',
											"sImageDirUrl" => '',
											"sImageThumbnailDirUrl" => '',
											"sImageCacheDir" => '',
											"sRegEx" => "^api__POSTID__[^0-9]+",
											"bOptmizeImagesForSearch" => 'NO',
											"sImageClass" => '',
											"sContainerClass" => '',
											"iThumbnailWidth" => 100,
											"iSearchType" => API_POSTID,
											"iRandomCount" => 1,
											"iExcerptLength" => 255,
											"sExcerptLinkText" => 'more...',
											"bExcerptStripTags" => 'NO',
											"bEnableExcerpt" => 'NO'
										);
			/**
			* Regular expression to use when searching for images associated with a post.
			* Must have __POSTID__ somewhere in the expression which will be replaced with
			*  the postid before searching for images
			*/
			var $sRegEx = "^api__POSTID__[^0-9]+";

			/**
			* Default regular expression to use when searching for images associated with a post.
			*/
			var $sDefaultRegEx = "^api__POSTID__[^0-9]+";

			/**
			* Default regular expression to use when renaming images for SEO
			*/
			var $sImageSEOPrefix = "sapi";
			var $sImageSEOPattern = '';
			var $sImageSEOPatternMatched = '';

			/**
			* Constructor. Calls the init method
			*/
			function KKAutoPostImages () {

				$this->sImageSEOPattern = '^' . $this->sImageSEOPrefix . '[0-9]+';
				$this->sImageSEOPatternMatched = '^' . $this->sImageSEOPrefix . '([0-9]+)';

				$this->init();

				//echo '<pre>'.print_r($this->aAdminOptions, TRUE).'</pre>';

				$this->sImageDirPath = ( empty($this->aAdminOptions['sImageDirPath']) || !is_dir($this->aAdminOptions['sImageDirPath']) ) ? FALSE : $this->aAdminOptions['sImageDirPath'];

				$this->sImageThumbnailDirPath = ( empty($this->aAdminOptions['sImageThumbnailDirPath']) || !is_dir($this->aAdminOptions['sImageThumbnailDirPath']) ) ? FALSE : $this->aAdminOptions['sImageThumbnailDirPath'];

				$this->sImageCacheDir = ( empty($this->aAdminOptions['sImageCacheDir']) || !is_dir($this->aAdminOptions['sImageCacheDir']) ) ? FALSE : $this->aAdminOptions['sImageCacheDir'];

				$this->sImageDirUrl = ( empty($this->aAdminOptions['sImageDirUrl']) ) ? get_bloginfo('wpurl') . '/wp-content/plugins/auto-post-images-api/images/' : $this->aAdminOptions['sImageDirUrl'];
				$this->sImageThumbnailDirUrl = ( empty($this->aAdminOptions['sImageThumbnailDirUrl']) ) ? get_bloginfo('wpurl') . '/wp-content/plugins/auto-post-images-api/images/thumbs/' : $this->aAdminOptions['sImageThumbnailDirUrl'];

				//$this->sImageDirPath = dirname(__FILE__) . '/images/';
				//$this->sImageThumbnailDirPath = dirname(__FILE__) . '/images/thumbs/';

				//$this->sImageDirUrl = get_bloginfo('wpurl') . '/wp-content/plugins/auto-post-images-api/images/';
				//$this->sImageThumbnailDirUrl = get_bloginfo('wpurl') . '/wp-content/plugins/auto-post-images-api/images/thumbs/';

				$this->sDefaultImageUrl = $this->sImageDirUrl . $this->sDefaultImageName;

				$this->sImageClass = $this->aAdminOptions['sImageClass'];
				$this->sContainerClass = $this->aAdminOptions['sContainerClass'];

				$this->iThumbnailWidth = ( empty($this->aAdminOptions['iThumbnailWidth']) || !is_numeric($this->aAdminOptions['iThumbnailWidth']) ) ? 100 : $this->aAdminOptions['iThumbnailWidth'];

				if ($this->aAdminOptions['bOptmizeImagesForSearch'] == 'YES') {

					$this->sRegEx = '^' . $this->sImageSEOPrefix . '__POSTID__'; //[^\n]*\.[^\n]+$';

				} else {

					$this->sRegEx = $this->aAdminOptions['sRegEx'];
				}

				$this->iSearchType = $this->aAdminOptions['iSearchType'];
				$this->iRandomCount = $this->aAdminOptions['iRandomCount'];


				$this->iExcerptLength = ( empty($this->aAdminOptions['iExcerptLength']) ) ? 255 : $this->aAdminOptions['iExcerptLength'];
				$this->sExcerptLinkText = ( empty($this->aAdminOptions['sExcerptLinkText']) ) ? 'more...' : $this->aAdminOptions['sExcerptLinkText'];
				$this->bExcerptStripTags = ( empty($this->aAdminOptions['bExcerptStripTags']) ) ? 'NO' : $this->aAdminOptions['bExcerptStripTags'];
				$this->bEnableExcerpt = ( empty($this->aAdminOptions['bEnableExcerpt']) ) ? 'NO' : $this->aAdminOptions['bEnableExcerpt'];

				//echo '<pre>'.print_r($this, TRUE).'</pre>';
			}

			/**
			* Calls the getAdminOptions method
			*/
			function init () {

				$this->getAdminOptions();
			}

			/**
			* Gets the plugin settings from the database and initializes the plugin settings
			*
			* @return array The plugins setting
			*/
			function getAdminOptions () {

				$mAdminOptions = get_option($this->sAdminOptionsName);

				//echo '<pre>'.print_r($mAdminOptions, TRUE).'</pre>';

				if( !empty($mAdminOptions) ) {

					foreach( $mAdminOptions as $sKey => $mOption ) {

						$this->aAdminOptions[$sKey] = $mOption;
					}
				}

				update_option($this->sAdminOptionsName, $this->aAdminOptions);

				//echo '<pre>'.print_r($this->aAdminOptions, TRUE).'</pre>';

				return $this->aAdminOptions;
			}

			/**
			* The meat of the plugin. Searches for images for a blog post and inserts the image into the blog posts content
			*
			* @param string $sContent The content of the blog post
			* @return string The content of the blog post with the image code
			*/
			function showImagesByPostId ($sContent) {

				$bAPIExcerpt = FALSE;

				if(!is_single() && !is_page()) {

					$bAPIExcerpt = TRUE;
				}

				// If it is not a post then return the contents unmodified
				//
				if(is_page()) {

					$this->getExcerptText($bAPIExcerpt, $this->bEnableExcerpt, $this->iExcerptLength, $this->bExcerptStripTags, $this->sExcerptLinkText, $this->iPostId, $sContent);
					return $sContent;
				}

				if ($this->sImageDirPath === FALSE || $this->sImageThumbnailDirPath === FALSE) {

					$this->getExcerptText($bAPIExcerpt, $this->bEnableExcerpt, $this->iExcerptLength, $this->bExcerptStripTags, $this->sExcerptLinkText, $this->iPostId, $sContent);
					return $sContent;
				}


				global $post;
				$this->iPostId = $post->ID;
				$sHtml = '';
				$bUseThumbnails = ( isset($this->aAdminOptions['bUseThumbnails']) ) ? $this->aAdminOptions['bUseThumbnails'] : FALSE;
				$bUsingDefaultImage = FALSE;

				if( !is_numeric($this->iPostId) ) {

					$this->iPostId = FALSE;
				}

				//
				// If SEO image optimization is turned ON then check the image name and rename if required
				//
				if ($this->aAdminOptions['bOptmizeImagesForSearch'] == 'YES') {

					$this->renameImagesForSEO();
				}

				$aImageFiles = array();

				switch($this->iSearchType) {

					case API_RANDOM :

						$aImageFiles = $this->getRandomImagesForThisPost($this->iRandomCount);
						break;

					case API_POSTSLUG :

						$sSlugPattern = "^{$post->post_name}[^\.]*\.[a-z]+";
						$aImageFiles = $this->getImagesForThisPostByPostSlug($this->iPostId, $sSlugPattern);
						break;

					case API_CATID :

						$aImageFiles = $this->getImagesForThisPostByCatID();
						break;

					case API_CATSLUG :

						$aImageFiles = $this->getImagesForThisPostByCatSLUG();
						break;

					case API_TAGNAME :

						$aImageFiles = $this->getImagesForThisPostByTagNAME();
						break;

					case API_POSTID :
					default:

						$aImageFiles = $this->getImagesForThisPost($this->iPostId);
						break;
				}

				sort($aImageFiles);

				//
				// If a post is a displayed as a list of posts then show only one image
				//
				if ( $this->aAdminOptions['bSummaryView'] == 'YES' && is_single() != TRUE ) {

					if( count($aImageFiles) > 0 ) {

						$aImageFiles = array( $aImageFiles[0] );
					}
				}

				$aImageUrls = array();

				//echo '<pre>'.print_r($aImageFiles, TRUE).'</pre>';

				foreach($aImageFiles as $sKey => $sImageName) {

					$sImageDirPath = $this->sImageDirPath . '/' . $sImageName;

					if( !is_file($sImageDirPath) ) {

						unset($aImageFiles[$sKey]);
						continue;
					}

					$aImageUrls[$sImageName] = $this->sImageDirUrl . $sImageName;
					$aImageFiles[$sImageName] = $sImageName;
					unset($aImageFiles[$sKey]);
				}

				// If no images were found for the post and the user wants to display default images
				//  then add the default image to the list of images to display
				//
				// Else if the user does not want to display default images then return the post contents unchanged
				//
				if( empty($aImageFiles) && $this->aAdminOptions['bDisplayDefaultImage'] == 'YES') {

					$aImageFiles[$this->sDefaultImageName] = $this->sDefaultImageName;
					$aImageUrls[$this->sDefaultImageName] = $this->sDefaultImageUrl;
					$bUsingDefaultImage = TRUE;

				} else if( empty($aImageFiles) && $this->aAdminOptions['bDisplayDefaultImage'] == 'NO') {

					$this->getExcerptText($bAPIExcerpt, $this->bEnableExcerpt, $this->iExcerptLength, $this->bExcerptStripTags, $this->sExcerptLinkText, $this->iPostId, $sContent);
					return $sContent;
				}

				$sHtml .= '<div class="kkautopostimage '.$this->sContainerClass.'">';

				foreach($aImageUrls as $sImageName => $sImageUrl) {

					$sThumbnailUrl = $sImageUrl;
					$sThumbSearchName = 'Default';

					if ( $bUsingDefaultImage === FALSE ) {

						// Setup thumbnail image
						//
						if( $bUseThumbnails === 'YES' ) {

							$sThumbnailName = 'thumb_' . $sImageName;
							$sThumbnailPath = $this->sImageThumbnailDirPath . '/' . $sThumbnailName;
							$sImageFilePath = $this->sImageDirPath . '/' . $sImageName;
							$sThumbnailUrl = $this->sImageThumbnailDirUrl . $sThumbnailName;

							if( FALSE === is_file($sThumbnailPath) ) {

								//
								// Create the thumbnail first
								//

								$oThumbs = new ThumbCreator();

								$bStatus = $oThumbs->createThumbnail($this->sImageDirPath . '/', $sImageFilePath, $sThumbnailPath, $this->iThumbnailWidth);

								if( $bStatus === FALSE ) {

									$sThumbnailUrl = $sImageUrl;
								}
							}
						}
					}

					$sThumbSearchName = $post->post_name;
					$sThumbSearchTags = str_replace('-', ' ', $sThumbSearchName);

					//echo '<pre>', print_r($post, TRUE), '</pre>';

					if( $bUseThumbnails === 'YES' ) {

						$sHtml .= "<a href=\"{$sImageUrl}\"  rel=\"ibox\" title=\"&nbsp;\"><img src=\"{$sThumbnailUrl}\" class=\"{$this->sImageClass}\" alt=\"{$sThumbSearchTags}\" title=\"".ucwords($post->post_title)."\" /></a><br />";

					} else {

						$sHtml .= "<img src=\"{$sThumbnailUrl}\" class=\"{$this->sImageClass}\" alt=\"{$sThumbSearchTags}\" title=\"".ucwords($post->post_title)."\" /><br />";
					}
				}

				$sHtml .= '</div>';

				$this->getExcerptText($bAPIExcerpt, $this->bEnableExcerpt, $this->iExcerptLength, $this->bExcerptStripTags, $this->sExcerptLinkText, $this->iPostId, $sContent);

				return $sHtml . $sContent;
			}

			/**
			* Hooked into 'wp_head' and inserts the css <link> tag into the pages <head>
			*/
			function includeCSS () {

				echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/auto-post-images-api/css/kkimageinpost.css" />' . "\n";
			}

			/**
			* Hooked into 'admin_head' and inserts the css <link> tag into the pages <head>
			*/
			function includeAdminCSS() {
				echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/auto-post-images-api/css/kkapiadmin.css" />' . "\n";
				echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/auto-post-images-api/css/kkapitabs.css" />' . "\n";
			}

			/**
			* Hooked into 'wp_head' and inserts the script <script> tag into the pages <head>
			*/
			function includeJS () {

				echo '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/auto-post-images-api/js/ibox.js"></script>' . "\n";
				echo '<script type="text/javascript">iBox.setPath("js/");</script>' . "\n";
			}

			/**
			* Hooked into 'admin_head' and inserts the script <script> tag intoAPI plugin settings page <head>
			*/
			function includeAdminJS () {

				echo '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/auto-post-images-api/js/tabs.js"></script>' . "\n";
			}

			/**
			* Searches the /wp-content/plugins/auto-post-images-api/images/ folder for images for the given blog post ID and returns the filename
			*
			* @param int $iPostId The ID of the blog POST
			* @return array List of image file names
			*/
			function getImagesForThisPost($iPostId, $sRegEx = FALSE) {

				if(!is_numeric($iPostId)) {

					return $aEmpty = array();
				}

				if($sRegEx === FALSE) {

					$sRegEx = $this->sRegEx;
				}

				$oFileSys = new Filesystem();
				$sPattern = str_replace('__POSTID__', $iPostId, $sRegEx);

				$aImages = $oFileSys->readDir($this->sImageDirPath, $sPattern);

				//echo '<p>Pattern ==', $this->sImageDirPath, '==<br />==', $sPattern, '==</p>';
				//echo '<pre>', print_r($aImages, TRUE), '</pre>';

				return $aImages;
			}

			/**
			* Searches the /wp-content/plugins/auto-post-images-api/images/ folder for images for the given posts category IDs and returns the filename
			*
			* @param int $iPostId The ID of the blog POST
			* @return array List of image file names
			*/
			function getImagesForThisPostByCatID($sRegEx = FALSE) {

				$oFileSys = new Filesystem();

				if($sRegEx === FALSE) {

					$sRegEx = $this->sDefaultRegEx;
				}

				$aImages = array();

				foreach((get_the_category()) as $category) {

					$sPattern = str_replace('__POSTID__', $category->cat_ID, $sRegEx);

					//echo '<pre>', print_r($sRegEx, TRUE), '</pre>';
					//echo '<pre>', print_r($sPattern, TRUE), '</pre>';

					$aImages = array_merge($aImages, $oFileSys->readDir($this->sImageDirPath, $sPattern));
				}

				//echo '<pre>', print_r($aImages, TRUE), '</pre>';

				return $aImages;
			}

			/**
			* Searches the /wp-content/plugins/auto-post-images-api/images/ folder for images for the given posts category IDs and returns the filename
			*
			* @param int $iPostId The ID of the blog POST
			* @return array List of image file names
			*/
			function getImagesForThisPostByCatSLUG($sRegEx = FALSE) {

				$oFileSys = new Filesystem();

				if($sRegEx === FALSE) {

					$sRegEx = $this->sDefaultRegEx;
				}

				$aImages = array();

				foreach((get_the_category()) as $category) {

					$sPattern = str_replace('__POSTID__', $category->category_nicename, $sRegEx);

					//echo '<pre>', print_r($sRegEx, TRUE), '</pre>';
					//echo '<pre>', print_r($sPattern, TRUE), '</pre>';

					$aImages = array_merge($aImages, $oFileSys->readDir($this->sImageDirPath, $sPattern));
				}

				//echo '<pre>', print_r($aImages, TRUE), '</pre>';

				return $aImages;
			}

			/**
			* Searches the /wp-content/plugins/auto-post-images-api/images/ folder for images for the given posts category IDs and returns the filename
			*
			* @param int $iPostId The ID of the blog POST
			* @return array List of image file names
			*/
			function getImagesForThisPostByTagNAME($sRegEx = FALSE) {

				$oFileSys = new Filesystem();

				if($sRegEx === FALSE) {

					$sRegEx = $this->sDefaultRegEx;
				}

				$aImages = array();

				$aTags = get_the_tags();
				//echo '<pre>', print_r($aTags, TRUE), '</pre>';exit;

				if ($aTags) {

					foreach($aTags as $oTag) {

						$sPattern = str_replace('__POSTID__', $oTag->name, $sRegEx);

						//echo '<pre>', print_r($sRegEx, TRUE), '</pre>';
						//echo '<pre>', print_r($sPattern, TRUE), '</pre>';

						$aImages = array_merge($aImages, $oFileSys->readDir($this->sImageDirPath, $sPattern));
					}
				}

				//echo '<pre>', print_r($aImages, TRUE), '</pre>';

				return $aImages;
			}

			/**
			* Searches the /wp-content/plugins/auto-post-images-api/images/ folder for images for the given blog post ID and returns the filename
			*
			* @param int $iPostId The ID of the blog POST
			* @return array List of image file names
			*/
			function getImagesForThisPostByPostSlug($iPostId, $sRegEx = FALSE) {

				if(!is_numeric($iPostId)) {

					return $aEmpty = array();
				}

				if($sRegEx === FALSE) {

					$sRegEx = $this->sRegEx;
				}

				$oFileSys = new Filesystem();
				$sPattern = str_replace('__POSTID__', $iPostId, $sRegEx);

				$aImages = $oFileSys->readDir($this->sImageDirPath, $sPattern);

				//echo '<p>Pattern ==', $this->sImageDirPath, '==<br />==', $sPattern, '==</p>';
				//echo '<pre>', print_r($aImages, TRUE), '</pre>';

				return $aImages;
			}


			/**
			* Searches the /wp-content/plugins/auto-post-images-api/images/ folder for images for the given blog post ID and returns the filename
			*
			* @param int $iPostId The ID of the blog POST
			* @return array List of image file names
			*/
			function getRandomImagesForThisPost($iRandomCount) {

				if(!is_numeric($iRandomCount)) {

					return $aEmpty = array();
				}

				$oFileSys = new Filesystem();

				$aDirImages = $oFileSys->readDir($this->sImageDirPath);

				if($iUnsetKey = array_search($this->sDefaultImageName, $aDirImages)) {

					unset($aDirImages[$iUnsetKey]);
					$aDirImages = array_values($aDirImages);
				}


				//echo '<pre>', print_r($iRandomCount, TRUE), '</pre>';
				//echo '<pre>', print_r($aDirImages, TRUE), '</pre>';

				$aImages = array();

				if(count($aDirImages) <= $iRandomCount) {

					$aImages = $aDirImages;

				} else {

					$iCount = count($aDirImages) - 1;
					$aImageKeys = array();

					for($i = 0; $i < $iRandomCount; $i++) {

						do {

							$iKey = rand(0, $iCount);

						} while ( in_array($iKey, $aImageKeys) );

						$aImageKeys[] = $iKey;

						$aImages[] = $aDirImages[$iKey];
					}
				}

				//echo '<pre>', print_r($aImages, TRUE), '</pre>';

				return $aImages;
			}


			/**
			* Hooked into 'admin_menu' sets-up the plugins setting page handles the settings update
			*/
			function printAdminPage () {

				$sImageDirAlert1 = '';
				$sImageDirAlert2 = '';
				$sThumbnailDirAlert = '';
				$sCacheDirAlert = '';

				$aAdminOptions = $this->getAdminOptions();

				if (isset($_POST['updateAutoPostImagesSettings'])) {

					//echo '<pre>'.print_r($_POST, TRUE).'</pre>';

					if (isset($_POST['AutoPostImagesDisplayDefaultImages'])) {

						$aAdminOptions['bDisplayDefaultImage'] = ( $_POST['AutoPostImagesDisplayDefaultImages'] == 'YES' ) ? 'YES' : 'NO';
					}

					if (isset($_POST['AutoPostImagesUseThumbnails'])) {

						$aAdminOptions['bUseThumbnails'] = ( $_POST['AutoPostImagesUseThumbnails'] == 'YES' ) ? 'YES' : 'NO';
					}

					if (isset($_POST['AutoPostImagesSummaryView'])) {

						$aAdminOptions['bSummaryView'] = ( $_POST['AutoPostImagesSummaryView'] == 'YES' ) ? 'YES' : 'NO';
					}

					if (isset($_POST['AutoPostImagesOptmizeImagesForSearch'])) {

						$aAdminOptions['bOptmizeImagesForSearch'] = ( $_POST['AutoPostImagesOptmizeImagesForSearch'] == 'YES' ) ? 'YES' : 'NO';
					}

					if (isset($_POST['AutoPostImagesImgDir'])) {

						$aAdminOptions['sImageDirPath'] = $_POST['AutoPostImagesImgDir'];
					}

					if (isset($_POST['AutoPostImagesThumbDir'])) {

						$aAdminOptions['sImageThumbnailDirPath'] = $_POST['AutoPostImagesThumbDir'];
					}

					if (isset($_POST['AutoPostImagesImgUrl'])) {

						$aAdminOptions['sImageDirUrl'] = ( !empty($_POST['AutoPostImagesImgUrl']) ) ? $_POST['AutoPostImagesImgUrl'] : $aAdminOptions['sImageDirUrl'];
					}

					if (isset($_POST['AutoPostImagesThumbUrl'])) {

						$aAdminOptions['sImageThumbnailDirUrl'] = ( !empty($_POST['AutoPostImagesThumbUrl']) ) ? $_POST['AutoPostImagesThumbUrl'] : $aAdminOptions['sImageThumbnailDirUrl'];
					}

					if (isset($_POST['AutoPostImagesRegEx'])) {

						$aAdminOptions['sRegEx'] = ( !empty($_POST['AutoPostImagesRegEx']) && strstr($_POST['AutoPostImagesRegEx'], '__POSTID__') ) ? str_replace('\\\\', '\\', trim($_POST['AutoPostImagesRegEx'])) : $aAdminOptions['sRegEx'];
					}

					if (isset($_POST['AutoPostImagesCacheDir'])) {

						$aAdminOptions['sImageCacheDir'] = $_POST['AutoPostImagesCacheDir'];
					}

					if ( !ereg("__POSTID__", $aAdminOptions['sRegEx']) ) {

						$aAdminOptions['sRegEx'] = $this->sDefaultRegEx;
					}

					if (isset($_POST['AutoPostImagesImageClass'])) {

						$aAdminOptions['sImageClass'] = $_POST['AutoPostImagesImageClass'];
					}

					if (isset($_POST['AutoPostImagesContainerClass'])) {

						$aAdminOptions['sContainerClass'] = $_POST['AutoPostImagesContainerClass'];
					}

					if (isset($_POST['AutoPostImagesThumbnailWidth'])) {

						$aAdminOptions['iThumbnailWidth'] = ( !empty($_POST['AutoPostImagesThumbnailWidth']) ) ? $_POST['AutoPostImagesThumbnailWidth'] : $aAdminOptions['iThumbnailWidth'];
					}

					if ( !ereg("/$", $aAdminOptions['sImageDirUrl']) ) {

						$aAdminOptions['sImageDirUrl'] = $aAdminOptions['sImageDirUrl'] . '/';
					}

					if ( !ereg("/$", $aAdminOptions['sImageThumbnailDirUrl']) ) {

						$aAdminOptions['sImageThumbnailDirUrl'] = $aAdminOptions['sImageThumbnailDirUrl'] . '/';
					}

					if (isset($_POST['AutoPostImagesSearchType'])) {

						switch($_POST['AutoPostImagesSearchType']) {

							case 'RANDOM':

								$aAdminOptions['iSearchType'] = API_RANDOM;
								break;

							case 'POSTSLUG':

								$aAdminOptions['iSearchType'] = API_POSTSLUG;
								break;

							case 'CATID':

								$aAdminOptions['iSearchType'] = API_CATID;
								break;

							case 'CATSLUG':

								$aAdminOptions['iSearchType'] = API_CATSLUG;
								break;

							case 'TAGNAME':

								$aAdminOptions['iSearchType'] = API_TAGNAME;
								break;

							case 'POSTID':
							default:

								$aAdminOptions['iSearchType'] = API_POSTID;
								break;
						}
					}

					if (isset($_POST['AutoPostImagesRandomCount'])) {

						$aAdminOptions['iRandomCount'] = $_POST['AutoPostImagesRandomCount'];
					}

					if (isset($_POST['AutoPostImagesImgUrl'])) {

						$aAdminOptions['iExcerptLength'] = ( !empty($_POST['AutoPostImagesExcerptLength']) ) ? $_POST['AutoPostImagesExcerptLength'] : $aAdminOptions['iExcerptLength'];
					}

					if (isset($_POST['AutoPostImagesImgUrl'])) {

						$aAdminOptions['sExcerptLinkText'] = ( !empty($_POST['AutoPostImagesExcerptLinkText']) ) ? $_POST['AutoPostImagesExcerptLinkText'] : $aAdminOptions['sExcerptLinkText'];
					}

					if (isset($_POST['AutoPostImagesExcerptStripTags'])) {

						$aAdminOptions['bExcerptStripTags'] = ( $_POST['AutoPostImagesExcerptStripTags'] == 'YES' ) ? 'YES' : 'NO';
					}

					if (isset($_POST['AutoPostImagesEnableExcerpt'])) {

						$aAdminOptions['bEnableExcerpt'] = ( $_POST['AutoPostImagesEnableExcerpt'] == 'YES' ) ? 'YES' : 'NO';
					}

					$aAdminOptions = array_map('stripslashes_deep', $aAdminOptions);

					update_option($this->sAdminOptionsName, $aAdminOptions);

					if (isset($_POST['AutoPostImagesRerun']) && $_POST['AutoPostImagesRerun'] == 'rerun') {

						//echo '<pre>', print_r($_POST, TRUE), '</pre>';

						$sRerunStatus = $this->rerunImagesProcesses();

						if($bRerunStatus !== FALSE) {

							echo $sRerunStatus;
						}
					}

					echo '<div class="updated"><p><strong>' . __("Settings Updated.", "KKAutoPostImages") . '</strong></p></div>';
				}

				$sRequestUri = $_SERVER["REQUEST_URI"];

				$sUpdateAutoPostImagesSubmitButton = __('Update Settings', 'KKAutoPostImages');

				//
				// Create radio buttons for DisplayDefaultImage admin option
				//
				$sDisplayDefaultImageYesRadio = __(' checked="checked" ', 'KKAutoPostImages');
				$sDisplayDefaultImageNoRadio = '';

				if ( $aAdminOptions['bDisplayDefaultImage'] == 'YES' ) {

					$sDisplayDefaultImageYesRadio = __(' checked="checked" ', 'KKAutoPostImages');
					$sDisplayDefaultImageNoRadio = '';

				} else {

					$sDisplayDefaultImageYesRadio = '';
					$sDisplayDefaultImageNoRadio = __(' checked="checked" ', 'KKAutoPostImages');
				}

				//
				// Create radio buttons for UseThumbnails admin option
				//
				$sUseThumbnailsYesRadio = __(' checked="checked" ', 'KKAutoPostImages');
				$sUseThumbnailsNoRadio = '';

				if ( $aAdminOptions['bUseThumbnails'] == 'YES' ) {

					$sUseThumbnailsYesRadio = __(' checked="checked" ', 'KKAutoPostImages');
					$sUseThumbnailsNoRadio = '';

				} else {

					$sUseThumbnailsYesRadio = '';
					$sUseThumbnailsNoRadio = __(' checked="checked" ', 'KKAutoPostImages');
				}

				//
				// Create radio buttons for SummaryView admin option
				//
				$sSummaryViewYesRadio = __(' checked="checked" ', 'KKAutoPostImages');
				$sSummaryViewNoRadio = '';

				if ( $aAdminOptions['bSummaryView'] == 'YES' ) {

					$sSummaryViewYesRadio = __(' checked="checked" ', 'KKAutoPostImages');
					$sSummaryViewNoRadio = '';

				} else {

					$sSummaryViewYesRadio = '';
					$sSummaryViewNoRadio = __(' checked="checked" ', 'KKAutoPostImages');
				}

				//
				// Create radio buttons for OptmizeImagesForSearch admin option
				//
				$sOptmizeImagesForSearchYesRadio = __(' checked="checked" ', 'KKAutoPostImages');
				$sOptmizeImagesForSearchNoRadio = '';

				if ( $aAdminOptions['bOptmizeImagesForSearch'] == 'YES' ) {

					$sOptmizeImagesForSearchYesRadio = __(' checked="checked" ', 'KKAutoPostImages');
					$sOptmizeImagesForSearchNoRadio = '';

				} else {

					$sOptmizeImagesForSearchYesRadio = '';
					$sOptmizeImagesForSearchNoRadio = __(' checked="checked" ', 'KKAutoPostImages');
				}

				//
				// Create radio buttons for SearchType option
				//
				$sSearchTypeRANDOMRadio = '';
				$sSearchTypePOSTSLUGRadio = '';
				$sSearchTypePOSTIDRadio = '';
				$sSearchTypeCATIDRadio = '';
				$sSearchTypeCATSLUGRadio = '';

				switch($aAdminOptions['iSearchType']) {

					case API_RANDOM:

						$sSearchTypeRANDOMRadio = __(' checked="checked" ', 'KKAutoPostImages');
						break;

					case API_POSTSLUG:

						$sSearchTypePOSTSLUGRadio = __(' checked="checked" ', 'KKAutoPostImages');
						break;

					case API_CATID:

						$sSearchTypeCATIDRadio = __(' checked="checked" ', 'KKAutoPostImages');
						break;

					case API_CATSLUG:

						$sSearchTypeCATSLUGRadio = __(' checked="checked" ', 'KKAutoPostImages');
						break;

					case API_TAGNAME:

						$sSearchTypeTAGNAMERadio = __(' checked="checked" ', 'KKAutoPostImages');
						break;

					case API_POSTID:
					default:

						$sSearchTypePOSTIDRadio = __(' checked="checked" ', 'KKAutoPostImages');
						break;
				}

				if ( $aAdminOptions['bExcerptStripTags'] == 'YES' ) {

					$sExcerptStripTagsYesRadio = __(' checked="checked" ', 'KKAutoPostImages');
					$sExcerptStripTagsNoRadio = '';

				} else {

					$sExcerptStripTagsYesRadio = '';
					$sExcerptStripTagsNoRadio = __(' checked="checked" ', 'KKAutoPostImages');
				}

				if ( $aAdminOptions['bEnableExcerpt'] == 'YES' ) {

					$sEnableExcerptYesRadio = __(' checked="checked" ', 'KKAutoPostImages');
					$sEnableExcerptNoRadio = '';

				} else {

					$sEnableExcerptYesRadio = '';
					$sEnableExcerptNoRadio = __(' checked="checked" ', 'KKAutoPostImages');
				}


				if( strstr($aAdminOptions['sImageDirPath'], 'plugins\auto-post-images-api') || strstr($aAdminOptions['sImageDirPath'], 'plugins/auto-post-images-api') ) {

					$sImageDirAlert1 = '<div class="error"><p><strong>Warning: Your "Image Directory" is inside the plugin directory. You should change it to a folder outside the plugin directory or you will lose your image when you upgrade the plugin.</strong></p></div>';
				}

				if( !is_dir($aAdminOptions['sImageDirPath']) ) {

					$sImageDirAlert2 = '<div class="error"><p><strong>Warning: Directory does not exist or path is incorrect.</strong></p></div>';
				}

				if( !is_dir($aAdminOptions['sImageThumbnailDirPath']) ) {

					$sThumbnailDirAlert = '<div class="error"><p><strong>Warning: Directory does not exist or path is incorrect.</strong></p></div>';
				}

				if( !is_dir($aAdminOptions['sImageCacheDir']) ) {

					$sCacheDirAlert = '<div class="error"><p><strong>Warning: Directory does not exist or path is incorrect.</strong></p></div>';
				}


				echo "
						<div class=\"kkwrap\">

							<form method=\"post\" action=\"{$sRequestUri}\">

								<h2>Auto Post Images (API) Settings</h2>

								{$sImageDirAlert1}

								<div class=\"submit\">
									<input type=\"submit\" name=\"updateAutoPostImagesSettings\" value=\"{$sUpdateAutoPostImagesSubmitButton}\" />
								</div>

								<div class=\"tabber\" style=\"width:90%;\">

								<div class=\"tabbertab\">
									<h2>Search Settings</h2>

									<h3>How do you want to attach images to a POST?</h3>

									<h4> By: </h4>

									<table cellpadding=\"2\" cellspacing=\"2\" style=\"margin-left:20px;\">
										<tr>
											<td width=\"20\">
												<input type=\"radio\" id=\"AutoPostImagesSearchType_POSTID\" name=\"AutoPostImagesSearchType\" value=\"POSTID\" {$sSearchTypePOSTIDRadio} />
											</td>
											<td width=\"100\">
												<label for=\"AutoPostImagesSearchType_POSTID\">Post ID</label>
											</td>
											<td>
												(<a target=\"_blank\" href=\"http://codex.wordpress.org/Function_Reference/get_the_ID\">Numeric ID of the post</a>)
											</td>
										</tr>

										<tr>
											<td>
												<input type=\"radio\" id=\"AutoPostImagesSearchType_POSTSLUG\" name=\"AutoPostImagesSearchType\" value=\"POSTSLUG\" {$sSearchTypePOSTSLUGRadio} />
											</td>
											<td>
												<label for=\"AutoPostImagesSearchType_POSTSLUG\">Post SLUG</label>
											</td>
											<td>
												(<a target=\"_blank\" href=\"http://codex.wordpress.org/Glossary#Post_Slug\">If using Pretty Permalinks, Post Slug is the title of the post</a>)
											</td>
										</tr>

										<tr>
											<td width=\"20\">
												<input type=\"radio\" id=\"AutoPostImagesSearchType_CATID\" name=\"AutoPostImagesSearchType\" value=\"CATID\" {$sSearchTypeCATIDRadio} />
											</td>
											<td width=\"100\">
												<label for=\"AutoPostImagesSearchType_CATID\">Category ID</label>
											</td>
											<td>
												(<a target=\"_blank\" href=\"http://codex.wordpress.org/Function_Reference/get_the_category\">Category ID of the post</a>)
											</td>
										</tr>

										<tr>
											<td width=\"20\">
												<input type=\"radio\" id=\"AutoPostImagesSearchType_CATSLUG\" name=\"AutoPostImagesSearchType\" value=\"CATSLUG\" {$sSearchTypeCATSLUGRadio} />
											</td>
											<td width=\"100\">
												<label for=\"AutoPostImagesSearchType_CATSLUG\">Category SLUG</label>
											</td>
											<td>
												(<a target=\"_blank\" href=\"http://codex.wordpress.org/Function_Reference/get_the_category\">Category SLUG of the post</a>)
											</td>
										</tr>

										<tr>
											<td width=\"20\">
												<input type=\"radio\" id=\"AutoPostImagesSearchType_TAGNAME\" name=\"AutoPostImagesSearchType\" value=\"TAGNAME\" {$sSearchTypeTAGNAMERadio} />
											</td>
											<td width=\"100\">
												<label for=\"AutoPostImagesSearchType_TAGNAME\">Tag Name</label>
											</td>
											<td>
												(<a target=\"_blank\" href=\"http://codex.wordpress.org/Function_Reference/get_the_tags\">Post tags</a>)
											</td>
										</tr>

										<tr>
											<td>
												<input type=\"radio\" id=\"AutoPostImagesSearchType_RANDOM\" name=\"AutoPostImagesSearchType\" value=\"RANDOM\" {$sSearchTypeRANDOMRadio} />
											</td>
											<td>
												<label for=\"AutoPostImagesSearchType_RANDOM\">Random</label>
											</td>
											<td>
												Number of images to return: <input type=\"text\" size=\"4\" id=\"AutoPostImagesRandomCount\" name=\"AutoPostImagesRandomCount\" value=\"$aAdminOptions[iRandomCount]\" />
											</td>
										</tr>

									</table>

								</div>


								<div class=\"tabbertab\">
									<h2>General Settings</h2>

									<h3>Display default image if not image is found?</h3>

									<p>
										<input type=\"radio\" id=\"AutoPostImagesDisplayDefaultImages_yes\" name=\"AutoPostImagesDisplayDefaultImages\" value=\"YES\" {$sDisplayDefaultImageYesRadio} /> <label for=\"AutoPostImagesDisplayDefaultImages_yes\">Yes</label>
										<input type=\"radio\" id=\"AutoPostImagesDisplayDefaultImages_no\" name=\"AutoPostImagesDisplayDefaultImages\" value=\"NO\" {$sDisplayDefaultImageNoRadio} /> <label for=\"AutoPostImagesDisplayDefaultImages_no\">No</label>
									</p>

									<p>Selecting &quot;No&quot; will hide the default &quot;Image coming soon&quot; image if no images are found for the post.</p>

									<h3>Use thumbnails to link to full size images?</h3>

									<p>
										<input type=\"radio\" id=\"AutoPostImagesUseThumbnails_yes\" name=\"AutoPostImagesUseThumbnails\" value=\"YES\" {$sUseThumbnailsYesRadio} /> <label for=\"AutoPostImagesUseThumbnails_yes\">Yes</label>
										<input type=\"radio\" id=\"AutoPostImagesUseThumbnails_no\" name=\"AutoPostImagesUseThumbnails\" value=\"NO\" {$sUseThumbnailsNoRadio} /> <label for=\"AutoPostImagesUseThumbnails_no\">No</label>
									</p>

									<p>Selecting &quot;Yes&quot; will use thumbnail images in the post to link to full size images.</p>

									<h3>Show only one image when post is displayed as part of a list?</h3>

									<p>
										<input type=\"radio\" id=\"AutoPostImagesSummaryView_yes\" name=\"AutoPostImagesSummaryView\" value=\"YES\" {$sSummaryViewYesRadio} /> <label for=\"AutoPostImagesSummaryView_yes\">Yes</label>
										<input type=\"radio\" id=\"AutoPostImagesSummaryView_no\" name=\"AutoPostImagesSummaryView\" value=\"NO\" {$sSummaryViewNoRadio} /> <label for=\"AutoPostImagesSummaryView_no\">No</label>
									</p>

									<p>Selecting &quot;Yes&quot; will show only the first image when the post is part of a list of Posts on the front page, Category page, etc.</p>

									<h3>Customize appearance</h3>

									<p>
										<label class=\"kklabel\">Container style class: </label><input type=\"text\" class=\"kkwidth20\" id=\"AutoPostImagesContainerClass\" name=\"AutoPostImagesContainerClass\" value=\"$aAdminOptions[sContainerClass]\" /> (Class names specified here is applied to the <em>div</em> that contains the images.)
									</p>

									<p>
										<label class=\"kklabel\">Image style class: </label><input type=\"text\" class=\"kkwidth20\" id=\"AutoPostImagesImageClass\" name=\"AutoPostImagesImageClass\" value=\"$aAdminOptions[sImageClass]\" /> (Class names specified here is applied to the images.)
									</p>

									<p>
										<label class=\"kklabel\">Thumbnail width (px): </label><input type=\"text\" class=\"kkwidth20\" id=\"AutoPostImagesThumbnailWidth\" name=\"AutoPostImagesThumbnailWidth\" value=\"$aAdminOptions[iThumbnailWidth]\" /> (Width of the thumbnails generated. The height is automatically calculated to maintain the aspect ratio. Changes are applied to any new thumbnails generated. If you wish to apply the new width to existing thumbnails then enable Maintenance functions at the end of this page.)
									</p>

									<h3>Image search Regular Expression?</h3>

									<p>
										<input type=\"text\" size=\"75\" id=\"AutoPostImagesRegEx\" name=\"AutoPostImagesRegEx\" value=\"$aAdminOptions[sRegEx]\" /> (Regular expression to use when searching for images associated with a post. Do not change this if you are not familiar with PHP regular expressions. If you do change it then it must have &quot;<span class=\"kkimptxt\">__POSTID__[^0-9]+</span>&quot; somewhere in the expression which will be replaced with the actual post id before searching for images. The default value here specifies that your images must be named as apiXXXZZZ.jpg where XXX is the POSTID of the post that you want to associate the image with and ZZZ is can be any character other than numbers and the extension of the image can be jpg or any valid image extension)
									</p>

									<p>
										<strong class=\"kkimptxt\">Note: </strong>Thi is used only if you choose <em>&quot;Post ID&quot;</em> option to search for images in &quot;<em>Search Settings</em>&quot; section at the top of the page.
									</p>

									<p>
										<strong>Note: </strong>If <em>Rename images for SEO</em> (below) is enabled then continue naming the images according to the regular expression specified here (apiXXXZZZ.jpg). The plugin will automatically rename images for SEO.
									</p>

									<h3>Rename images for SEO?</h3>

									<p>
										<input type=\"radio\" id=\"AutoPostImagesOptmizeImagesForSearch_yes\" name=\"AutoPostImagesOptmizeImagesForSearch\" value=\"YES\" {$sOptmizeImagesForSearchYesRadio} onClick=\"javascript:alert('Ensure that images uploaded are named according to the regular expression specified in Image search Regular Expression (above). The plugin will automatically rename images for SEO.')\" /> <label for=\"AutoPostImagesOptmizeImagesForSearch_yes\">Yes</label>
										<input type=\"radio\" id=\"AutoPostImagesOptmizeImagesForSearch_no\" name=\"AutoPostImagesOptmizeImagesForSearch\" value=\"NO\" {$sOptmizeImagesForSearchNoRadio} /> <label for=\"AutoPostImagesOptmizeImagesForSearch_no\">No</label>
									</p>

									<p>Selecting &quot;Yes&quot; will rename the images using the POST title so that the images names are optimized for indexing by search engines.</p>

									<p>
										<strong class=\"kkimptxt\">Note: </strong>Used only if you choose <em>&quot;Post ID&quot;</em> option to search for images in &quot;<em>Search Settings</em>&quot; section at the top of the page.
									</p>

									<p>
										<strong>Note: </strong>Ensure that images uploaded are named according to the regular expression specified in <em>Image search Regular Expression</em> (above). The plugin will automatically rename images for SEO. For example: If <em>Image search Regular Expression</em> is set to \"^api__POSTID__[^.]*.[a-zA-Z]+$\" then you will upload images for POST #3 as <em>api3.jpg</em> and the SEO process will rename it as <em>sapi3_the-title-of-the-post_0.jpg</em> and the thumbnail will be named <em>thumb_sapi3_the-title-of-the-post_0.jpg</em>
									</p>

								</div>


								<div class=\"tabbertab\">
									<h2>Path and Url Settings</h2>

									<h3>Image directory?</h3>

									{$sImageDirAlert1}
									{$sImageDirAlert2}

									<p>This is the location where you will be putting all the images. You should change this from the default to a folder outside the plugin directory.</p>

									<p>
										<input type=\"text\" class=\"kkwidth90\" id=\"AutoPostImagesImgDir\" name=\"AutoPostImagesImgDir\" value=\"$aAdminOptions[sImageDirPath]\" />
									</p>

									<h3>Thumbnail directory?</h3>

									{$sThumbnailDirAlert}

									<p>This is where the thumbnails generated by the plugin are stored. You should change this from the default to a folder outside the plugin directory.</p>

									<p>
										<input type=\"text\" class=\"kkwidth90\" id=\"AutoPostImagesThumbDir\" name=\"AutoPostImagesThumbDir\" value=\"$aAdminOptions[sImageThumbnailDirPath]\" />
									</p>

									<h3>Cache directory? (Used by the thumbnail creator)</h3>

									{$sCacheDirAlert}

									<p>This is the directory used by the plugin as cache location when generating thumbnails. Ensure that the plugin has write permissions for this folder.</p>

									<p>
										<input type=\"text\" class=\"kkwidth90\" id=\"AutoPostImagesCacheDir\" name=\"AutoPostImagesCacheDir\" value=\"$aAdminOptions[sImageCacheDir]\" />
									</p>

									<h3>Image directory URL?</h3>

									<p>This the url to the directory where the images are stored.</p>

									<p>
										<input type=\"text\" class=\"kkwidth90\" id=\"AutoPostImagesImgUrl\" name=\"AutoPostImagesImgUrl\" value=\"$aAdminOptions[sImageDirUrl]\" />
									</p>

									<h3>Thumbnail directory URL?</h3>

									<p>This is the url to the directory where the thumbnails are stored.</p>

									<p>
										<input type=\"text\" class=\"kkwidth90\" id=\"AutoPostImagesThumbUrl\" name=\"AutoPostImagesThumbUrl\" value=\"$aAdminOptions[sImageThumbnailDirUrl]\" />
									</p>

								</div>


								<div class=\"tabbertab\">
									<h2>Maintenance</h2>

									<h3>Rerun image maintenence functions?</h3>

									<p>
										<input type=\"checkbox\" id=\"AutoPostImagesRerun\" value=\"rerun\" name=\"AutoPostImagesRerun\" value=\"$aAdminOptions[bRerun]\" /> Yes! Re-generate thumbnails &amp; SEO renaming (if enabled).
									</p>

									<p>
										<strong>Note:</strong> If checked the plugin will delete &amp; re-generate all thumbnails folder, rename all images for SEO (if enabled). Use this to clean-up the thumbnails and ensure that all the images are named appropriately.
									</p>
								</div>


								<div class=\"tabbertab\">
									<h2>Excerpt</h2>

									<h3>Enable Excerpt?</h3>

									<p>
										<input type=\"radio\" id=\"AutoPostImagesEnableExcerpt_yes\" name=\"AutoPostImagesEnableExcerpt\" value=\"YES\" {$sEnableExcerptYesRadio} /> <label for=\"AutoPostImagesEnableExcerpt_yes\">Yes</label>
										<input type=\"radio\" id=\"AutoPostImagesEnableExcerpt_no\" name=\"AutoPostImagesEnableExcerpt\" value=\"NO\" {$sEnableExcerptNoRadio} /> <label for=\"AutoPostImagesEnableExcerpt_no\">No</label>
										<span style=\"margin-left:30px;\">(Default is &quot;No&quot;)</span>
									</p>

									<h3>Excerpt length</h3>

									<p>Length of the excerpt. The length excludes all html tags even if you answer &quot;No&quot; for the next question.</p>

									<p>
										<input type=\"text\" class=\"kkwidth20\" id=\"AutoPostImagesExcerptLength\" name=\"AutoPostImagesExcerptLength\" value=\"$aAdminOptions[iExcerptLength]\" />
									</p>

									<h3>Strip html tags?</h3>

									<p>Select &quot;Yes&quot; if you want all html tags to be removed from the excerpt. Default is &quot;No&quot;.</p>

									<p>
										<input type=\"radio\" id=\"AutoPostImagesExcerptStripTags_yes\" name=\"AutoPostImagesExcerptStripTags\" value=\"YES\" {$sExcerptStripTagsYesRadio} /> <label for=\"AutoPostImagesExcerptStripTags_yes\">Yes</label>
										<input type=\"radio\" id=\"AutoPostImagesExcerptStripTags_no\" name=\"AutoPostImagesExcerptStripTags\" value=\"NO\" {$sExcerptStripTagsNoRadio} /> <label for=\"AutoPostImagesExcerptStripTags_no\">No</label>
									</p>

									<h3>Text to use for link to full post</h3>

									<p>Default is &quot;more...&quot;</p>

									<p>
										<input type=\"text\" class=\"kkwidth20\" id=\"AutoPostImagesExcerptLinkText\" name=\"AutoPostImagesExcerptLinkText\" value=\"$aAdminOptions[sExcerptLinkText]\" />
									</p>

								</div>


								<div class=\"submit\">
									<input type=\"submit\" name=\"updateAutoPostImagesSettings\" value=\"{$sUpdateAutoPostImagesSubmitButton}\" />
								</div>

							</div>
							</form>
						</div>
				";
			}

			function renameImagesForSEO($iPostId = FALSE, $sPostName = '') {

				if($iPostId === FALSE || is_numeric($iPostId) === FALSE) {

					global $post;

					$iPostId = $this->iPostId;
					$sPostName = $post->post_name;
				}

				$sThumbSearchName = $sPostName;
				$sThumbSearchTags = str_replace('-', ' ', $sThumbSearchName);
				$iImageSeoCount = 0;

				//
				// If SEO image optimization is turned ON then check the image name and rename if required
				//

				$aImageFiles = $this->getImagesForThisPost($iPostId, $this->aAdminOptions['sRegEx']);

				//echo '<pre>', print_r($aImageFiles, TRUE), '</pre>';
				//echo '<pre>', print_r($this->aAdminOptions['sRegEx'], TRUE), '</pre>';

				sort($aImageFiles);

				//echo '<pre>', print_r($aImageFiles, TRUE), '</pre>';

				foreach($aImageFiles as $sKey => $sImageName) {

					if ($this->aAdminOptions['bOptmizeImagesForSearch'] == 'YES') {

						if(!eregi($this->sImageSEOPattern, $sImageName)) {

							$sImageFilePath = $this->sImageDirPath . '/' . $sImageName;
							$sThumbImageFilePath = $this->sImageThumbnailDirPath . '/' . 'thumb_' . $sImageName;

							//echo '<p>Image - ', $sImageFilePath, '</p>';

							$sExtn = pathinfo($sImageFilePath, PATHINFO_EXTENSION);

							do {

								$sNewSeoImageName = $this->sImageSEOPrefix . $iPostId . '_' . $sThumbSearchName . '_' . str_pad($iImageSeoCount++, 4, "0", STR_PAD_LEFT) . '.' . $sExtn;
								$sNewSeoThumbImageName = 'thumb_' . $sNewSeoImageName;

								$sNewImageFilePath = $this->sImageDirPath . '/' . $sNewSeoImageName;
								$sNewThumbImageFilePath = $this->sImageThumbnailDirPath . '/' . $sNewSeoThumbImageName;

							} while (is_file($sNewImageFilePath) === TRUE);

							//echo "<p>rename {$sImageName} to {$sNewImageFilePath}</p>";
							//echo "<p>rename {$sThumbImageFilePath} to {$sNewThumbImageFilePath}</p>";

							//Rename the image file
							//
							if(is_file($sImageFilePath)) {

								rename($sImageFilePath, $sNewImageFilePath);
							}

							//Rename the thumbnail image file
							//
							if(is_file($sThumbImageFilePath)) {

								rename($sThumbImageFilePath, $sNewThumbImageFilePath);
							}

						} else {

							//
							//echo "<p>{$sImageName} does not need SEO renaming</p>";
						}
					}
				}
			}

			function rerunImagesProcesses() {

				$aImageTypes = array();

				$oFileSys = new Filesystem();
				$sStatus = '<div class="updated"><p><strong>' . __("Running maintenance methods on images.", "KKAutoPostImages") . '</strong></p>';

				//
				// Delete all the thumbnails
				//
				$aThumbnails = $oFileSys->readDir($this->sImageThumbnailDirPath);

				//echo '<pre>', print_r($aThumbnails, TRUE), '</pre>';

				foreach($aThumbnails as $sThumbnailPath) {

					//echo "<p>{$this->sImageThumbnailDirPath} {$sThumbnailPath}</p>";

					unlink($this->sImageThumbnailDirPath . '/' .$sThumbnailPath);
				}

				$sStatus .= '<p>- ' . __("Deleted all thumbnails.", "KKAutoPostImages") . '</p>';

				$aPostIds = array();

				//
				// Run SEO on image names
				//
				if ($this->aAdminOptions['bOptmizeImagesForSearch'] == 'YES') {

					$aPostsList = get_posts('');

					foreach($aPostsList as $post) {

						//echo $post->ID;
						//echo '<pre>', print_r($post, TRUE), '</pre>';

						$aPostIds[$post->ID] = $post->ID;
						$this->renameImagesForSEO($post->ID, $post->post_name);
					}
				}

				$sStatus .= '<p> - ' . __("Optimized all image names for search engine optimization.", "KKAutoPostImages") . '</p>';

				//
				// Locate images that are not associated with any POSTs & images that are not named correctly
				//
				$aImages = $oFileSys->readDir($this->sImageDirPath);

				$aImagePostIds = array();

				$sRegExMatched = $this->aAdminOptions['sRegEx'];

				foreach($aImages as $sImagePath) {

					//echo "<p>{$this->sImageDirPath} {$sImagePath}</p>";

					if( eregi($this->sImageSEOPatternMatched, $sImagePath) ) {

						//$aImageTypes['seo'][] = $sImagePath;

					} else if(eregi($sRegExMatched, $sImagePath)) {

						$aImageTypes['Non SEO Images'][] = $sImagePath;

					} else {

						$aImageTypes['Unknown Images'][] = $sImagePath;
					}
				}

				$sImageProperties = '<strong>Report:</strong><ul class="kkreportui">';
				foreach($aImageTypes as $sImageType => $aImageList) {

					$sImageProperties .= "<li><strong>{$sImageType}</strong><ul class=\"kkreportui2\">";

					foreach($aImageList as $sImage) {

						$sImageProperties .= "<li>{$sImage}</li>";
					}

					$sImageProperties .= "</ul></li>";
				}
				$sImageProperties .= "</ul>";

				$sStatus .= '<p> - ' . __("Examining all images.", "KKAutoPostImages") . '</p>' . "<p>{$sImageProperties}</p>";

				//echo '<pre>', print_r($aImageTypes, TRUE), '</pre>';

				$sStatus .= '<p /><p><strong>' . __("Maintenance complete.", "KKAutoPostImages") . '</strong></p>';

				$sStatus .= '</div>';

				return $sStatus;
			}

			function getExcerptText ($bAPIExcerpt, $bEnableExcerpt, $iExcerptLength, $bExcerptStripTags, $sExcerptLinkText, $iPostId, &$sContent) {

				if ($bAPIExcerpt === TRUE && $bEnableExcerpt == 'YES') {

					$iLength = $iExcerptLength;

					if ($bExcerptStripTags == 'NO') {

						$iHtmlLen = strlen($sContent);
						if ($iHtmlLen > $iLength) {

							$iCount = 0;
							for($i = 0; $i < $iHtmlLen && $iCount < $iLength; $i++) {

								if($sContent[$i] == '<') {

									$bSkip = TRUE;

								} else if($sContent[$i] == '>') {

									$bSkip = FALSE;

								} else {

									if ($bSkip === FALSE) {

										$iCount++;
									}
								}
							}
						} else {

							$i = $iLength;
						}

						$sContent = $this->closetags(substr($sContent,0,$i));

					} else {

						$sContent = substr(strip_tags($sContent),0,$iLength);
					}

					if ( !empty($sExcerptLinkText)) {

						$sLink = get_permalink();
						$sTitle = get_the_title($iPostId);

						$sContent .= " <a href=\"{$sLink}\" title=\"{$sTitle}\" >{$sExcerptLinkText}</a>";
					}
				}
			}

			function closetags ( $html ) {

				//put all opened tags into an array
				//
				preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );

				$openedtags = $result[1];

				//put all closed tags into an array
				//
				preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
				$closedtags = $result[1];
				$len_opened = count ( $openedtags );

				//all tags are closed
				//
				if( count ( $closedtags ) == $len_opened ) {

					return $html;
				}

				$openedtags = array_reverse ( $openedtags );

				//close tags
				//
				for( $i = 0; $i < $len_opened; $i++ ) {

					if ( !in_array ( $openedtags[$i], $closedtags ) ) {

						$html .= "</" . $openedtags[$i] . ">";
					} else {

						unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
					}
				}

				return $html;
			}


		} // End Class KKAutoPostImages

	} // End if


	//
	// Initialize the admin panel
	//
	if (!function_exists("AutoPostImages_AdminPageFunc")) {

		function AutoPostImages_AdminPageFunc () {

			global $oKKAutoPostImages;

			if ( !isset($oKKAutoPostImages) ) {

				return;
			}

			if ( function_exists('add_options_page') ) {

				$mypage = add_options_page('Auto Post Images (API) Settings', 'Auto Post Images (API) Settings', 9, basename(__FILE__), array(&$oKKAutoPostImages, 'printAdminPage'));

				add_action( "admin_print_scripts-$mypage", array(&$oKKAutoPostImages, 'includeAdminCSS'), 1);
				add_action( "admin_print_scripts-$mypage", array(&$oKKAutoPostImages, 'includeAdminJS'), 1);
			}
		}
	}

	/*
	 *
	 *    Initializing the class and hooking into wordpress
	 *
	 */

	if (class_exists("KKAutoPostImages")) {

		$oKKAutoPostImages = new KKAutoPostImages();
	}

	// Actions and Filters
	//
	if (isset($oKKAutoPostImages)) {

		//
		// Actions
		//

		// Admin screen for the Auto Post Images (API) plugin
		//
		add_action('admin_menu', 'AutoPostImages_AdminPageFunc');

		// Add the css and javascript files to the page <head>
		//
		add_action('wp_head', array(&$oKKAutoPostImages, 'includeCSS'), 1);
		add_action('wp_head', array(&$oKKAutoPostImages, 'includeJS'), 1);

		// Call the init function to initialize the admin options
		//
		add_action('activate_auto-post-images/auto-post-images.php',  array(&$oKKAutoPostImages, 'init'));

		//
		// Filters
		//

		// Add the image to the post content
		//
		add_filter('the_content', array(&$oKKAutoPostImages, 'showImagesByPostId'), 1);
	}

?>