<?php

// prevent cron script to be accessed from outside this server

$LOCALHOSTS   = array('localhost', '127.0.0.1');

$HTTP_HOSTS   = array('www.loft.andshop.com.au');
$REMOTE_ADDRS = array('110.232.142.51');
$SERVER_ADDRS = array('110.232.142.51');

$can_execute_cron_job = (
	   in_array($_SERVER['HTTP_HOST'], $LOCALHOSTS)
	or  ( 
		    	in_array($_SERVER['REMOTE_ADDR'], $REMOTE_ADDRS) 
			and in_array($_SERVER['SERVER_ADDR'], $SERVER_ADDRS)
			and in_array($_SERVER['HTTP_HOST'],   $HTTP_HOSTS)
		)
);

if( !$can_execute_cron_job ) {
    die('Permission denied.'); 
}

set_time_limit(0);

// error_reporting(-1);
ini_set('display_errors', 1);
ini_set('log_errors', 1);


require_once( '../../../../wp-load.php' );
require_once( ABSPATH . 'wp-includes/post.php' );
require_once( ABSPATH . 'wp-includes/theme.php' );

require_once( get_theme_root() . '/andloft/libs/includes/log4php/Logger.php' );

Logger::configure('log4php_config_cron_remove_trashed_posts_and_unattached_attachments.xml');

$daily_file_logger = Logger::getLogger('weibo_cron_daily_logger');

global $wpdb;

function logError($error_string) {

	global $daily_file_logger;

	$daily_file_logger->error($error_string);

	print 'ERROR: ' . $error_string . '<br />';

}

function logInfo($info_string) {
	
	global $daily_file_logger;

	$daily_file_logger->info($info_string);

	print $info_string . '<br />';
}

function remove_trashed_posts_and_unattached_attachments() {

	$trashed_posts = get_posts(array('post_status' => 'trash'));

	if ( !empty( $trashed_posts ) ) {
		foreach ($trashed_posts as $post) {
			$post_id = $post->ID;

			if( wp_delete_post( $post_id, true ) ) {
				logInfo('Deleting trashed post: ' . $post_id);
			} else {
				logInfo('Failed deleting trashed post: ' . $post_id);
			}
		}		
	}

	sleep(10);

	// remove unattached image attachments
	$unattached_image_attachments = get_posts(array(
		'post_type'    => 'attachment',
		 'numberposts' => null,
		 'post_status' => null,
		 'post_parent' => 0,
		 'numberposts' => 1000,
		 'order'       => 'ASC',
	));

	if ( !empty($unattached_image_attachments) ) {
		foreach ( $unattached_image_attachments as $unattached_image_attachment ) {
			$post_id = $unattached_image_attachment->ID;
			logInfo('Deleting unattached attachment: ' . $post_id);
			wp_delete_attachment( $post_id, true );
		}
	}

	logInfo('--------------------------------');
	logInfo('                                ');
}

remove_trashed_posts_and_unattached_attachments();

?>