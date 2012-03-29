<?php

/*
*
* Author: FrogsThemes
* File: include all classes functions, includes and components
*
*
*/

add_theme_support( 'automatic-feed-links' );

define('CONCATENATE_SCRIPTS', false);

// allow post thumbnails
add_theme_support( 'post-thumbnails', array( 'post' ) ); // Add it for posts
set_post_thumbnail_size( 200, 170, true );

add_image_size( 'thumbnail-post', 200, 180, true );
add_image_size( 'thumbnail-twocols', 430, 400, true );
add_image_size( 'thumbnail-threecols', 660, 600, true );

add_image_size( 'post', 200, 9999 );
add_image_size( 'twocols', 430, 9999 );
add_image_size( 'threecols', 660, 9999 );

// theme specific options in left bar
include TEMPLATEPATH . '/libs/includes/admin-options.php';
include TEMPLATEPATH . '/libs/functions/functions.admin-options.php';

// theme specific functions
include TEMPLATEPATH . '/libs/functions/functions.theme-functions.php';

// admin functions
include TEMPLATEPATH . '/libs/functions/functions.post-functions.php';

automatic_feed_links();

if ( function_exists('register_sidebar') ) {
	register_sidebar( array(
		'name' => 'Sidebar',
		'id' => 'sidebar',
		'before_widget' => '<div id="%1$s" class="%2$s widget">',
		'after_widget' => '</div>',
		'before_title' => '<span class="widget-title">',
		'after_title' => '</span>'
	) );
}

add_action('wp_head', 'frogs_wp_head');
add_action('wp_footer', 'frogs_wp_footer');
add_action('admin_init', 'frogs_add_init');
add_action('admin_menu', 'frogs_add_admin');
add_action('init', 'frogs_init');

function post_title_excerpt($post_title) {
	$post_title_excerpt = $post_title;

	if ( strlen($post_title) > 25 ) {
		$post_title_excerpt = substr($post_title, 0, 24) . '...';
	}

	return $post_title_excerpt;
}

function show_chinese() {

	$boolean_result = false;

	if ( isset($_SERVER) and !empty($_SERVER) ) {
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4); 

		if ( preg_match("/zh-c/i", $lang) or preg_match("/zh/i", $lang) ) {
			$boolean_result = true;
		}
	}

	return $boolean_result;
}

?>