<?PHP
/**
* Filesystem utils
*
* @author Karthikeyan Karunanidhi <wordpress@karunanidhi.com>
* @access private
* @copyright 2008 Karthikeyan Karunanidhi. All rights reserved.
* @package com.karunanidhi.karthikeyan.Filesystem
*
* Plugin Name: Auto Image In Post
* Plugin URI: http://karthikeyan.karunanidhi.com/
* Version: v1.00
* Author: <a href="http://karthikeyan.karunanidhi.com/">Karthikeyan Karunanidhi</a>
* Description: This plugin will put images into each post. The images must all be in the same folder and must have the post-id prefixed to their name. All the images that that have the same post-id prefix will be displayed on that post. Posts that do not have image will either show a default image or no image.
*/

	/**
	* Filesystem helper methods
	*
	* @package com.karunanidhi.karthikeyan.Filesystem
	* @author Karthikeyan Karunanidhi <wordpress@karunanidhi.com>
	*/
	class Filesystem {

		var $iLevel = 0;
		var $iMaxLevel = 0;

		function Filesystem () {

		}

		/**
		* Returns the list of files in the given directory and sub-directories that match the given pattern and returns them as an array
		*
		* @param string $sDirPath Path to the directory to read
		* @param string $sFileRegEx Regular expression to filter the files in the directory. Optional.
		* @return array List of files in the directory that match the given regular expression
		*/
		function readDir ($sDirPath, $sFileRegEx = FALSE, $bProcessSubFolders = FALSE) {

			$aFilePath = array();

			if (is_dir($sDirPath)) {

				if ($dh = opendir($sDirPath)) {

					while (($file = readdir($dh)) !== false) {

						if( $file=='.' || $file=='..' ) {

							continue;
						}

						if(is_dir($sDirPath.'\\'.$file)) {

							if ($bProcessSubFolders !== TRUE) {

								continue;
							}

							$aList = $this->readDir($sDirPath.'\\'.$file, $sFileRegEx);

							if(is_array($aList) && count($aList) > 0) {

								$aFilePath = array_merge($aList, $aFilePath);
							}

						} else {

							if($sFileRegEx !== FALSE && !eregi($sFileRegEx, $file)) {

								continue;
							}

							//$aFilePath[] = $sDirPath . '\\' . $file;
							$aFilePath[] = $file;
						}

					}
					closedir($dh);
				}
			}

			return $aFilePath;

		}

		/**
		* Returns the list of files in the given directory and sub-directories that match the given pattern and returns them as an array
		*
		* @param string $sDirPath Path to the directory to read
		* @param string $sFileRegEx Regular expression to filter the files in the directory. Optional.
		* @return array List of files in the directory that match the given regular expression
		*/
		function getSubDirectories ($sDirPath, $iMaxLevel = FALSE) {

			$this->$iLevel = 0;
			$this->$iMaxLevel = $iMaxLevel;

			return $this->getSubDir ($sDirPath);

		}

		function getSubDir ($sDirPath) {

			$this->$iLevel += 1;

			if($this->$iMaxLevel !== FALSE && $this->$iLevel > $this->$iMaxLevel) {

				$this->$iLevel -= 1;
				return FALSE;
			}

			$aDirPath = array();

			if (is_dir($sDirPath)) {

				if ($dh = opendir($sDirPath)) {

					while (($file = readdir($dh)) !== false) {

						if( $file=='.' || $file=='..' ) {

							continue;
						}

						if(is_dir($sDirPath.'\\'.$file)) {

							$sPath = $sDirPath.'\\'.$file;

							$aDirPath[$sPath] = $sPath;

							$aList = $this->getSubDir($sPath);

							if(is_array($aList) && count($aList) > 0) {

								$aDirPath[$sPath] = $aList;
							}

						}

					}
					closedir($dh);
					$this->$iLevel -= 1;

				}
			}

			return $aDirPath;

		}

	}
