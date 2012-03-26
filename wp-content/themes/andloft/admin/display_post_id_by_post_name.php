<?php

require_once( '../../../../wp-load.php' );
require_once( ABSPATH . 'wp-includes/post.php' );

if ( $_POST['search'] ) {

	global $wpdb;

	$post_name = $_POST['post_name'];

	$sql_query_post_by_post_name = "SELECT POST_ID FROM {$wpdb->postmeta} WHERE POST_NAME = '{$post_name}'";

	$post_id_array = $wpdb->get_results($sql_query_post_by_post_name);

	var_dump($post_id_array);
}

?>

<from action="/" method="post">
	<input type="text" name="post_name" value="" />
	<input type="submit" name="search" value="Search" /> 
</from>