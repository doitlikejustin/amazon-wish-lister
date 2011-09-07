<?php

require_once('phpquery.php');

$amazon_id = '37XI10RRD17X2';
phpQuery::newDocumentFile('http://www.amazon.com/registry/wishlist/' . $amazon_id . '?reveal=unpurchased&filter=all&layout=standard');

$i = 0;
$items = pq('tbody.itemWrapper');

foreach($items as $item)
{
	$check_if_regular = pq($item)->find('span.commentBlock nobr');	
	
	if($check_if_regular != '')
	{	
		//$array[$i]['array'] = pq($item)->html();
		$array[$i]['name'] = pq($item)->find('span.productTitle strong a')->html();
		$array[$i]['link'] = pq($item)->find('span.productTitle a')->attr('href');
		$array[$i]['price'] = pq($item)->find('span.wlPriceBold strong')->html();
		$array[$i]['date-added'] = pq($item)->find('span.commentBlock nobr')->html();
		$array[$i]['priority'] = pq($item)->find('span.priorityValueText')->html();
		$array[$i]['rating'] = pq($item)->find('span.asinReviewsSummary a span span')->html();
		$array[$i]['total-ratings'] = pq($item)->find('span.crAvgStars a:nth-child(2)')->html();
		$array[$i]['comment'] = pq($item)->find('span.commentValueText')->html();
		$array[$i]['picture'] = pq($item)->find('td.productImage a img')->attr('src');

		$i++;
	}
}

//?format=json
if($_REQUEST['format'] == 'json') { json_encode($array); }

//?format=array
if($_REQUEST['format'] == 'array') { print_r($array); }

//?format=xml
else { print_r($array); }

?>