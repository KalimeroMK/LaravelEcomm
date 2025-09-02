<?php

declare(strict_types=1);

namespace Modules\Front\Services;

use Illuminate\Support\Str;
use Modules\Product\Models\Product;
use Modules\Post\Models\Post;
use Modules\Category\Models\Category;
use Modules\Brand\Models\Brand;

class SeoService
{
    /**
     * Generate dynamic meta title
     */
    public function generateTitle(string $title, string $type = 'page', ?string $siteName = null): string
    {
        $siteName = $siteName ?? config('app.name');

        return match ($type) {
            'product' => "{$title} - Buy Online | {$siteName}",
            'category' => "{$title} Products - Shop Online | {$siteName}",
            'brand' => "{$title} Products - Official Store | {$siteName}",
            'blog' => "{$title} - Blog | {$siteName}",
            'home' => "{$siteName} - Online Shopping Store",
            default => "{$title} | {$siteName}",
        };
    }

    /**
     * Generate dynamic meta description
     */
    public function generateDescription(string $content, string $type = 'page', int $length = 160): string
    {
        $description = strip_tags($content);
        $description = Str::limit($description, $length);

        return match ($type) {
            'product' => "Shop {$description} online. Fast shipping, secure payment, and great prices. Order now!",
            'category' => "Browse our collection of {$description}. Quality products at competitive prices. Shop now!",
            'brand' => "Discover {$description} products. Official store with authentic items and fast delivery.",
            'blog' => "Read about {$description}. Latest news, tips, and insights from our blog.",
            default => $description,
        };
    }

    /**
     * Generate keywords for a page
     */
    public function generateKeywords(array $baseKeywords, string $type = 'page', ?object $model = null): string
    {
        $keywords = $baseKeywords;

        if ($model) {
            $keywords = array_merge($keywords, $this->extractModelKeywords($model, $type));
        }

        // Add type-specific keywords
        $keywords = array_merge($keywords, $this->getTypeKeywords($type));

        return implode(', ', array_unique($keywords));
    }

    /**
     * Extract keywords from model
     */
    private function extractModelKeywords(object $model, string $type): array
    {
        $keywords = [];

        if ($model instanceof Product) {
            $keywords[] = $model->title;
            $keywords[] = $model->brand?->title;
            $keywords[] = $model->categories->pluck('title')->toArray();
            $keywords[] = 'buy online';
            $keywords[] = 'shop now';
        } elseif ($model instanceof Category) {
            $keywords[] = $model->title;
            $keywords[] = 'products';
            $keywords[] = 'shop online';
        } elseif ($model instanceof Brand) {
            $keywords[] = $model->title;
            $keywords[] = 'official store';
            $keywords[] = 'authentic';
        } elseif ($model instanceof Post) {
            $keywords[] = $model->title;
            $keywords[] = $model->tags->pluck('title')->toArray();
            $keywords[] = 'blog';
            $keywords[] = 'article';
        }

        return array_filter(array_merge(...array_map(fn($k) => is_array($k) ? $k : [$k], $keywords)));
    }

    /**
     * Get type-specific keywords
     */
    private function getTypeKeywords(string $type): array
    {
        return match ($type) {
            'product' => ['online shopping', 'ecommerce', 'buy now', 'fast shipping'],
            'category' => ['products', 'shop', 'online store', 'categories'],
            'brand' => ['official', 'authentic', 'brand store', 'genuine'],
            'blog' => ['blog', 'article', 'news', 'tips', 'guide'],
            'home' => ['online shopping', 'ecommerce', 'store', 'products', 'deals'],
            default => ['online', 'shopping', 'store'],
        };
    }

    /**
     * Generate canonical URL
     */
    public function generateCanonicalUrl(string $route, array $parameters = []): string
    {
        return route($route, $parameters);
    }

    /**
     * Generate Open Graph data
     */
    public function generateOpenGraphData(string $type, object $model = null): array
    {
        $baseData = [
            'og:type' => $type,
            'og:site_name' => config('app.name'),
            'og:locale' => 'en_US',
        ];

        if ($model) {
            $baseData = array_merge($baseData, $this->getModelOpenGraphData($model, $type));
        }

        return $baseData;
    }

