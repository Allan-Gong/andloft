<?PHP

	require_once ( dirname(__FILE__) . '/phpThumb/phpthumb.class.php');

	// USAGE:
	//
	// $sImageFilePath = 'test.jpg';
	// $sThumbnailFilePath = 'thumb_test.jpg';
	//
	// KK_Thumbs::createThumbnail ($sImageFilePath, $sThumbnailFilePath);
	//
	class ThumbCreator {

		function ThumbCreator() {

		}

		function createThumbnail ($sImageDir, $sImageFilePath, $sThumbnailFilePath, $iThumbnailWidth = 100) {

			// create phpThumb object
			//
			$oThumb = new phpThumb();

			$sCache = dirname(__FILE__) . '/phpThumb/cache/';

			// this is very important when using a single object to process multiple images
			//
			$oThumb->resetObject();

			$oThumb->setSourceData( file_get_contents($sImageFilePath) );

			// PLEASE NOTE:
			// You must set any relevant config settings here. The phpThumb
			// object mode does NOT pull any settings from phpThumb.config.php
			//
			$oThumb->setParameter('config_document_root', $sImageDir);
			$oThumb->setParameter('config_cache_directory', $sCache);

			// set parameters (see "URL Parameters" in phpthumb.readme.txt)
			//
			$oThumb->setParameter('w', $iThumbnailWidth);

			// generate & output thumbnail
			//
			$sOutputFilename = $sThumbnailFilePath;

			// generate & output thumbnail
			//
			if ($oThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!

				if ($oThumb->RenderToFile($sOutputFilename)) {

					return TRUE;

				} else {

					return FALSE;
				}

			} else {

				return FALSE;
			}
		}
	}
