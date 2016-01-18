<?php

/*
 * Amazon Wish Lister
 *
 * URL: http://www.justinscarpetti.com/projects/amazon-wish-lister/
 * URL: https://github.com/doitlikejustin/amazon-wish-lister
 *
 * Author: Justin Scarpetti
 *
 */
error_reporting(E_ERROR);
set_time_limit(300);
require_once('phpquery.php');

//?id=YOUR_AMAZON_ID
//get the amazon id or force an ID if none is passed
if(isset($_GET['id'])) $amazon_id = $_GET['id'];
else $amazon_id = '37XI10RRD17X2';

//?tld=AMAZON_COUNTRY
//Set the regional variant of Amazon to use.  e.g `?tld=co.uk` or `?tld=de` or ?tld=com`. Defaults to `com`
//Tested with: `ca`, `com`, `com.br`, `co.jp`, `co.uk`, `de`, `fr`, `in`, `it`
//Currently no wishlists available for: `com.au`, `com.mx`, `es`, `nl`
if(isset($_GET['tld'])) $amazon_country = $_GET['tld'];
else $amazon_country = 'com';

//?reveal=unpurchased
//checks what to reveal (unpurchased, all, or purchased)... defaults to unpurchased
if($_GET['reveal'] == 'unpurchased') $reveal = 'reveal=unpurchased';
elseif($_GET['reveal'] == 'all') $reveal = 'reveal=all';
elseif($_GET['reveal'] == 'purchased') $reveal = 'reveal=purchased';
else $reveal = 'reveal=unpurchased';

//?sort=date
//sorting options (date, title, price-high, price-low, updated, priority)
if($_GET['sort'] == 'date') $sort = 'sort=date-added';
elseif($_GET['sort'] == 'priority') $sort = 'sort=priority';
elseif($_GET['sort'] == 'title') $sort = 'sort=universal-title';
elseif($_GET['sort'] == 'price-low') $sort = 'sort=universal-price';
elseif($_GET['sort'] == 'price-high') $sort = 'sort=universal-price-desc';
elseif($_GET['sort'] == 'updated') $sort = 'sort=last-updated';
else $sort = 'sort=date-added';

//?tag=affiliate-tag
//Set the affiliate tag - usually ends `-21`
if(isset($_GET['tag'])) $affiliate_tag = $_GET['tag'];
else $affiliate_tag = '';

$baseurl = 'http://www.amazon.' . $amazon_country;
$content = phpQuery::newDocumentFile("$baseurl/registry/wishlist/$amazon_id?$reveal&$sort&layout=standard");
$i = 0;