    /**
     * Get model-specific Open Graph data
     */
    private function getModelOpenGraphData(object $model, string $type): array
    {
        if ($model instanceof Product) {
            return [
                'og:title' => $this->generateTitle($model->title, 'product'),
                'og:description' => $this->generateDescription($model->summary ?? $model->description, 'product'),
                'og:image' => $model->imageUrl,
                'og:url' => route('front.product-detail', $model->slug),
                'product:price:amount' => $model->price,
                'product:price:currency' => 'USD',
                'product:availability' => $model->stock > 0 ? 'in stock' : 'out of stock',
            ];
        } elseif ($model instanceof Post) {
            return [
                'og:title' => $this->generateTitle($model->title, 'blog'),
                'og:description' => $this->generateDescription($model->summary ?? $model->description, 'blog'),
                'og:image' => $model->imageUrl,
                'og:url' => route('front.blog-detail', $model->slug),
                'article:published_time' => $model->created_at->toISOString(),
                'article:author' => $model->user?->name ?? 'Admin',
            ];
        } elseif ($model instanceof Category) {
            return [
                'og:title' => $this->generateTitle($model->title, 'category'),
                'og:description' => $this->generateDescription($model->summary ?? $model->description, 'category'),
                'og:url' => route('front.product-cat', $model->slug),
            ];
        }

        return [];
    }

    /**
     * Generate Twitter Card data
     */
    public function generateTwitterCardData(string $type, object $model = null): array
    {
        $baseData = [
            'twitter:card' => 'summary_large_image',
            'twitter:site' => '@' . config('app.name'),
        ];

        if ($model) {
            $baseData = array_merge($baseData, $this->getModelTwitterData($model, $type));
        }

        return $baseData;
    }

    /**
     * Get model-specific Twitter data
     */
    private function getModelTwitterData(object $model, string $type): array
    {
        if ($model instanceof Product) {
            return [
                'twitter:title' => $this->generateTitle($model->title, 'product'),
                'twitter:description' => $this->generateDescription($model->summary ?? $model->description, 'product'),
                'twitter:image' => $model->imageUrl,
            ];
        } elseif ($model instanceof Post) {
            return [
                'twitter:title' => $this->generateTitle($model->title, 'blog'),
                'twitter:description' => $this->generateDescription($model->summary ?? $model->description, 'blog'),
                'twitter:image' => $model->imageUrl,
            ];
        }

        return [];
    }

    /**
     * Generate breadcrumb data
     */
    public function generateBreadcrumbs(array $items): array
    {
        $breadcrumbs = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => route('front.index'),
            ],
        ];

        $position = 2;
        foreach ($items as $item) {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs,
        ];
    }

    /**
     * Generate product schema
     */
    public function generateProductSchema(Product $product): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->title,
            'description' => $product->description,
            'image' => $product->imageUrl,
            'url' => route('front.product-detail', $product->slug),
            'sku' => $product->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand?->title ?? 'Unknown',
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $product->price,
                'priceCurrency' => 'USD',
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                ],
            ],
        ];

        if ($product->categories->isNotEmpty()) {
            $schema['category'] = $product->categories->first()->title;
        }

        if ($product->discount && $product->discount > 0) {
            $schema['offers']['priceValidUntil'] = now()->addDays(30)->format('Y-m-d');
        }

        return $schema;
    }

    /**
     * Generate organization schema
     */
    public function generateOrganizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'logo' => config('app.url') . '/assets/img/logo/logo.png',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+1-555-123-4567',
                'contactType' => 'customer service',
                'areaServed' => 'US',
                'availableLanguage' => 'English',
            ],
            'sameAs' => [
                'https://www.facebook.com/yourpage',
                'https://www.twitter.com/yourpage',
                'https://www.instagram.com/yourpage',
            ],
        ];
    }

    /**
     * Generate website schema
     */
    public function generateWebsiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => config('app.url') . '/product/search?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }
}
