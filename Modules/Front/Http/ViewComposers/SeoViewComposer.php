<?php

declare(strict_types=1);

namespace Modules\Front\Http\ViewComposers;

use Illuminate\View\View;
use Modules\Front\Services\SeoService;

class SeoViewComposer
{
    public function __construct(
        private readonly SeoService $seoService
    ) {}

    public function compose(View $view): void
    {
        $data = $view->getData();

        // Generate base SEO data
        $seoData = $this->generateSeoData($data);

        // Add to view
        $view->with('seo', $seoData);
    }

    private function generateSeoData(array $data): array
    {
        $seoData = [
            'title' => config('app.name'),
            'description' => 'Online shopping store with quality products and fast delivery.',
            'keywords' => 'online shopping, ecommerce, products, deals, discounts',
            'canonical' => request()->url(),
            'og' => [],
            'twitter' => [],
            'schema' => [],
        ];

        // Determine page type and generate appropriate SEO data
        if (isset($data['product_detail'])) {
            $seoData = $this->generateProductSeo($data['product_detail'], $seoData);
        } elseif (isset($data['post'])) {
            $seoData = $this->generatePostSeo($data['post'], $seoData);
        } elseif (isset($data['category'])) {
            $seoData = $this->generateCategorySeo($data['category'], $seoData);
        } elseif (isset($data['brand'])) {
            $seoData = $this->generateBrandSeo($data['brand'], $seoData);
        } elseif (request()->is('/')) {
            $seoData = $this->generateHomeSeo($seoData);
        }

        return $seoData;
    }

    private function generateProductSeo($product, array $seoData): array
    {
        $seoData['title'] = $this->seoService->generateTitle($product->title, 'product');
        $seoData['description'] = $this->seoService->generateDescription($product->summary ?? $product->description, 'product');
        $seoData['keywords'] = $this->seoService->generateKeywords(['online shopping', 'buy online'], 'product', $product);
        $seoData['canonical'] = $this->seoService->generateCanonicalUrl('front.product-detail', ['slug' => $product->slug]);
        $seoData['og'] = $this->seoService->generateOpenGraphData('product', $product);
        $seoData['twitter'] = $this->seoService->generateTwitterCardData('product', $product);
        $seoData['schema'] = $this->seoService->generateProductSchema($product);

        return $seoData;
    }

    private function generatePostSeo(?object $post, array $seoData): array
    {
        $seoData['title'] = $this->seoService->generateTitle($post->title, 'blog');
        $seoData['description'] = $this->seoService->generateDescription($post->summary ?? $post->description, 'blog');
        $seoData['keywords'] = $this->seoService->generateKeywords(['blog', 'article', 'news'], 'blog', $post);
        $seoData['canonical'] = $this->seoService->generateCanonicalUrl('front.blog-detail', ['slug' => $post->slug]);
        $seoData['og'] = $this->seoService->generateOpenGraphData('article', $post);
        $seoData['twitter'] = $this->seoService->generateTwitterCardData('article', $post);

        return $seoData;
    }

    private function generateCategorySeo(?object $category, array $seoData): array
    {
        $seoData['title'] = $this->seoService->generateTitle($category->title, 'category');
        $seoData['description'] = $this->seoService->generateDescription($category->summary ?? $category->description, 'category');
        $seoData['keywords'] = $this->seoService->generateKeywords(['products', 'shop online'], 'category', $category);
        $seoData['canonical'] = $this->seoService->generateCanonicalUrl('front.product-cat', ['slug' => $category->slug]);
        $seoData['og'] = $this->seoService->generateOpenGraphData('website', $category);
        $seoData['twitter'] = $this->seoService->generateTwitterCardData('website', $category);

        return $seoData;
    }

    private function generateBrandSeo(?object $brand, array $seoData): array
    {
        $seoData['title'] = $this->seoService->generateTitle($brand->title, 'brand');
        $seoData['description'] = $this->seoService->generateDescription($brand->description ?? '', 'brand');
        $seoData['keywords'] = $this->seoService->generateKeywords(['official store', 'authentic'], 'brand', $brand);
        $seoData['canonical'] = $this->seoService->generateCanonicalUrl('front.product-brand', ['slug' => $brand->slug]);
        $seoData['og'] = $this->seoService->generateOpenGraphData('website', $brand);
        $seoData['twitter'] = $this->seoService->generateTwitterCardData('website', $brand);

        return $seoData;
    }

    private function generateHomeSeo(array $seoData): array
    {
        $seoData['title'] = $this->seoService->generateTitle('', 'home');
        $seoData['description'] = $this->seoService->generateDescription('Shop online for quality products with fast delivery and secure payment. Best deals and discounts available.', 'home');
        $seoData['keywords'] = $this->seoService->generateKeywords(['online shopping', 'ecommerce', 'store'], 'home');
        $seoData['canonical'] = $this->seoService->generateCanonicalUrl('front.index');
        $seoData['og'] = $this->seoService->generateOpenGraphData('website');
        $seoData['twitter'] = $this->seoService->generateTwitterCardData('website');
        $seoData['schema'] = [
            $this->seoService->generateOrganizationSchema(),
            $this->seoService->generateWebsiteSchema(),
        ];

        return $seoData;
    }
}
