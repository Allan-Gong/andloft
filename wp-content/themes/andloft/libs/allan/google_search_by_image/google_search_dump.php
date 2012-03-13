<?php

	//create array of data to be posted
	$post = array(
		'encoded_image' => '@D:\Tools\wamp\www\wordpress\image_search_test_sample.jpg',
		'h1'            => 'en',
		'safe'          => 'off',
		'bih'           => '800',
		'biw'           => '1280',
		'image_content' => '',
		'filename'      => ''
	);

	$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
	$header[1] = "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
	$header[2] = "Cache-Control: max-age=0";
	$header[3] = "Connection: keep-alive";
	$header[4] = "Keep-Alive: 300";
	$header[5] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$header[6] = "Accept-Language: en-us,en;q=0.5";
	$header[7] = "Pragma: ";

	$curl_connection = curl_init('http://www.google.co.in/searchbyimage/upload');

	//set options
	curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; rv:10.0.2) Gecko/20100101 Firefox/10.0.2");
	curl_setopt($curl_connection, CURLOPT_HTTPHEADER, $header); 
	curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl_connection, CURLOPT_POST, true);

	//set data to be posted
	curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post);

	//perform our request
	$result = curl_exec($curl_connection);

	print_r($result);

	//show information regarding the request
	// print_r(curl_getinfo($curl_connection));
	// echo curl_errno($curl_connection) . '-' . curl_error($curl_connection);

	//close the connection
	curl_close($curl_connection);

?>