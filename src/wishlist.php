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
set_time_limit(60);
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
						$array[$i]['name'] = htmlentities(pq($item)->find('span.productTitle strong a')->html(), ENT_COMPAT|ENT_HTML401, 'UTF-8', FALSE);
						$array[$i]['link'] = pq($item)->find('span.productTitle a')->attr('href');
						$array[$i]['old-price'] = pq($item)->find('span.price a-text-strike')->html();
						$array[$i]['new-price'] = pq($item)->find('span.priceblock_ourprice')->html();
						$array[$i]['date-added'] = str_replace('Added', '', pq($item)->find('span.commentBlock nobr')->html());
						$array[$i]['priority'] = pq($item)->find('span.priorityValueText')->html();
						$array[$i]['rating'] = pq($item)->find('span.asinReviewsSummary a span span')->html();
						$array[$i]['total-ratings'] = pq($item)->find('span.crAvgStars a:nth-child(2)')->html();
						$array[$i]['comment'] = pq($item)->find('span.commentValueText')->html();
						$array[$i]['picture'] = pq($item)->find('td.productImage a img')->attr('src');
						$array[$i]['page'] = $page_num;
						
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
					$name = trim(htmlentities(pq($item)->find('a[id^="itemName_"]')->html(), ENT_COMPAT|ENT_HTML401, 'UTF-8', FALSE));
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
						$array[$i]['new-price'] = trim(pq($item)->find('div.a-spacing-small div.a-row span.a-size-medium.a-color-price')->html());
						$array[$i]['date-added'] = trim(str_replace('Added', '', pq($item)->find('div[id^="itemAction_"] .a-size-small')->html()));
						$array[$i]['priority'] = trim(pq($item)->find('span[id^="itemPriorityLabel_"]')->html());
						$array[$i]['rating'] = 'N/A';
						$array[$i]['total-ratings'] = $total_ratings;
						$array[$i]['comment'] = trim(pq($item)->find('span[id^="itemComment_"]')->html());
						$array[$i]['picture'] = pq($item)->find('div[id^="itemImage_"] img')->attr('src');
						$array[$i]['page'] = $page_num;
						
						$i++;
					}
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
if($_REQUEST['format'] == 'json') { echo json_encode($array); }
elseif($_REQUEST['format'] == 'xml') { echo xml_ecode($array); }
elseif($_REQUEST['format'] == 'array') { print_r($array); }
else { echo json_encode($array); }

?>
