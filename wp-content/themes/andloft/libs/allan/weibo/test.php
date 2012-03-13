<?php

$username = 'hgon23@gmail.com';       
$password = 'allan2641';

$api_base = 'http://api.t.sina.com.cn/statuses/user_timeline.json?';

// param validate

// …

$curl = curl_init();

//curl_setopt($curl, CURLOPT_URL, "http://api.t.sina.com.cn/statuses/user_timeline.json?source=2365217913&uid=1657430300");

$post_data['source'] = '2365217913';
$post_data['user_id'] = '1657430300';
$post_data['feature'] = '0';
$post_data['count'] = '10';
$post_data['base_app'] = '0';

// $test_string = implode('&', $post_data);

// var_dump($test_string);


curl_setopt(
	$curl, 
	CURLOPT_URL, 
	$api_base . http_build_query($post_data)
);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");



$data = curl_exec($curl);

//var_dump(utf8_encode($data));

//var_dump(json_decode($data, true));

//print_r($data);

curl_close($curl);

$source_array = json_decode($data, true);

//var_dump($source_array);

$result_array = array();

// foreach ( $source_array as $source_item ) {
// 	if ( array_key_exists('text', $source_array) ) {
// 		$test_result = strpos($source_item['text'], '@');

// 		echo 'text: ' . $source_item['text'] . ' | ' . $test_result . '<br />';
// 	} 
// }

foreach ( $source_array as $source_item ) {

	$item_info = array();

	if ( array_key_exists('text', $source_item) and !array_key_exists('retweeted_status', $source_item) ) {
		if ( false === strpos($source_item['text'], '@') and array_key_exists('mid', $source_item) ) {

			$item_info['mid']  = $source_item['mid'];
			$item_info['text'] = $source_item['text'];

			if ( array_key_exists('thumbnail_pic', $source_item) ) {
				$item_info['thumbnail_pic'] = $source_item['thumbnail_pic'];
			}

			if ( array_key_exists('bmiddle_pic', $source_item) ) {
				$item_info['bmiddle_pic'] = $source_item['bmiddle_pic'];
			}

			if ( array_key_exists('original_pic', $source_item) ) {
				$item_info['original_pic'] = $source_item['original_pic'];
			}

			if ( array_key_exists('created_at', $source_item) ) {
				$item_info['created_at'] = $source_item['created_at'];
			}
		}
	} else if ( array_key_exists('retweeted_status', $source_item) ) {
		// if ( false === strpos($source_item['retweeted_status']['text'], '@') and array_key_exists('mid', $source_item) ) {
		// 	$item_info['mid']  = $source_item['mid'];
		// 	$item_info['text'] = $source_item['retweeted_status']['text'];

		// 	if ( array_key_exists('created_at', $source_item) ) {
		// 		$item_info['created_at'] = $source_item['created_at'];
		// 	}

		// 	if ( array_key_exists('thumbnail_pic', $source_item['retweeted_status']) ) {
		// 		$item_info['thumbnail_pic'] = $source_item['retweeted_status']['thumbnail_pic'];
		// 	}

		// 	if ( array_key_exists('bmiddle_pic', $source_item['retweeted_status']) ) {
		// 		$item_info['bmiddle_pic'] = $source_item['retweeted_status']['bmiddle_pic'];
		// 	}

		// 	if ( array_key_exists('original_pic', $source_item['retweeted_status']) ) {
		// 		$item_info['original_pic'] = $source_item['retweeted_status']['original_pic'];
		// 	}
		// }
	} else {
		// do nothing for now
	}

	if ( ! empty($item_info) )  {
		array_push($result_array, $item_info);	
	}
}

var_dump($result_array);

?>