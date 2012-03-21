<?php

set_time_limit(0);

error_reporting(-1);

require_once( '../../../../wp-load.php' );
require_once( ABSPATH . 'wp-includes/post.php' );

require_once( 'functions.admin.upload_post.php' );
require_once( 'functions.admin.google_search_by_image_upload.php' );

global $wpdb;

$WEIBOs = array(
	array(
		'weibo_id' => '1657430300',    //创意工坊
		'tags'     => array('design'),
	),
);

// foreach ($WEIBOs as $WEIBO) {
// 	var_dump($WEIBO);
// }

// $result_weibo_array = get_image_posts_by_weibo_id('1657430300');
// var_dump($result_weibo_array);

function get_latest_weibo_mid_by_weibo_id ($weibo_id) {

	global $wpdb;

	$weibo_mid = 0;

	$sql_query_post_by_weibo_id = "SELECT POST_ID FROM {$wpdb->postmeta} WHERE META_KEY = 'wp_post_weibo_id' AND META_VALUE = '{$weibo_id}'";

	// var_dump($sql_query_post_by_weibo_id);

	$weibo_post_array = $wpdb->get_results($sql_query_post_by_weibo_id);

	$weibo_mid_by_weibo_id_array = array();

	foreach ( $weibo_post_array as $weibo_post ) {
		$sql_query_post_by_weibo_mid = "SELECT META_VALUE FROM {$wpdb->postmeta} WHERE META_KEY = 'wp_post_weibo_mid' AND POST_ID = {$weibo_post->POST_ID}";

		// var_dump($sql_query_post_by_weibo_mid);

		$weibo_mid_post_array = $wpdb->get_results($sql_query_post_by_weibo_mid);

		array_push($weibo_mid_by_weibo_id_array, $weibo_mid_post_array[0]->META_VALUE);

	}

	if ( !empty($weibo_mid_by_weibo_id_array) ) {
		sort($weibo_mid_by_weibo_id_array, SORT_STRING);
		$weibo_mid = array_pop($weibo_mid_by_weibo_id_array);
	}

	return $weibo_mid;
}

function get_all_of_latest_image_weibo() {

	$result_array = array();

	foreach ($$WEIBO_IDs as $weibo_id) {
		array_push($result_array, get_image_posts_by_weibo_id());
	}

}

function get_image_posts_by_weibo_id($weibo_id, $since_id = 0){

	$username = 'hgon23@gmail.com';       
	$password = 'allan2641';

	$weibo_api_base = 'http://api.t.sina.com.cn/statuses/user_timeline.json?';
	
	$curl = curl_init();

	$post_data['source']   = '2365217913';
	$post_data['user_id']  = $weibo_id;
	$post_data['feature']  = '0';
	$post_data['count']    = '5';
	$post_data['base_app'] = '0';

	if ( $since_id ) {
		$post_data['since_id'] = $since_id;
	}

	curl_setopt(
		$curl, 
		CURLOPT_URL, 
		$weibo_api_base . http_build_query($post_data)
	);

	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	// curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");

	$weibo_data_json = curl_exec($curl);

	curl_close($curl);

	if ( empty($weibo_data_json) ) {
		return array();
	}

	$weibo_data_array = json_decode($weibo_data_json, true);

	$result_post_array = array();

	foreach ( $weibo_data_array as $weibo_data_array_item ) {
		$result_post_array_item = array();

		$is_original_image_weibo = (
				!array_key_exists('retweeted_status', $weibo_data_array_item)
			and array_key_exists('original_pic', $weibo_data_array_item)
		);

		if ( ! $is_original_image_weibo ) {

			continue;
		}

		$result_post_array_item['mid']          = $weibo_data_array_item['mid'];
		$result_post_array_item['original_pic'] = $weibo_data_array_item['original_pic'];

		if ( array_key_exists('text', $weibo_data_array_item) ) {
			$result_post_array_item['text'] = $weibo_data_array_item['text'];
		}

		if ( array_key_exists('created_at', $weibo_data_array_item) ) {
			$result_post_array_item['created_at'] = $weibo_data_array_item['created_at'];
		}

		array_push($result_post_array, $result_post_array_item);	
	}

	return $result_post_array;
}

function add_weibo_meta_data_to_post( $post_id, $weibo_id, $weibo_mid ) {
	update_post_meta( $post_id, 'wp_post_weibo_id', $weibo_id );
	update_post_meta( $post_id, 'wp_post_weibo_mid', $weibo_mid );

	return $post_id;
}

function generate_google_search_by_image_content ( $google_search_by_image_url ) {
	$result_content_string = '<p>Detailed description for this post will come soon ...</p>';

	if ( !empty($google_search_by_image_url) ) {
		$result_content_string = "<p>For more information about this post, please check out <a target=\"_blank\" href=\"{$google_search_by_image_url}\">Google image search result of this post (opens new window/tab)</a></p>";
	}

	return $result_content_string;
}

function get_post_attached_image_file_path($post_id) {
	
	$result_post_attached_image_file_path = '';

	$args = array( 
		'post_parent' => $post_id,
		'post_type'   => 'attachment', 
		'numberposts' => 1,
		'post_status' => 'any',
	);

	$image_attachment_array = get_children($args);

	if ( !empty($image_attachment_array) ) {
		$post_image_attachment_id = key($image_attachment_array);

		$result_post_attached_image_file_path = get_attached_file($post_image_attachment_id);
	}

	return $result_post_attached_image_file_path;
}

function cron_job() {

	$WEIBOs = array(
		array(
			'weibo_id' => '1657430300',    //创意工坊
			'tags'     => array('design'),
		),
	);

	error_reporting(-1);

	// var_dump($WEIBOs);

	foreach ( $WEIBOs as $weibo_array ) {
		$latest_weibo_mid = get_latest_weibo_mid_by_weibo_id($weibo_array['weibo_id']);

		// print '$latest_weibo_mid: ' . $latest_weibo_mid . '<br />';

		// if ( !$latest_weibo_mid ) {
		// 	continue;
		// }

		$result_weibo_array = get_image_posts_by_weibo_id($weibo_array['weibo_id'], $latest_weibo_mid);

		//var_dump($result_weibo_array); break;

		if ( !empty($result_weibo_array) ) {
			foreach ( $result_weibo_array as $weibo_post ) {

				$post_id = upload_image_post(
					'No title yet',                                         // post title
					'weibo_image_post',                                     // post name
					$weibo_post['text'],                                    // post content
					array(1),                                               // post category
					array_merge( array('weibo'), $weibo_array['tags'] ),    // post tags
					$weibo_post['original_pic'],                            // image url
					NULL                                                    // image description
				);

				print 'added post - post_id: ' . $post_id . '<br />';

				if ( $post_id ) {
					$post_id = add_weibo_meta_data_to_post( $post_id, $weibo_array['weibo_id'], $weibo_post['mid'] );

					$image_absolute_path = get_post_attached_image_file_path($post_id);

					$google_search_by_image_result = google_search_by_image_upload($image_absolute_path);

					$post_id = update_image_post_title_and_add_content(
						$post_id,
						$google_search_by_image_result['image_title'],
						generate_google_search_by_image_content($google_search_by_image_result['image_url'])
					);

					if ( $google_search_by_image_result['image_title'] == 'No title yet' ) {
						delete_post($post_id);
					}

					sleep(10);
				}
			}
		} else {
			print "No new original weibo post found for weibo id {$weibo_array['weibo_id']} <br />";
		}

		sleep(10);

	} // foreach ( $WEIBOs as $weibo_array ) {

}

cron_job();



?>