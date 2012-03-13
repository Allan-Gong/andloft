=== Plugin Name ===
Contributors: Karthikeyan
Donate link: http://www.karthikeyankarunanidhi.com/plugins/
Tags: auto,automatic,image,images,post,recipe,recipes,cook,cooking,chef,kitchen,photo,blog,photos
Requires at least: 2.6.2
Tested on: 2.6.2, 2.7, 2.7.1, 2.8.4,2.8.6,2.9,3.0,3.0.1,3.0.3,3.1
Tested up to: 3.1
Stable tag: 3.1.2

Automatically insert images to posts without having to edit the post. Supports renaming images for search engine optimization (SEO).

== Description ==

Automatically adds images to posts without having to edit the post. You can choose to have images selected at random or using the POST ID or POST SLUG.

To insert images into posts you no longer have to insert them one by one using the editor. All you have to do is name them appropriately and upload them to a folder & the plugin will automatically insert them into the corresponding post.

Want to add more images to the post? All you have to do is put more images into the folder and they will show up on the post without having to edit the post.

Need to remove a image from a post? Then just delete the image and it will no longer appear in the post.

No more hassle of editing the post.

Images tagged with the POSTID are displayed on that post. If the thumbnail option is enabled the plugin will use thumbnails in the post to link to the full size images. The plugin will automatically create thumbnails when needed.

Usage: Upload images by naming them as `'apiPOSTID'` where 'POSTID' is ID of post where the image should be displayed. If you wish to use a different filename format then you can edit the regular expression pattern in the settings page.

Multiple images can be attached to a post simply by ensuring that all of them have the same prefix. For posts that do not have images a default "Image coming soon" image is displayed; this can be disabled from the settings screen. 

Example Image Name: `'api66.jpg', 'api66a.jpeg', 'api66_two.gif', 'api66something'`, etc. Note: When posting multiple images to the same post by naming them with the same 'apiNNN' prefix. Avoid numbers immediately after the POSTID. For example: `'api662.gif'` will not work if the ID of the POST is 66; instead name it something like `'api66image2.gif'`. Again, if you wish to use a different filename format then you can edit the regular expression pattern in the settings page.

