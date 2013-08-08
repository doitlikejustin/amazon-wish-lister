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
error_reporting(0);
require_once('phpquery.php');

//?id=YOUR_AMAZON_ID
//get the amazon id or force an ID if none is passed
if(isset($_GET['id'])) $amazon_id = $_GET['id'];
else $amazon_id = '37XI10RRD17X2';

//?reveal=unpurchased
//checks what to reveal (unpurchased, all, or purchased)... defaults to unpurchased
if($_GET['reveal'] == 'unpurchased') $reveal = 'reveal=unpurchased';
else if($_GET['reveal'] == 'all') $reveal = 'reveal=all';
else if($_GET['reveal'] == 'purchased') $reveal = 'reveal=purchased';
else $reveal = 'reveal=unpurchased';

//?sort=date
//sorting options (date, title, price-high, price-low, updated, priority)
if($_GET['sort'] == 'date') $sort = 'sort=date-added';
else if($_GET['sort'] == 'priority') $sort = 'sort=priority';
else if($_GET['sort'] == 'title') $sort = 'sort=universal-title';
else if($_GET['sort'] == 'price-low') $sort = 'sort=universal-price';
else if($_GET['sort'] == 'price-high') $sort = 'sort=universal-price-desc';
else if($_GET['sort'] == 'updated') $sort = 'sort=last-updated';
else $sort = 'sort=date-added';

$content = phpQuery::newDocumentFile("http://www.amazon.com/registry/wishlist/$amazon_id?$reveal&$sort&layout=standard");
$i = 0;

if($content == '')
{
	echo('ERROR');
	die();
}
else
{
	//get all pages
	$pages = count(pq('.pagDiv .pagPage'));
	
	for($page_num=1; $page_num<=$pages; $page_num++)
	{
	
		$contents = phpQuery::newDocumentFile("http://www.amazon.com/registry/wishlist/$amazon_id?$reveal&$sort&layout=standard&page=$page_num");
		
		if($contents == '')
		{
			echo('ERROR');
			die();
		}
		else
		{
			
			//get all items
			$items = pq('tbody.itemWrapper');
		
			//loop through items
			foreach($items as $item)
			{
				$check_if_regular = pq($item)->find('span.commentBlock nobr');	
				
				if($check_if_regular != '')
				{	
					//$array[$i]['array'] = pq($item)->html();
					$array[$i]['name'] = htmlentities(pq($item)->find('span.productTitle strong a')->html(), ENT_COMPAT|ENT_HTML401, 'UTF-8', FALSE);
					$array[$i]['link'] = pq($item)->find('span.productTitle a')->attr('href');
					$array[$i]['old-price'] = pq($item)->find('span.strikeprice')->html();
					$array[$i]['new-price'] = pq($item)->find('span.wlPriceBold strong')->html();
					$array[$i]['date-added'] = pq($item)->find('span.commentBlock nobr')->html();
					$array[$i]['priority'] = pq($item)->find('span.priorityValueText')->html();
					$array[$i]['rating'] = pq($item)->find('span.asinReviewsSummary a span span')->html();
					$array[$i]['total-ratings'] = pq($item)->find('span.crAvgStars a:nth-child(2)')->html();
					$array[$i]['comment'] = pq($item)->find('span.commentValueText')->html();
					$array[$i]['picture'] = pq($item)->find('td.productImage a img')->attr('src');
					
					$i++;
				}
			}			
		}	
	}
}


//format the xml
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

//?format=json
//format the wishlist (json, xml, or php array object) defaults to json
if($_REQUEST['output'] == 'json') { echo json_encode($array); }
else if($_REQUEST['format'] == 'xml') { echo xml_ecode($array); }
else if($_REQUEST['format'] == 'array') { print_r($array); }
else { echo json_encode($array); }

?>