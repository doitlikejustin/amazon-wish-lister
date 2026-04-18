# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Amazon Wish Lister is a PHP web scraper API that retrieves Amazon wishlist data by scraping Amazon's HTML (Amazon deprecated their official Wishlist API). It returns data in multiple formats (JSON, XML, RSS, or PHP array) via query parameters.

## No Build or Test Commands

This project has no build system, test suite, or linter configured. There is no `package.json`, `Makefile`, or `phpunit.xml`. Testing is done manually by serving the PHP files and making HTTP requests.

To run locally, serve the `src/` directory with any PHP-capable web server:

```bash
php -S localhost:8000 -t src/
```

Then test via: `http://localhost:8000/wishlist.php?id=<AMAZON_WISHLIST_ID>`

## Architecture

The entire API lives in two files:

- **`src/wishlist.php`** — The scraping engine. Accepts `$_GET` parameters, fetches Amazon wishlist pages using phpQuery, parses item data, and outputs the result.
- **`src/phpquery.php`** — Bundled copy of phpQuery v0.9.5 (a jQuery-like server-side DOM library). Do not modify this file.

The `example/` directory contains a reference frontend implementation using jQuery AJAX to consume the API — it is not part of the core library.

## How `wishlist.php` Works

**Request parameters** (all via `$_GET`):
- `id` — Amazon wishlist ID (required)
- `country` — Amazon domain suffix, e.g. `com`, `co.uk`, `de` (default: `com`)
- `sort` — Sort order passed directly to Amazon URL
- `format` — Output format: `json` (default), `xml`, `xmlnew`, `rss`, `array`
- `affiliate` — Amazon affiliate tag to append to product URLs
- `isbn` / `author` — Set to `1` to fetch additional detail pages per item (slow — one request per item)

**Dual-layout scraping:** Amazon has two wishlist HTML layouts. The code first tries the old layout (CSS selector `tbody.itemWrapper`), then falls back to the new layout (`.g-items-section div[id^="item_"]`). Any changes to Amazon's DOM will require updating these selectors.

**Pagination:** The scraper loops through all pages automatically by incrementing `?page=` until no more items are found.

**Output:** Each item is collected into a PHP array and then serialized into the requested format with the appropriate `Content-Type` header.

## Key Conventions

- **Procedural style throughout** — no classes, no namespaces. Global variables (`$baseurl`, `$affiliate_tag`) are used across functions.
- **phpQuery selectors** are the primary means of data extraction. When Amazon changes their HTML, the fix is always updating the CSS selectors in `wishlist.php`.
- Helper functions in `wishlist.php`: `text_prepare()`, `get_ASIN()`, `get_large_ssl_image()`, `get_affiliate_link()`, `get_ISBN()`, `get_Author()`.
- `error_reporting(E_ERROR)` is set intentionally to suppress phpQuery warnings during scraping.
- The project has no managed Composer dependencies; `phpquery.php` is vendored directly.