Demo: [Dash of Salt & A Pinch of Love](http://sethulakshmi.karunanidhi.com/)

Author Home: [Karthikeyan](http://www.karthikeyankarunanidhi.com)

Plugin Home: [Auto Post Images (API)](http://www.karthikeyankarunanidhi.com/plugins/)

== Installation ==

1. Download the plugin zip file
1. Upload the plugin contents into your WordPress installation's plugin directory
1. The plugin's .php files and readme.txt should be installed in the 'wp-content/plugins/auto-post-images-api/' directory
1. From the Plugin Management page, activate the "Auto Post Images (API)" plugin
1. Edit the plugin image/thumbnail directory path and url in the Plugin Admin Panel to a directory outside the plugin inatallation directory. **Note: If you use the default image directory inside the plugin installation directory you images might get deleted when upgrading**
1. Copy the "imagecomingsoon.jpg" default image from the plugin directory ('wp-content/plugins/auto-post-images-api/images/')into your image directory. If you don't copy it then you will get 404 error for the image.
1. Upload images to the image directory and they will appear in the corresponding posts

== Screenshots ==

1. Image thumbnail automatically added to post
2. Settings Page
3. Full size image displayed when clicked on thumbnail
4. Using Post-ID to add images to posts
5. Displaying random images
6. Using Post-SLUG to add images to posts
7. Sample images and naming conventions

== Frequently Asked Questions ==

= I not sure how to name the images? =

* If under "Search Setting" for "How do you want to attach images to a POST?" you choose "Post ID" 
* If under "General Settings" the "Image search Regular Expression?" is set to the default "^api__POSTID__[^0-9]+"
* If the POSTID of the post that the images are for is 123 then the images names in the following format will work:
 * api123.jpg
 * api123something.jpg
 * api123_a.jpg
 * api123_b.jpg
* The following will not work:
 * api123_cat1.jpg
 * api123_cat2.jpg
 * cat_photo_123.jpg
 * api__123__cat.jpg
* If you set "Rename images for SEO?" to "Yes" you should still name the images as described above. The plugin will rename the images for SEO.

= I want a feature. Can you add it for me? =

Sure. Drop me an email :)

= How do I not show the default image when there is no image for a post? =

1. Go to Dashboard 
1. Click Settings
1. Click Auto Post Images (API) Settings
1. Select "**No**" under "**Display default image if not image is found?**" 
1. Click "Update Settings"
1. Now the default "Image Coming Soon" image is not displayed for posts that do not have images.
1. Posts that have images will continue displaying the images as usual

= I don't want to use thumbnails. I want full size images in my posts =

1. Go to Dashboard 
1. Click Settings
1. Click Auto Post Images (API) Settings
1. Select "**No**" under "**Use thumbnails to link to full size images?**"
1. Click "Update Settings"
1. Now the full size images are added to posts instead of thumbnails

= Where is the Plugin Admin Panel? Do you expect me to edit the code myself? =

1. Go to Dashboard 
1. Go to Settings
1. Click Auto Post Images (API) Settings

= I want to name the images something other than apiPOSTID ? =

Sure. All you have to do is change the regular expression "**Image search Regular Expression?**". Just make sure it has __POSTID__ somewhere in the expression so that the plugin knows which images are attached to which post.

= Do I name the images differently when SEO is enabled ? =

No. You still upload the images using the same filename format specified in "**Image search Regular Expression?**". The plugin will use this to search for images associated with the post and then rename them using the posts title.

= What image types are supported? =

Anything that the browser supports.

= How do I change the image position and style =

You can customize the image position and style by providing your own CSS classes in the **Customize appearance** section in the settings page. You can also change the styles in **/auto-post-images/css/kkimageinpost.css**.
If you like to change where the images are displayed (top, bottom, left, right, center) then providing your own CSS style class here is the best way.

= I have a question that is not answered here =

Email me. I'll be happy to answer it.

= I want to donate = 

If you are really thinking about donating... Thanks, I really appreciate it. Instead of donating money just write a few lines about the plugin and link to the plugin (http://wordpress.org/extend/plugins/auto-post-images-api/) and my site (http://www.Karunanidhi.com) or use the following code: `'<a href="http://wordpress.org/extend/plugins/auto-post-images-api/">Auto-Post-Images (API)</a> plugin by <a href="http://www.Karunanidhi.com">Karthikeyan</a>'`

== Changelog ==

= 3.1.2 =
* On Sunday, February 27, 2011
* Bug fix: The length of the excerpt of not correct when strip tags was set to NO
* Bug fix: Added function to close any open html tags in the excerpt so that the excerpt is valid html


= 3.1.1 =
* On Sunday, February 27, 2011
* Bug fix: The excerpt function was not being called if the post did not have images.

= 3.1 =
* On Sunday, February 27, 2011
* Adding excerpt functionality.
* Adding tabs to option page so that the setting page is more user friendly

= 3.0.2 =
* On Tuesday, December 28, 2010
* Enhancement: Image is clickable only if "Use thumbnails to link to full size images?" is set to "Yes". 
* Added new text to FAQ to clarify image naming convention and updated screenshots


= 3.0.1 =
* On Thursday, June 24, 2010
* Bug Fix: backslash in file path breaking the plugin in linux environments. replaced with forward slash which works in both windows and linux


= 3.0 =
* On Sunday, June 20, 2010
* Added three more new ways to add images to posts: Tag Name, Category Name, Category ID
* Added new option to restrict the number of images displayed for a post to one when the post is displayed as part of a list
* Fixed bug with saving image and thumbnail directory paths
* Fixed bug in sorting images so that the images a displayed in correct sort order

= 2.4 =
* On Friday, December 25, 2009
* Added two new ways to add images: Random and using POST SLUG
* Re-designed the setting page to make it easier to use

= 2.3 =
* On 11:10:19 PM, Tuesday, December 01, 2009
* Added settings page options that will allow users to apply custom CSS classes to the image container, images, and control thumbnail width
* Updated default regular expressions
* Added "Maintenance" functions to clean-up thumbnails and force renaming of all images for search engine optimization
* Removed all inline style code for the admin screen and moved it into an admin-page only css file

= 2.2 =
* On 12:46:43 AM, Friday, September 18, 2009
* Added SEO support for images. When enabled the images will renamed using the post title

= 2.1.2 =
* On 11:16:26 PM, Tuesday, March 10, 2009
* Fixed issue with regex that was used to fetch images

= 2.1.1 =
* On 11:37:11 PM, Monday, March 09, 2009
* Fixed bug in regular expression.

= 2.1 =
* On 3:32:28 PM, Thursday, January 29, 2009
* Added 5 new settings: Image Dir, Thumbnail Dir, Cache Dir, Image Url, and Thumbnail Url so that they can be changed. This will allow users to put their image and thumbnail folders outside the plugin directory and not have to lose image when updating the plugin

= 2.0.3 =
* On 7:17:30 PM, Wednesday, January 28, 2009
* Removing background image from ibox popup that was generating a 404

= 2.0.2 =
* On 6:52:48 PM, Wednesday, January 28, 2009
* Path fix: folder name was being incorrectly set to auto-post-images instead of auto-post-images-api

= 2.0.1 =
* On 6:31:20 PM, Wednesday, January 28, 2009
* Removed title from ibox popup

= 2.0 =
* On 12:01:10 AM, Wednesday, January 28, 2009
* Added thumbnail support: PHP code to create thumbnails and ibox javascript for displaying full size image on overlay

= 1.1.1 =
* On 8:12:33 AM, Thursday, November 06, 2008
* Looks like I have to update the stable tag
* Added documentation
* Found out that the version number needs to be updated in both the readme.txt and the plugin php file :)


= 1.1 =
* On 1:05:24 AM, Thursday, November 06, 2008
* Fixed formatting issues in the readme file - escaped underscores
* Updated image filename regexp and readme text
* Added demo site link to readme and added more faq’s
* Added admin screen to update settings related to the plugin
* Added new setting to control weather the default image is displayed when images are not found for a post
* Added screenshots to the readme file

= 1.0 =
* On 2:31:26 PM, Tuesday, November 04, 2008
* First release. 

== Upgrade Notice ==

= 3.1.2 =
Enhancement: Adding excerpt functionality with support for generating excerpt of the required length after stripping out all html tags or retain html tags and generate excerpt of (roughly) the required length while still excluding html tags from the total length of the excerpt and also closing open html tags so that the html excerpt is valid html. Adding tabs to option page so that the setting page is more user friendly.
