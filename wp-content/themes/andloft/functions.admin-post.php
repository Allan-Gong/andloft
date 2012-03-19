<?php

require( '../../../wp-load.php' );

require( '../../../wp-admin/includes/file.php' );

require( '../../../wp-admin/includes/media.php' );

$post_image_url = 'http://media-cdn.pinterest.com/upload/99642210475616281_Jgz5Ex4c.jpg';
$post_image_description = 'sample post image dscription';


$post_name           = 'post_test_name';
$post_title          = 'post_test_title';
$post_content        = 'post_test_content';
$post_category_array = array(1);
$tag_array           = array('test_tag');

$post = array(
  // 'ID' => [ <post id> ] //Are you updating an existing post?
  // 'menu_order' => [ <order> ] //If new post is a page, sets the order should it appear in the tabs.
  'comment_status' => 'open', // 'closed' means no comments.
  //'ping_status' => 'open', // 'closed' means pingbacks or trackbacks turned off
  //'pinged' => [ ? ] //?
  //'post_author' => [ <user ID> ] //The user ID number of the author.
  'post_category' => $post_category_array, //Add some categories.
  'post_content' => $post_content, //The full text of the post.
  //'post_date' => [ Y-m-d H:i:s ] //The time post was made.
  //'post_date_gmt' => [ Y-m-d H:i:s ] //The time post was made, in GMT.
  //'post_excerpt' => [ <an excerpt> ] //For all your post excerpt needs.
  'post_name' => $post_name, // The name (slug) for your post
  //'post_parent' => [ <post ID> ] //Sets the parent of the new post.
  //'post_password' => [ ? ] //password for post?
  'post_status' => 'publish', //Set the status of the new post. 
  'post_title' => $post_title, //The title of your post.
  'post_type' => 'post', //You may want to insert a regular post, page, link, a menu item or some custom post type
  'tags_input' =>$tag_array, //For tags.
  //'to_ping' => [ ? ] //?
  //'tax_input' => [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies. 
);  

$post_id = wp_insert_post( $post, true );

$post_image_tag = media_sideload_image($post_image_url, $post_id, $post_image_description);

var_dump($post_image_tag);


?>