<?php

require_once( 'functions.admin.utils.php' );

// $image_path = 'C:/test_image/1.jpg';
// $image_path = 'C:/test_image/2.jpg';
// $image_path = 'C:/test_image/3.jpg';
// $image_path = 'C:/test_image/4.jpg';
// $image_path = 'C:/test_image/5.jpg';
// $image_path = 'C:/test_image/6.jpg';
// $image_path = 'C:/test_image/7.jpg';
// $image_path = 'C:/test_image/8.jpg';
// $image_path = 'C:/test_image/9.jpg';
// $image_path = 'C:/test_image/10.jpg';
// $image_path = 'C:/test_image/11.jpg';
// $image_path = 'C:/test_image/12.jpg';

// $image_path = 'D:/image_test/1.jpg';
// $image_path = 'D:/image_test/2.jpg';
// $image_path = 'D:/image_test/3.jpg';
// $image_path = 'D:/image_test/4.jpg';
//$image_path = 'D:/image_test/5.jpg';
// $image_path = 'D:/image_test/6.jpg';

// $result = google_search_by_image_upload($image_path);
// print_r($result);

function get_inner_HTML($node){

	$doc = new DOMDocument();
	$count = 0;

	foreach ($node->childNodes as $child){

		if ( $child->localName !== 'li' ) {
			continue;
		}

		if ( method_exists($child, 'getAttribute') and $child->getAttribute('id') == 'imagebox_bigimages' ) {
			continue;
		}

		if ( method_exists($child, 'getAttribute') and $child->getAttribute('class') == 'g' ) {
			
			$li_item_tds = $child->firstChild->firstChild->firstChild->childNodes;

			if ( method_exists($li_item_tds, 'item') ) {

				$li_item_td         = $li_item_tds->item(1);
				$li_item_h3_a       = $li_item_td->firstChild->firstChild;
				$li_item_h3_a_value = $li_item_h3_a->textContent;

				if ( !is_english($li_item_h3_a_value) ) {
					continue;
				}							
			}

		}

		$doc->appendChild($doc->importNode($child, true));
		$count ++;
	}

	$result_html = $doc->saveHTML();

	return array(
		'result_html' => $result_html,
		'count'       => $count,
	);
}

function google_search_by_image_upload ($image_absolute_path) {

	// print $image_absolute_path . '<br />';

	error_reporting(E_ERROR);

	$DEFAULT_IMAGE_TITLE = 'No title yet';

	$result_image_title = $DEFAULT_IMAGE_TITLE;

	$result_image_title_url = '';

	if ( empty($image_absolute_path) ) {
		return $result_image_title;
	} 

	$google_search_by_image_base_url = 'http://www.google.co.in/searchbyimage/upload';

	$google_search_base_url = 'https://www.google.com';

	$post_args = array(
		'encoded_image' => "@$image_absolute_path",
		'h1'            => 'en',
		'safe'          => 'off',
		'bih'           => '800',
		'biw'           => '1280',
		'image_content' => '',
		'filename'      => '',
	);

	$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
	$header[1] = "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
	$header[2] = "Cache-Control: max-age=0";
	$header[3] = "Connection: keep-alive";
	$header[4] = "Keep-Alive: 300";
	$header[5] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$header[6] = "Accept-Language: en-us,en;q=0.5";
	$header[7] = "Pragma: ";

	$curl_connection = curl_init($google_search_by_image_base_url);

	//set options
	curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; rv:10.0.2) Gecko/20100101 Firefox/10.0.2");
	curl_setopt($curl_connection, CURLOPT_HTTPHEADER, $header); 
	curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl_connection, CURLOPT_POST, true);

	//set data to be posted
	curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_args);

	//perform our request
	$result = curl_exec($curl_connection);

	//show information regarding the request
	// print_r(curl_getinfo($curl_connection));
	// echo curl_errno($curl_connection) . '-' . curl_error($curl_connection);

	//close the connection
	curl_close($curl_connection);

	// parse result and get information we wanted:
	//print_r($result);

	/* 
		try to get the image title: 

		Best guess for this image: <a href="google_image_serach_url">$image_title</a>

		if the above line can not be found:

		try the following:
		1. Pages that include matching images: first english item to grab the title (Also list all items in the post content)
		   exclude: id="imagebox_bigimages", 
		2. set image title to be 'No title yet' and notify admin to manually set a title later.

		Also include a link for google search by image (opens new tab) at the bottom of the post content.
	*/

	$doc = new DOMDocument();

	$doc->loadHTML($result);

	$div_search = $doc->getElementById('search');
	$ol_rso   = $doc->getElementById('rso');
	$li_items = $ol_rso->childNodes;

	for ( $i = 0; $i < $li_items->length; $i++ ) {
		$li_item_i = $li_items->item($i);

		if ( !method_exists($li_item_i, 'getAttribute') ) {
			continue;
		}

		$li_item_i_attribute_style = $li_item_i->getAttribute('style');

		if ( !empty( $li_item_i_attribute_style ) ) {
			$text_pages_that_include_matching_images = $li_item_i->firstChild->textContent;

			if ( false !== strpos($text_pages_that_include_matching_images, 'Pages that include matching images') ) {
				for ( $j = $i + 1; $j < $li_items->length; $j++ ) {
					$li_item_j = $li_items->item($j);

					if ( !method_exists($li_item_j, 'getAttribute') ) {
						continue;
					}

					if ( $li_item_j->getAttribute('class') == 'g' ) {
						/*
							$li_item_j's structure
							<li>
								<table>
									<tbody>
										<tr>
											<td>
												<div>
													<a href="/images?.....">
														<img />
													</a>
												</div>
											</td>
											<td>
												<h3><a href="url we wanted">text we wanted</a></h3>
												<div></div>
											</td>
											<td></td>
										</tr>
									</tbody>
								</table>
								<div>
								</div>
							</li>
						*/

						//             <li>      -> <table>  -> <tbody>  -> <tr>     -> <td>
						$li_item_tds = $li_item_j->firstChild->firstChild->firstChild->childNodes;

						// seconde <td> is what we wanted
						if ( method_exists($li_item_tds, 'item') ) {
							$li_item_td = $li_item_tds->item(1);

							//              <td>       -> <h3>     -> <a>      
							$li_item_h3_a = $li_item_td->firstChild->firstChild;

							$li_item_h3_a_value = $li_item_h3_a->textContent;

							if ( is_english($li_item_h3_a_value) ) {
								$result_image_title     = $li_item_h3_a_value;
								$result_image_title_url = $li_item_h3_a->getAttribute('href');

								break;
							}							
						}

					} // if ( $li_item_j->getAttribute('class') == 'g' )
				} // foreach ( $j = $i + 1; $j < $li_items->length; j++ )
			} // if ( false !== strpos($text_pages_that_include_matching_images, 'Pages that include matching images') )
			break;
		} // if ( !empty( $li_item_i->getAttribute('style') ) )
	} // for ( $i = 0; $i < $li_items->length; i++ )

	$inner_html_result = get_inner_HTML($ol_rso);

	$google_search_result_html = '<div id="ires"><ol id="rso">' . $inner_html_result['result_html'] . '</ol></div>';

	if ( $inner_html_result['count'] == 1 ) {
		$google_search_result_html = $google_search_result_html . '<p>No result found!</p>';
	}

	return array(
		'image_title'               => $result_image_title,
		'image_url'                 => $result_image_title_url,
		'google_search_result_html' => $google_search_result_html,
	);
}

?>