<?php

set_time_limit(0);

// error_reporting(-1);
ini_set('display_errors', 0);
ini_set('log_errors', 1);


require_once( '../../../../wp-load.php' );
require_once( ABSPATH . 'wp-includes/post.php' );
require_once( ABSPATH . 'wp-includes/theme.php' );

require_once( 'functions.admin.upload_post.php' );
require_once( 'functions.admin.google_search_by_image_upload.php' );

require_once( get_theme_root() . '/andloft/libs/includes/log4php/Logger.php' );

Logger::configure('log4php_config.xml');

$daily_file_logger = Logger::getLogger('weibo_cron_daily_logger');

global $wpdb;

// $WEIBOs = array(
// 	array(
// 		'weibo_id' => '1657430300',    //创意工坊
// 		'tags'     => array('design'),
// 	),
// );

// foreach ($WEIBOs as $WEIBO) {
// 	var_dump($WEIBO);
// }

// $result_weibo_array = get_image_posts_by_weibo_id('1657430300');
// var_dump($result_weibo_array);

function logInfo($info_string) {
	
	global $daily_file_logger;

	$daily_file_logger->info($info_string);

	print $info_string . '<br />';
}

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

	foreach ($WEIBO_IDs as $weibo_id) {
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
	$post_data['feature']  = '1';
	$post_data['count']    = '10';
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

		if ( !is_array($weibo_data_array_item) ) {
			continue;
		}

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

function generate_legal_text(){
	return '<p style="margin: 2em 0 0 0;"><strong>Please note:</strong> content in this post is collected from the internet and can <strong>NOT</strong> be used for any commercial purpose.</p>';
}

function generate_google_search_by_image_form($image_absolute_path) {

	if ( !empty($image_absolute_path) ) {

		return
			'<p>For more information about this post, please search Google Search by image by clicking the "Search" button below (opens new tab): </p>'
			. '<div>'
				. '<form action="http://www.google.co.in/searchbyimage/upload" method="post" enctype="multipart/form-data" target="_blank">'
					. '<input style="display:none;" type="file" name="encoded_image" value="' . $image_absolute_path . '">'
					. '<input type="hidden" name="h1" value="en">'
					. '<input type="hidden" name="safe" value="off">'
					. '<input type="hidden" name="bih" value="800">'
					. '<input type="hidden" name="biw" value="1280">'
					. '<input type="submit" name="Search" value="Search">'
				.'</form>'
				. '<br /><br />'
			. '</div>'
		;

	} else {
		return '';
	}
}

function generate_google_search_by_image_content ( $google_search_by_image_url ) {
	$result_content_string = '<p>Detailed description for this post will come soon ...</p>';

	if ( !empty($google_search_by_image_url) ) {
		$result_content_string = "<p>For more information about this post, please check out:</p>";
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
		array(
			'weibo_id' => '1628951200',    //创意铺子
			'tags'     => array('design'),
		),
		array(
			'weibo_id' => '2143579695',    //全球顶尖摄影
			'tags'     => array('photography','art'),
		),
		array(
			'weibo_id' => '1746316033',    //堆糖网
			'tags'     => array('image','art','design'),
		),
		array(
			'weibo_id' => '1858319430',    //环球时尚家居
			'tags'     => array('fashion','furniture'),
		),
		array(
			'weibo_id' => '1966380590',    //环球家居
			'tags'     => array('fashion','furniture'),
		),
		array(
			'weibo_id' => '1756434105',    //创意家居
			'tags'     => array('fashion','furniture','design'),
		),
		array(
			'weibo_id' => '2141098287',    //爱旅行爱幻想
			'tags'     => array('travel'),
		),
		array(
			'weibo_id' => '1832447572',    //一块去旅行
			'tags'     => array('travel'),
		),
		array(
			'weibo_id' => '1658364043',    //全球创意搜罗
			'tags'     => array('design'),
		),
		array(
			'weibo_id' => '1802393212',    //收录唯美图片
			'tags'     => array('picture','aesthetic'),
		),
		// array(
		// 	'weibo_id' => '1992523932',    //美食工场
		// 	'tags'     => array('gourmet'),
		// ),
		array(
			'weibo_id' => '1757142323',    //海外美食家
			'tags'     => array('gourmet',),
		),
	);

	// error_reporting(-1);
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);

	foreach ( $WEIBOs as $weibo_array ) {
		
		try {

			$latest_weibo_mid = get_latest_weibo_mid_by_weibo_id($weibo_array['weibo_id']);

			logInfo('$latest_weibo_mid: ' . $latest_weibo_mid );

			$result_weibo_array = get_image_posts_by_weibo_id($weibo_array['weibo_id'], $latest_weibo_mid);

			//var_dump($result_weibo_array); break;

			if ( !empty($result_weibo_array) ) {

				foreach ( $result_weibo_array as $weibo_post ) {

					try {

						$post_id = upload_image_post(
							'No title yet',                                         // post title
							'weibo_image_post',                                     // post name
							$weibo_post['text'], // post content
							array(1),                                               // post category
							array_merge( array('weibo'), $weibo_array['tags'] ),    // post tags
							$weibo_post['original_pic'],                            // image url
							NULL                                                    // image description
						);

						if ( $post_id ) {
							$post_id = add_weibo_meta_data_to_post( $post_id, $weibo_array['weibo_id'], $weibo_post['mid'] );

							$image_absolute_path = get_post_attached_image_file_path($post_id);

							$google_search_by_image_result = google_search_by_image_upload($image_absolute_path);

							$post_id = update_image_post_title_and_add_content(
								$post_id,
								$google_search_by_image_result['image_title'],
								$google_search_by_image_result['google_search_result_html'] . generate_legal_text()
							);

							if ( $google_search_by_image_result['image_title'] !== 'No title yet' ) {
								logInfo('publishing post - post_id: ' . $post_id );
								$post_id = wp_update_post( array(
									'ID'          => $post_id,
									'post_status' => 'publish',
								));

							} else {
								logInfo('Pending post - post_id: ' . $post_id );
								$post_id = wp_update_post( array(
									'ID'          => $post_id,
									'post_status' => 'pending',
								));
							}

							sleep(10);
						}

					} catch (Exception $e) {
						logError($e->getMessage());
						continue;
					}

				} // foreach ( $result_weibo_array as $weibo_post ) {
			} else {
				logInfo("No new original weibo post found for weibo id {$weibo_array['weibo_id']}");
			}

			sleep(10);

		} catch (Exception $e) {
			logError($e->getMessage());
			continue;
		}

		logInfo("=====================================");

	} // foreach ( $WEIBOs as $weibo_array ) {


}

cron_job();

logInfo('--------------------------------');

?>