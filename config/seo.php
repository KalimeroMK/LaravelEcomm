<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | SEO Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for SEO optimization features
    | including meta tags, structured data, and performance optimizations.
    |
    */

    'site' => [
        'name' => env('SEO_SITE_NAME', config('app.name')),
        'description' => env('SEO_SITE_DESCRIPTION', 'Online shopping store with quality products and fast delivery.'),
        'keywords' => env('SEO_SITE_KEYWORDS', 'online shopping, ecommerce, products, deals, discounts'),
        'author' => env('SEO_SITE_AUTHOR', 'E-commerce Store'),
        'language' => env('SEO_SITE_LANGUAGE', 'en'),
        'locale' => env('SEO_SITE_LOCALE', 'en_US'),
    ],

    'meta' => [
        'default_title' => env('SEO_DEFAULT_TITLE', 'Online Shopping Store'),
        'title_separator' => env('SEO_TITLE_SEPARATOR', ' | '),
        'title_suffix' => env('SEO_TITLE_SUFFIX', ''),
        'description_length' => env('SEO_DESCRIPTION_LENGTH', 160),
        'keywords_count' => env('SEO_KEYWORDS_COUNT', 10),
    ],

    'open_graph' => [
        'enabled' => env('SEO_OG_ENABLED', true),
        'app_id' => env('SEO_OG_APP_ID', ''),
        'site_name' => env('SEO_OG_SITE_NAME', config('app.name')),
        'locale' => env('SEO_OG_LOCALE', 'en_US'),
        'image' => env('SEO_OG_IMAGE', '/assets/img/logo/logo.png'),
        'image_width' => env('SEO_OG_IMAGE_WIDTH', 1200),
        'image_height' => env('SEO_OG_IMAGE_HEIGHT', 630),
    ],

    'twitter' => [
        'enabled' => env('SEO_TWITTER_ENABLED', true),
        'card' => env('SEO_TWITTER_CARD', 'summary_large_image'),
        'site' => env('SEO_TWITTER_SITE', '@yourstore'),
        'creator' => env('SEO_TWITTER_CREATOR', '@yourstore'),
    ],

    'structured_data' => [
        'enabled' => env('SEO_STRUCTURED_DATA_ENABLED', true),
        'organization' => [
            'name' => env('SEO_ORG_NAME', config('app.name')),
            'url' => env('SEO_ORG_URL', config('app.url')),
            'logo' => env('SEO_ORG_LOGO', '/assets/img/logo/logo.png'),
            'contact_point' => [
                'telephone' => env('SEO_ORG_PHONE', '+1-555-123-4567'),
                'contact_type' => 'customer service',
                'area_served' => 'US',
                'available_language' => 'English',
            ],
            'same_as' => [
                env('SEO_ORG_FACEBOOK', 'https://www.facebook.com/yourpage'),
                env('SEO_ORG_TWITTER', 'https://www.twitter.com/yourpage'),
                env('SEO_ORG_INSTAGRAM', 'https://www.instagram.com/yourpage'),
            ],
        ],
    ],

    'sitemap' => [
        'enabled' => env('SEO_SITEMAP_ENABLED', true),
        'max_urls' => env('SEO_SITEMAP_MAX_URLS', 50000),
        'compress' => env('SEO_SITEMAP_COMPRESS', true),
        'cache_duration' => env('SEO_SITEMAP_CACHE_DURATION', 3600), // 1 hour
        'priorities' => [
            'home' => 1.0,
            'category' => 0.8,
            'product' => 0.8,
            'post' => 0.7,
            'brand' => 0.6,
            'page' => 0.5,
        ],
        'changefreq' => [
            'home' => 'daily',
            'category' => 'weekly',
            'product' => 'weekly',
            'post' => 'monthly',
            'brand' => 'monthly',
            'page' => 'monthly',
        ],
    ],

    'robots' => [
        'enabled' => env('SEO_ROBOTS_ENABLED', true),
        'user_agent' => '*',
        'disallow' => [
            '/admin/',
            '/backend/',
            '/api/',
            '/storage/',
            '/vendor/',
            '/uploads/',
            '/cart/',
            '/checkout/',
            '/user/',
            '/login',
            '/register',
            '/password/',
            '/email/',
            '/magic/',
        ],
        'allow' => [
            '/product-detail/',
            '/product-cat/',
            '/product-brand/',
            '/blog/',
            '/blog-detail/',
            '/about-us',
            '/contact',
        ],
        'crawl_delay' => env('SEO_ROBOTS_CRAWL_DELAY', 1),
    ],

    'performance' => [
        'enabled' => env('SEO_PERFORMANCE_ENABLED', true),
        'lazy_loading' => env('SEO_LAZY_LOADING', true),
        'image_optimization' => env('SEO_IMAGE_OPTIMIZATION', true),
        'css_minification' => env('SEO_CSS_MINIFICATION', true),
        'js_minification' => env('SEO_JS_MINIFICATION', true),
        'preload_critical' => env('SEO_PRELOAD_CRITICAL', true),
        'preconnect_domains' => [
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
            'https://www.google-analytics.com',
        ],
    ],

    'analytics' => [
        'google_analytics' => env('SEO_GA_ID', ''),
        'google_tag_manager' => env('SEO_GTM_ID', ''),
        'facebook_pixel' => env('SEO_FB_PIXEL', ''),
        'google_search_console' => env('SEO_GSC_VERIFICATION', ''),
    ],

    'breadcrumbs' => [
        'enabled' => env('SEO_BREADCRUMBS_ENABLED', true),
        'show_home' => env('SEO_BREADCRUMBS_SHOW_HOME', true),
        'separator' => env('SEO_BREADCRUMBS_SEPARATOR', ' > '),
        'home_text' => env('SEO_BREADCRUMBS_HOME_TEXT', 'Home'),
    ],

    'pagination' => [
        'enabled' => env('SEO_PAGINATION_ENABLED', true),
        'rel_next_prev' => env('SEO_PAGINATION_REL_NEXT_PREV', true),
        'canonical_first_page' => env('SEO_PAGINATION_CANONICAL_FIRST', true),
    ],

    'amp' => [
        'enabled' => env('SEO_AMP_ENABLED', false),
        'product_pages' => env('SEO_AMP_PRODUCT_PAGES', false),
        'blog_posts' => env('SEO_AMP_BLOG_POSTS', false),
    ],

    'cache' => [
        'enabled' => env('SEO_CACHE_ENABLED', true),
        'duration' => env('SEO_CACHE_DURATION', 3600), // 1 hour
        'tags' => ['seo', 'meta', 'sitemap'],
    ],
];
