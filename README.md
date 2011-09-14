Amazon Wish Lister
==================

This is a little API to retrieve Amazon Wish List data. There is no official API, as Amazon shut it down a couple years ago. The only way around that... screen scraping.

Amazon Wish Lister uses [phpQuery](http://code.google.com/p/phpquery/) (server-side CSS3 selector driven DOM API based on jQuery) to scrape Amazon's Wish List page and exports to JSON, XML, or PHP Array Object.

Perfect if you want to host display your wish list on your own website.

Demo Site: [http://www.justinscarpetti.com/projects/amazon-wish-lister/](http://www.justinscarpetti.com/projects/amazon-wish-lister/)
JSON Output: [http://www.justinscarpetti.com/projects/amazon-wish-lister/api/?id=37XI10RRD17X2](http://www.justinscarpetti.com/projects/amazon-wish-lister/api/?id=37XI10RRD17X2)

How to use
==========

You just need to add a few parameters to get your wish list. All of the parameters, except for the Amazon ID, will have a default... well the default for the Amazon ID is my Amazon ID, you probably don't want that.

The rest (how you style it) is up to you. Happy coding.

### Amazon ID
`?id=YOUR_AMAZON_ID`  
`?id=37XI10RRD17X2`

### Reveal (What to get)
`?reveal=unpurchased`  
`?reveal=all`  
`?reveal=purchased`

### Sort
`?sort=date`  
`?sort=priority`  
`?sort=title`  
`?sort=price-low (low to high)`  
`?sort=price-high (high to low)`  
`?sort=updated`

### Output Format
`?format=json`  
`?format=xml`  
`?format=array`

### Example
`wishlist.php?id=37XI10RRD17X2&reveal=all&sort=priority&format=json`

Support
=======

https://github.com/doitlikejustin/amazon-wish-lister/issues