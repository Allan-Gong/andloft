<?php
@include_once('../simplepie.inc');

// Parse it
$feed = new SimplePie();
if (isset($argv[1]) && $argv[1] !== '')
{
	$feed->set_feed_url($argv[1]);
}
else{
	$feed->set_feed_url('http://pinterest.com/hanyung/feed.rss');
}

$feed->enable_cache(false);
$feed->init();

$items = $feed->get_items();

foreach ( $items as $item )
{
	echo $item->get_title() . '<br />' . $item->get_description() . "<br />";

	$doc = new DOMDocument();

	$doc->loadHTML($item->get_description());

	$a = $doc->getElementsByTagName('a')->item(0);

	$href_url = $a->getAttribute('href');

	$result_array = @get_pinterest_real_post_url($href_url);

	var_dump($result_array);

	break;
}

//var_dump($feed->get_item_quantity());


function get_pinterest_real_post_url ($pinterest_url) {

	$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
	$header[1] = "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
	$header[2] = "Cache-Control: max-age=0";
	$header[3] = "Connection: keep-alive";
	$header[4] = "Keep-Alive: 300";
	$header[5] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$header[6] = "Accept-Language: en-us,en;q=0.5";
	$header[7] = "Pragma: ";

	$curl_connection = curl_init($pinterest_url);
	
	//set options
	curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; rv:10.0.2) Gecko/20100101 Firefox/10.0.2");
	curl_setopt($curl_connection, CURLOPT_HTTPHEADER, $header); 
	curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);

	$result = curl_exec($curl_connection);

	//close the connection
	curl_close($curl_connection);

	$doc = new DOMDocument();

	$doc->loadHTML($result);

	$img = $doc->getElementById('pinCloseupImage');

	$href = $img->parentNode->getAttribute('href');

	return array(
		'post_url'  => $href,
		'image_url' => $img->getAttribute('src'),
	);
}

?>