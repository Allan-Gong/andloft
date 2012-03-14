<?php

require_once( '../../../wp-load.php' );
require_once( ABSPATH . '/wp-admin/includes/file.php' );
require_once( ABSPATH . 'wp-admin/includes/image.php');
require_once( ABSPATH . 'wp-admin/includes/media.php' );
require_once( ABSPATH . 'wp-includes/post.php' );

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

$post_image_html_tag = media_sideload_image($post_image_url, $post_id, $post_image_description);

$post_content = 
  $post_image_html_tag 
  . '<br /><br />' 
  . $post_content
;

$post_id = wp_update_post( array(
  'ID'           => $post_id,
  'post_content' => $post_content,
) );

$args = array(
    //'numberposts'     => 5,
    //'offset'          => 0,
    //'category'        => ,
    //'orderby'         => 'post_date',
    //'order'           => 'DESC',
    //'include'         => ,
    //'exclude'         => ,
    //'meta_key'        => ,
    //'meta_value'      => ,
    'post_type'       => 'attachment',
    //'post_mime_type'  => '',
    //'post_name'       => 
    'post_parent'     => $post_id,
    //'post_status'     => 'publish' 
);

$image_array = get_posts( $args );

//var_dump($image_array);

if ( count( $image_array ) ) {
  $image = $image_array[0];

  // wp_update_attachment_metadata( 
  //   $thumb_id, 
  //   wp_generate_attachment_metadata( $thumb_id, $new_file ) 
  // );

  update_post_meta( $post_id, 'media_use', 'image' );

  update_post_meta( $post_id, '_thumbnail_id', $image->ID );

  $has_post_thumbnail = has_post_thumbnail($post_id);

  $post_meta = get_post_meta($post_id, 'media_use', true);

  var_dump($has_post_thumbnail);
  var_dump($post_meta);
}
else {
  echo 'failed';
}

// update post column: wp_post_meta columns (radom)
// update post template: wp_post_meta _wp_page_template: always set to post with sidebar

?>