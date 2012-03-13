<?php
	require('magpierss-0.72/rss_fetch.inc');

	$feed_urls = array(
		'pinterest' => 'http://pinterest.com/hanyung/feed.rss',
	);

	foreach ( $feed_urls as $name => $url ) {
		$rss = fetch_rss($url);
		var_dump($rss);
	}

?>