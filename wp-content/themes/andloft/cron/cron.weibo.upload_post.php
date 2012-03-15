<?php

$WEIBO_IDs = array(
	'1657430300',   //创意工坊
);

function get_image_posts_by_weibo_id($weibo_id, $since_id = 0){

	$username = 'hgon23@gmail.com';       
	$password = 'allan2641';

	$weibo_api_base = 'http://api.t.sina.com.cn/statuses/user_timeline.json?';
	
	$curl = curl_init();

	$post_data['source']   = '2365217913';
	$post_data['user_id']  = $weibo_id;
	$post_data['feature']  = '0';
	$post_data['count']    = '100';
	$post_data['base_app'] = '0';

	if ( $since_id ) {
		$post_data['since_id'] = $since_id;
	}

	curl_setopt(
		$curl, 
		CURLOPT_URL, 
		$api_base . http_build_query($post_data)
	);

	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");

	$weibo_data_json  = curl_exec($curl);

	curl_close($curl);

	if ( !$weibo_data_json ) {
		return array();
	}

	$weibo_data_array = json_decode($data, true);

	$result_post_array = array();

	foreach ( $weibo_data_array as $weibo_data_array_item ) {
		$result_post_array_item = array();

		$is_original_image_weibo = (
				array_key_exists('original_pic', $weibo_data_array_item)
			and !array_key_exists('retweeted_status', $weibo_data_array_item)
		);

		if ( !$is_original_weibo ) {
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





?>