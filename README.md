Amazon Wish Lister
==================

This is a little API to retrieve Amazon Wish List data. There is no official API, as Amazon shut it down a couple years ago. The only way around that... screen scraping.

Amazon Wish Lister uses [phpQuery](http://code.google.com/p/phpquery/) (server-side CSS3 selector driven DOM API based on jQuery) to scrape Amazon's Wish List page and exports to JSON, XML, or PHP Array Object.

* Scrapes the following from your Amazon Wish List:
    1. Item name
    2. Item link
    3. Price of item when added to wish list
    4. Current price of item
    5. Date added to wish list
    6. Priority (set by you)
    7. Item rating
    8. Total ratings
    9. Comments on item (set by you)
    10. Picture of item
* Perfect if you want to host display your wish list on your own website. 
* Best used if cached, or saved in database.
* Supports multi-page Amazon Wish Lists as well as Amazon Wish List "Ideas"
* Return list as JSON, XML, or just dump PHP Array Object.

**Demo Site:** [http://www.justinscarpetti.com/projects/amazon-wish-lister/](http://www.justinscarpetti.com/projects/amazon-wish-lister/)  
**JSON Output:** [http://www.justinscarpetti.com/projects/amazon-wish-lister/api/?id=37XI10RRD17X2](http://www.justinscarpetti.com/projects/amazon-wish-lister/api/?id=37XI10RRD17X2)

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

What it returns
===============

Below is an exmaple if you had http://amzn.com/B0002FTH66 on your wishlist (item #37 on your wishlist).

    [
        {
            "num": 37,
            "name": "Scotch Box Sealing Tape Dispenser H180, 2 in",
            "link": "http://www.amazon.com/Scotch-Sealing-Tape-Dispenser-H180/dp/B0002FTH66/ref=wl_it_dp_v_nS_nC/185-8110132-3235609?ie=UTF8&colid=3DR0P4HP87IIJ&coliid=I19JS64ZHWBA5M",
            "old-price": "$24.09",
            "new-price": "$19.99",
            "date-added": "Added June 7, 2012",
            "priority": "low",
            "rating": "4.7 out of 5 stars",
            "total-ratings": "63",
            "comment": "I like taping stuff",
            "picture": "http://ecx.images-amazon.com/images/I/41BKbZu836L._SL500_SL135_.jpg",
            "page": 2
        }
    ]



Support
=======

https://github.com/doitlikejustin/amazon-wish-lister/issues