if($content == '')
{
	echo('ERROR');
	die();
}
else
{
	//get all pages
	//if the count of itemWrapper is > 0 it's the old wishlist
	if(count(pq('tbody.itemWrapper')) > 0)
	{
		$pages = count(pq('.pagDiv .pagPage'));
	}
	//it's the new wishlist
	else
	{
		$pages = count(pq('#wishlistPagination li[data-action="pag-trigger"]'));
	}

	//if no "$pages" then only 1 page exists
	if(empty($pages)) $pages=1;

	for($page_num=1; $page_num<=$pages; $page_num++)
	{
		$contents = phpQuery::newDocumentFile("$baseurl/registry/wishlist/$amazon_id?$reveal&$sort&layout=standard&page=$page_num");

		if($contents == '')
		{
			echo('ERROR');
			die();
		}
		else
		{
			//get all items
			$items = pq('tbody.itemWrapper');

			//if items exist (the let's use the old Amazon wishlist
			if($items->html())
			{
				//loop through items
				foreach($items as $item)
				{
					$check_if_regular = pq($item)->find('span.commentBlock nobr');

					if($check_if_regular != '')
					{
						//$array[$i]['array'] = pq($item)->html();
						$array[$i]['num'] = $i + 1;
						$array[$i]['name'] = text_prepare(pq($item)->find('span.productTitle strong a')->html());
						$array[$i]['link'] = pq($item)->find('span.productTitle a')->attr('href');
						$array[$i]['old-price'] = pq($item)->find('span.strikeprice')->html();
						$array[$i]['new-price'] = text_prepare(pq($item)->find('span[id^="itemPrice_"]')->html());
						$array[$i]['date-added'] = text_prepare(str_replace('Added', '', pq($item)->find('span.commentBlock nobr')->html()));
						$array[$i]['priority'] = pq($item)->find('span.priorityValueText')->html();
						$array[$i]['rating'] = pq($item)->find('span.asinReviewsSummary a span span')->html();
						$array[$i]['total-ratings'] = pq($item)->find('span.crAvgStars a:nth-child(2)')->html();
						$array[$i]['comment'] = text_prepare(pq($item)->find('span.commentValueText')->html());
						$array[$i]['picture'] = pq($item)->find('td.productImage a img')->attr('src');
						$array[$i]['page'] = $page_num;
						$array[$i]['ASIN'] = get_ASIN($array[$i]['link']);
						$array[$i]['large-ssl-image'] = get_large_ssl_image($array[$i]['picture']);
						$array[$i]['affiliate-url'] = get_affiliate_link($array[$i]['ASIN']);
						if($_GET['isbn'] == true) {
							$array[$i]['isbn'] = get_ISBN($array[$i]['link']);
						}
						if($_GET['author'] == true) {
							$array[$i]['author'] = get_Author($array[$i]['link']);
						}

						$i++;
					}
				}
			}
			//if $items is empty, most likely the new Amazon HTML wishlist is being retrieved
			//let's load a the new wishlist
			else
			{
				$items = pq('.g-items-section div[id^="item_"]');

				//loop through items
				foreach($items as $item)
				{
					$name = htmlentities(trim(pq($item)->find('a[id^="itemName_"]')->html()));
					$link = pq($item)->find('a[id^="itemName_"]')->attr('href');

					if(!empty($name) && !empty($link))
					{
						$total_ratings = pq($item)->find('div[id^="itemInfo_"] div:a-spacing-small:first a.a-link-normal:last')->html();
						$total_ratings = trim(str_replace(array('(', ')'), '', $total_ratings));
						$total_ratings = is_numeric($total_ratings) ? $total_ratings : '';

						//$array[$i]['array'] = pq($item)->html();
						$array[$i]['num'] = $i + 1;
						$array[$i]['name'] = $name;
						$array[$i]['link'] = $baseurl . $link;
						$array[$i]['old-price'] = 'N/A';
						$array[$i]['new-price'] = text_prepare(pq($item)->find('span[id^="itemPrice_"]')->html());
						$array[$i]['date-added'] = text_prepare(str_replace('Added', '', pq($item)->find('div[id^="itemAction_"] .a-size-small')->html()));
						$array[$i]['priority'] = text_prepare(pq($item)->find('span[id^="itemPriorityLabel_"]')->html());
						$array[$i]['rating'] = 'N/A';
						$array[$i]['total-ratings'] = $total_ratings;
						$array[$i]['comment'] = text_prepare((pq($item)->find('span[id^="itemComment_"]')->html()));
						$array[$i]['picture'] = pq($item)->find('div[id^="itemImage_"] img')->attr('src');
						$array[$i]['page'] = $page_num;
						$array[$i]['ASIN'] = get_ASIN($array[$i]['link']);
						$array[$i]['large-ssl-image'] = get_large_ssl_image($array[$i]['picture']);
						$array[$i]['affiliate-url'] = get_affiliate_link($array[$i]['ASIN']);
						if($_GET['isbn'] == true) {
							$array[$i]['isbn'] = get_ISBN($array[$i]['link']);
						}
						if($_GET['author'] == true) {
							$array[$i]['author'] = pq($item)->find('div[id^="itemInfo_"] .a-row.a-size-small:has(h5 a[id^="itemName_"])');
							$array[$i]['author']->find('h5')->remove();
							$array[$i]['author'] = trim(preg_replace('/\([\ \w]+\)/', '', str_replace('by', '', $array[$i]['author']->text())));
						}

						$i++;
					}
				}
			}
		}
	}
}

