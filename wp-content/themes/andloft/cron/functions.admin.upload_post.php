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

function delete_post($post_id, $reason='No reason provided!') {
  wp_delete_post( $post_id, false );

  $DEBUG = false;

  if ( $DEBUG ) {
    print $reason . '<br />';
  }
}

function upload_image_post(
  $post_title,
  $post_name,
  $post_content,
  $post_category = array(1),
  $post_tags = array(),
  $image_url,
  $image_description = NULL
){
  
  error_reporting(E_ERROR);

  $post_args = array(
    'post_category' => $post_category,
    'post_content'  => $post_content,
    'post_name'     => $post_name,
    'post_title'    => $post_title,
    'post_status'   => 'draft',
    'tags_input'    => $post_tags,
  );

  $post_id = wp_insert_post( $post_args, true );

  if ( is_wp_error($post_id) ) {
    return false;
  }

  $post_image_html_tag = media_sideload_image($image_url, $post_id, $image_description);

  if ( is_wp_error($post_image_html_tag) ) {
    // remove post
    delete_post($post_id, 'is_wp_error($post_image_html_tag)');
    return false;
  }

  $post_content =
      '<div style="overflow: auto;">' . $post_image_html_tag . '</div>'
    . '<p>' . $post_content . '</p>'
  ;

  $post_id = wp_update_post( array(
    'ID'           => $post_id,
    'post_content' => $post_content,
  ));

  if ( !$post_id ) {
    delete_post($post_id, 'if ( !$post_id )');
    return false;
  }

  $image_query_args = array(
      'post_type'   => 'attachment',
      'post_parent' => $post_id,
  );

  $image_query_result_array = get_posts( $image_query_args );

  if ( !count( $image_query_result_array ) ) {
    delete_post($post_id, 'if ( !count( $image_query_result_array ) )');
    return false;
  }

  $image = $image_query_result_array[0];

  if ( ! update_post_meta( $post_id, 'media_use', 'image' ) ) {
    delete_post($post_id, "if ( ! update_post_meta( $post_id, 'media_use', 'image' ) )");
    return false;
  }

  if ( ! update_post_meta( $post_id, '_thumbnail_id', $image->ID ) ) {
    delete_post($post_id, "if ( ! update_post_meta( $post_id, '_thumbnail_id', $image->ID ) )");
    return false;
  }

  if ( ! update_post_meta( $post_id, 'columns', get_random_post_column() ) ) {
    delete_post($post_id, "if ( ! update_post_meta( $post_id, 'columns', get_random_post_column() ) )");
    return false;
  }

  if ( ! update_post_meta( $post_id, '_wp_post_template', 'post-sidebar.php' ) ) {
    delete_post($post_id, "if ( ! update_post_meta( $post_id, '_wp_post_template', 'post-sidebar.php' ) )");
    return false;
  }

  $post_id = wp_update_post( array(
    'ID'          => $post_id,
    'post_status' => 'publish',
  ));

  if ( !$post_id ) {
    delete_post($post_id, "if ( !$post_id )");
    return false;
  }

  return $post_id;
}

function get_random_post_column() {

  $result = 'One';

  if ( rand(1, 100) <= 95 ) {
    $result = 'One';
  } elseif ( rand(1, 100) <= 4 ) {
    $result = 'Two';
  } elseif ( rand(1, 100) <= 1 ) {
    $result = 'Three';
  } else {
    $result = 'One';
  }

  // switch ( rand(1,3) ) {
  //   case 1:
  //       $result = 'One';
  //       break;
  //   case 2:
  //       $result = 'Two';
  //       break;
  //   case 3:
  //       $result = 'Three';
  //       break;
  // }

  return $result;
}

function update_image_post_title_and_add_content($post_id, $title, $added_content) {

  $post = get_post($post_id);

  $post_id = wp_update_post( array(
    'ID'           => $post_id,
    'post_title'   => $title,
    'post_content' => $post->post_content . $added_content,
  ));

  return $post_id;
}

?>