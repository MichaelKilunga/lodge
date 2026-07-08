# Project Rules & SEO Guidelines (`d:\lodge`)

## Technical SEO & Meta Tag Standards
1. **Global Meta Tags (`template/public.blade.php`)**:
   - Always include `<link rel="canonical" href="{{ url()->current() }}" />` in the base layout header.
   - Provide fallback-equipped `@yield` directives for `meta_description`, `meta_keywords`, and `robots` (`index, follow`).
2. **Social Sharing Cards**:
   - Every public layout/view must define both OpenGraph (`og:title`, `og:description`, `og:image`, `og:url`, `og:type`, `og:site_name`) and Twitter Card (`twitter:card`, `twitter:title`, `twitter:description`, `twitter:image`) meta tags.
   - Always fallback `og:image` to `$global_settings['hero_image_path']` or `asset('img/default/default-room.png')` if no specific page/post image exists.

## Schema.org JSON-LD Structured Data
Whenever building or updating public views, inject relevant Schema.org JSON-LD scripts inside the `@section('head')` block:
- **Global Layout (`template/public.blade.php`)**: `LodgingBusiness` with NAP (Name, Address, Phone), `priceRange`, and `starRating`.
- **Home & Room Listings (`public/home.blade.php`, `public/rooms.blade.php`)**: `ItemList` enumerating featured or available rooms with `offers` and `occupancy`.
- **Room Details (`public/room_details.blade.php`)**: `HotelRoom` (including `BedDetails`, `occupancy`, and `Offer`) alongside `BreadcrumbList`.
- **Blog Articles (`public/blog/show.blade.php`)**: `BlogPosting` / `Article` (declaring `headline`, `author`, `publisher`, `datePublished`) alongside `BreadcrumbList`.
- **Location & Contact (`public/location.blade.php`)**: `LocalBusiness` / `Place` including exact `GeoCoordinates` (`latitude`, `longitude`).

## Controller & Routing Conventions for SEO
1. **Dynamic XML Sitemaps (`SitemapController`)**:
   - When rendering XML sitemaps, return the view response cleanly using all 4 arguments:
     ```php
     return response()->view('template.sitemap', compact('rooms', 'posts'), 200, [
         'Content-Type' => 'application/xml'
     ]);
     ```
     *(This avoids IDE static analysis warnings regarding missing arguments and ensures clean content-type headers).*
2. **Blog & Content Controllers (`PostController`)**:
   - Automatically calculate estimated reading time when creating/updating content (`ceil(str_word_count(strip_tags($content)) / 200)`).
   - Auto-populate `meta_title`, `meta_description`, and `excerpt` with sensible defaults from the content if left blank by the user/admin.