//go to product details page for isbn
function get_ISBN($url) {
	$productPage = phpQuery::newDocumentFile($url);
	return trim(str_replace("ISBN-13:", "", text_prepare(pq($productPage)->find('.bucket .content li:has(b:contains("ISBN-13"))')->text())));
}

function get_Author($url) {
	$productPage = phpQuery::newDocumentFile($url);
	return trim(str_replace('(Author)', '', text_prepare(pq($productPage)->find('#byline .author .a-popover-preload .a-size-medium')->text())));
}

//format the xml (old style)
function xml_ecode($array) {
	$xml = '';
	if (is_array($array) || is_object($array)) {
		foreach ($array as $key=>$value) {
			if (is_numeric($key)) {
				$key = 'item';
			}

			//create the xml tags
			$xml .= '<' . $key . '>' . xml_ecode($value) . '</' . $key . '>';
		}
	} else { $xml = htmlspecialchars($array, ENT_QUOTES); }
	return $xml;
}

//Convert an array into valid XML
//From http://stackoverflow.com/a/5965940/1127699
function xml_encode($data, &$xml_data) {
	foreach( $data as $key => $value )
	{
		if( is_array($value) )
		{
			if( is_numeric($key) )
			{
				$key = 'item'.$key; //Elements can't be purely numeric
			}
			$subnode = $xml_data->addChild($key);
			xml_encode($value, $subnode);
		} else {
			$xml_data->addChild("$key",htmlspecialchars("$value"));
		}
	}

	return $xml_data->asXML();
}

function rss_encode($data) {

	global $baseurl;

	//	Most recent item
	//	Should really be RFC-822
	$pubDate = $data[0]['date-added'];

	$link = htmlspecialchars("{$baseurl}/registry/wishlist/{$amazon_id}?{$reveal}&{$sort}&layout=standard");

	$rss = '<?xml version="1.0" encoding="UTF-8" ?>
			<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
				<channel>
					<title>Amazon Wishlist</title>
					<description></description>
					<link>'.$link.'</link>
					<language>en-gb</language>
					<pubDate>'.$pubDate.'</pubDate>';

	foreach( $data as $key => $value )
	{
		if( is_array($value) )
		{
			$rss .= '<item>
						<title>'.$value['comment'].' '.$value['new-price'] .'</title>
						<link>'.$value['affiliate-url'].'</link>
						<description>
							<![CDATA[
								<a href="'.$value['affiliate-url'].'">'.
									html_entity_decode($value['name']).'
									<br/>
									<img src="'.$value['large-ssl-image'].'"/>
								</a>
							]]>
						</description>
						<pubDate>'.$value['date-added'].'</pubDate>
						<guid>'.$value['affiliate-url'].'</guid>
					</item>';
		}
	}

	$rss .= '</channel>
			</rss>';

	return $rss;
}

//Make sure the text is prepared for use on the web.
function text_prepare($text) {
	return trim($text);
}

