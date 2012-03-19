<?php

require_once( '../../../../wp-load.php' );
require_once( ABSPATH . '/wp-admin/includes/file.php' );
require_once( ABSPATH . 'wp-admin/includes/image.php');
require_once( ABSPATH . 'wp-admin/includes/media.php' );
require_once( ABSPATH . 'wp-includes/post.php' );

/*

  possible post status

  'publish' - A published post or page
  'pending' - post is pending review
  'draft' - a post in draft status
  'auto-draft' - a newly created post, with no content
  'future' - a post to publish in the future
  'private' - not visible to users who are not logged in
  'inherit' - a revision. see get_children.
  'trash' - post is in trashbin. added with Version 2.9. 

*/

function upload_image_post(
  $post_title,
  $post_name,
  $post_content,
  $postcategory = array(1),
  $post_tags = array(),
  $image_url,
  $image_description = NULL
){
  
  $post_args = array(
    'post_category' => $post_category,
    'post_content'  => $post_content,
    'post_name'     => $post_name,
    'post_title'    => $post_title,
    'post_status'   => 'draft',
    'tags_input'    => $post_tags,
  );

  $post_id = wp_insert_post( $post, true );

  if ( is_wp_error($post_id) ) {
    return false;
  }

  $post_image_html_tag = media_sideload_image($image_url, $post_id, $image_description);

  if ( is_wp_error($post_image_html_tag) ) {
    // remove post
    wp_delete_post( $post_id, false );
    return false;
  }

  $post_content = 
    $post_image_html_tag 
    . '<br /><br />' 
    . $post_content
  ;

  $post_id = wp_update_post( array(
    'ID'           => $post_id,
    'post_content' => $post_content,
  ));

  if ( !$post_id ) {
    wp_delete_post( $post_id, false );
    return false;
  }

  $image_query_args = array(
      'post_type'   => 'attachment',
      'post_parent' => $post_id,
  );

  $image_query_result_array = get_posts( $image_query_args );

  if ( !count( $image_array ) ) {
    wp_delete_post( $post_id, false );
    return false;
  }

  $image = $image_array[0];

  if ( ! update_post_meta( $post_id, 'media_use', 'image' ) ) {
    wp_delete_post( $post_id, false );
    return false;
  }

  if ( ! update_post_meta( $post_id, '_thumbnail_id', $image->ID ) ) {
    wp_delete_post( $post_id, false );
    return false;
  }

  if ( ! update_post_meta( $post_id, 'columns', get_random_post_column() ) ) {
    wp_delete_post( $post_id, false );
    return false;
  }

  if ( ! update_post_meta( $post_id, '_wp_post_template', 'post-sidebar.php' ) ) {
    wp_delete_post( $post_id, false );
    return false;
  }

  $post_id = wp_update_post( array(
    'ID'          => $post_id,
    'post_status' => 'publish',
  ));

  if ( !$post_id ) {
    wp_delete_post( $post_id, false );
    return false;
  }

  return $post_id;
}

function get_random_post_column() {

  $result = 'One';

  switch ( rand(1,3) ) {
    case 0:
        $result = 'One';
        break;
    case 1:
        $result = 'Two';
        break;
    case 2:
        $result = 'Three';
        break;
  }

  return $result;
}

function get_image_info_from_google_search_by_image() {

}

function is_english ($string) {
  
}

?>