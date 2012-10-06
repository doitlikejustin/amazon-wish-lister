<?php

if(isset($_GET['id'])) $amazon_id = $_GET['id'];
else $amazon_id = '37XI10RRD17X2';

//get the file contents of the outputted json
$get_wishlist = file_get_contents("http://www.justinscarpetti.com/projects/amazon-wish-lister/api/?id=$amazon_id&format=json");

//convert the json to an array
//if json_decode is set to true it outputs as an array
//if set to false, it outputs as an object
$data = json_decode($get_wishlist, true);
print_r($data);

$total_items = count($data);
$item_number = 0;

function get_item_data($item_type)
{
	//gets the json decoded array outside the funtion
	global $data;
	
	//get the current item number from for() loop
	global $item_number;
		
	//get the item being called in the function from the array
	//same as $item_data = $data[0]['price'];
	$item_data = $data[$item_number][$item_type];
	
	if($item_type == "name")
	{
		//max length the string should be
		$max_length = 50;
		
		if(strlen($item_data) > $max_length)
		{
			$item_data = substr($item_data, 0, $max_length) . "...";
			return $item_data;
		}
	}
	
	//get the prioty and returns a string which will be appended to an image
	else if($item_type == "priority")
	{
		//return string (highest - loweset) or (5 - 1)
		//this returned string will be appened to the .png image
		switch ($item_data) {
		    case "highest":
		        return "highest";
		        break;
		    case "high":
		        return "high";
		        break;
		    case "medium":
		        return "medium";
		        break;
		    case "low":
		        return "low";
		        break;
		    case "lowest":
		        return "lowest";
		        break;
		    default:
		        return "medium";
		}		
	}

	//calculate the rating and return a value
	else if($item_type == "rating")
	{
		if($item_data > 4.8) { return 5; }
		else if($item_data > 3.9) { return 4; }
		else if($item_data > 2.9) { return 3; }
		else if($item_data > 1.9) { return 2; }
		else if($item_data > 0.9) { return 1; }
		else { return 0; }
	}
	
	else if($item_type == "new-price")
	{
		if($item_data == '')
		{
			$item_data = "N/A";
		}
	}
	
	return $item_data;
}

//if the array returns and error then echo "echo"
if(isset($data['error']))
{
	//show an error
	echo("error");
}

//if there are no errors, format and output the data
else
{
	echo "<header>Amazon Wish Lister Example";
	if(isset($total_items)) echo " / $total_items total items";
	echo '</header>';

	//loop through 
	for($i = 0; $i < $total_items; $i++)
	{
		echo '<div class="item">';
		echo '<h2><a rel="external" href="' . get_item_data("link") . '">' . get_item_data("name") . '</a></h2>';
		echo '<div class="top">';
			//echo '<div class="date-added">Added on <span>' . get_item_data("date-added") . '</span></div>';
			if(get_item_data("rating") != "")
			{
				echo '<div class="rating"><img src="images/rating-' . get_item_data("rating") . '.png" alt="' . get_item_data("rating") . ' star review" title="' . get_item_data("rating") . ' star review" width="110" height="22" /> ';
				echo '<span class="num-ratings">(<a href="' . get_item_data("link") . '#customerReviews">' . get_item_data("total-ratings") . ' Reviews</a>)</span></div>';
			}
			else
			{
				echo '<div class="rating"><img src="images/rating-0.png" alt="No ratings" title="No ratings" width="110" height="22" /> ';
				echo '<span class="num-ratings">(No reviews)</span></div>';
			}
		echo '</div>'; //end top
		echo '<div class="bottom">';
			echo '<img class="picture" src="' . get_item_data("picture") . '" alt="' . get_item_data("item") . ' " title="' . get_item_data("item") . '" />';
			echo '<div class="price"><span>' . get_item_data("new-price") . '</span></div>';
			echo '<div class="buy"><a href="' . get_item_data("link") . '">BUY ME</a></div>';
			echo '<div class="priority"><img src="images/priority-' . get_item_data("priority") . '.png" alt="' . get_item_data("priority") . ' rating" title="' . get_item_data("priority") . ' rating" width="124" height="24" /></div>';
		echo '</div>'; //end bottom
		if(get_item_data("comment") != "")
		{
			echo '<div class="comment-top"><div class="comment">Comment: <span>' . get_item_data("comment") . '</span></div></div>';
		}
		echo '</div>'; //end item
	
		$item_number++;
	}
}

?>