//	Get the ASIN of the product
function get_ASIN($url) {
	/*
		ASIN is a 10 character string - https://en.wikipedia.org/wiki/Amazon_Standard_Identification_Number
		Typical URls
		http://www.amazon.co.uk/dp/B00IKIKDOM/ref=wl_it_dp_v_nS_ttl/278-9067265-3899254?_encoding=UTF8&colid=15SCUHW5RONL6&coliid=ICBVF90YVEFF4&psc=1
		http://www.amazon.co.jp/dp/B00OOD4RLC/ref=wl_it_dp_v_S_ttl/376-5041442-9894656?_encoding=UTF8&colid=HIOT27YDKDXW&coliid=I2199742B598MC
		http://www.amazon.de/dp/B00TRQ0CSI/ref=wl_it_dp_v_nS_ttl/275-4926214-1753269?_encoding=UTF8&colid=X82UNL4VMFM9&coliid=I2G0K6DW0S1MHT
		http://www.amazon.ca/dp/B00A7WDYYU/ref=wl_it_dp_v_nS_ttl/180-5102401-8253319?_encoding=UTF8&colid=3OR5VN6044A6I&coliid=I30UZD33GMHBK3
		http://www.amazon.com/dp/B00DQYNKCM/ref=wl_it_dp_v_nS_ttl/191-5492771-2500240?_encoding=UTF8&colid=37XI10RRD17X2&coliid=ILV2H2MHRX7HU&psc=1
		http://www.amazon.es/dp/B007RXO716/ref=wl_it_dp_v_nS_ttl/279-0188662-6542856?_encoding=UTF8&colid=1ORXZQUAJ8H96&coliid=I35SODJIWT2DTA
		http://www.amazon.in/dp/B00E81GGGY/ref=wl_it_dp_v_nS_ttl/275-0633160-8128200?_encoding=UTF8&colid=1HCKFSCVFG2UW&coliid=IUH4Z2TCPKDF2
		http://www.amazon.it/dp/B00Y0O5L6U/ref=wl_it_dp_v_nS_ttl/280-4610661-1922667?_encoding=UTF8&colid=2RPUB231AJ78D&coliid=IFOHKF3REGMUU&psc=1
		http://www.amazon.com.br/dp/B00YSILJZU/ref=wl_it_dp_v_nS_ttl/177-3586976-1287133?_encoding=UTF8&colid=3OF5TPV1ZMWLM&coliid=I36RLGSQVQOUYH
		http://www.amazon.fr/dp/B003FRXFWK/ref=wl_it_dp_v_S_ttl/275-1202652-6343230?_encoding=UTF8&colid=1EHE58O7QKWTH&coliid=I3EGD6MPOTC9GY
	*/

	//	Remove the Base URL and /dp/
	global $baseurl;
	$ASIN = str_replace($baseurl . "/dp/", '', $url);
	//	Grab the ASIN
	$ASIN = substr($ASIN, 0, 10);

	return $ASIN;
}

function get_large_ssl_image($image_url) {
	/*
		Change
			http://ecx.images-amazon.com/images/I/41kWB4Z4PTL._SL250_.jpg
		To
			https://images-eu.ssl-images-amazon.com/images/I/41kWB4Z4PTL._SL2500_.jpg

		Image URLs are always .com for some reason.
	*/

	$largeSSLImage = str_replace("http://ecx.images-amazon.com", 'https://images-eu.ssl-images-amazon.com', $image_url);
	$largeSSLImage = str_replace("_.jpg", '0_.jpg', $largeSSLImage);

	return $largeSSLImage;
}

function get_affiliate_link($AISN) {

	/*
		According to https://affiliate-program.amazon.co.uk/gp/associates/help/t5/a21

		> So if you need to build a simple text link to a specific item on Amazon.co.uk, here is the link format you need to use:
		http://www.amazon.co.uk/dp/ASIN/ref=nosim?tag=YOURASSOCIATEID

		e.g. http://www.amazon.co.uk/dp/B00U7EXH72/ref=nosim?tag=shkspr-21

		Is this the same for all countries?

		Your Associate ID only workds with one country
		https://affiliate-program.amazon.co.uk/gp/associates/help/t22/a13%3Fie%3DUTF8%26pf_rd_i%3Dassoc_he..
	*/

	global $baseurl, $affiliate_tag;
	$affiliateURL = $baseurl . "/dp/" . $AISN . "/ref=nosim?tag=" . $affiliate_tag;

	return $affiliateURL;
}

//?format=json
//format the wishlist (json, xml, or php array object) defaults to json
if($_REQUEST['format'] == 'json') {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($array);
}
elseif($_REQUEST['format'] == 'xml') {
	header('Content-Type: text/xml; charset=utf-8');
	echo xml_ecode($array);
}
elseif($_REQUEST['format'] == 'XML') {
	header('Content-Type: text/xml; charset=utf-8');
	echo xml_encode($array, new SimpleXMLElement('<?xml version="1.0"?><data></data>'));
}
elseif($_REQUEST['format'] == 'array') {
	header('Content-Type: text/html; charset=utf-8');
	print_r($array);
}
elseif($_REQUEST['format'] == 'rss') {
	header('Content-Type: application/rss+xml; charset=utf-8');
	echo rss_encode($array);
}
else {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($array);
}
