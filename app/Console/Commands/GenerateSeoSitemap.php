<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

class GenerateSeoSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:generate-sitemap 
                            {--type=all : Type of sitemap to generate (all, products, posts, categories, brands)}
                            {--limit=50000 : Maximum number of URLs per sitemap}
                            {--compress : Compress sitemap with gzip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate comprehensive SEO-optimized sitemaps';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🗺️  Generating SEO-optimized sitemaps...');

        $type = $this->option('type');
        $limit = (int) $this->option('limit');
        $compress = $this->option('compress');

        try {
            switch ($type) {
                case 'products':
                    $this->generateProductsSitemap($limit, $compress);

                    break;
                case 'posts':
                    $this->generatePostsSitemap($limit, $compress);

                    break;
                case 'categories':
                    $this->generateCategoriesSitemap($limit, $compress);

                    break;
                case 'brands':
                    $this->generateBrandsSitemap($limit, $compress);

                    break;
                case 'all':
                default:
                    $this->generateAllSitemaps($limit, $compress);

                    break;
            }

            $this->info('✅ Sitemap generation completed successfully!');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('❌ Error generating sitemap: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    private function generateAllSitemaps(int $limit, bool $compress): void
    {
        $this->generateMainSitemap();
        $this->generateProductsSitemap($limit, $compress);
        $this->generatePostsSitemap($limit, $compress);
        $this->generateCategoriesSitemap($limit, $compress);
        $this->generateBrandsSitemap($limit, $compress);
    }

    private function generateMainSitemap(): void
    {
        $this->info('📄 Generating main sitemap...');

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        $baseUrl = config('app.url');
        $timestamp = now()->format('Y-m-d\TH:i:s\Z');

        // Add sitemap entries
        $sitemaps = [
            ['loc' => $baseUrl.'/sitemap-products.xml', 'lastmod' => $timestamp],
            ['loc' => $baseUrl.'/sitemap-posts.xml', 'lastmod' => $timestamp],
            ['loc' => $baseUrl.'/sitemap-categories.xml', 'lastmod' => $timestamp],
            ['loc' => $baseUrl.'/sitemap-brands.xml', 'lastmod' => $timestamp],
        ];

        foreach ($sitemaps as $sitemapEntry) {
            $sitemap .= '  <sitemap>'."\n";
            $sitemap .= '    <loc>'.$sitemapEntry['loc'].'</loc>'."\n";
            $sitemap .= '    <lastmod>'.$sitemapEntry['lastmod'].'</lastmod>'."\n";
            $sitemap .= '  </sitemap>'."\n";
        }

        $sitemap .= '</sitemapindex>';

        File::put(public_path('sitemap.xml'), $sitemap);
        $this->info('✅ Main sitemap generated: sitemap.xml');
    }

    private function generateProductsSitemap(int $limit, bool $compress): void
    {
        $this->info('🛍️  Generating products sitemap...');

        $products = Product::where('status', 'active')
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $sitemap = $this->generateSitemapXml($products, 'front.product-detail', 'slug');

        $filename = 'sitemap-products.xml';
        File::put(public_path($filename), $sitemap);

        if ($compress) {
            $this->compressSitemap($filename);
        }

        $this->info("✅ Products sitemap generated: {$filename} ({$products->count()} products)");
    }

    private function generatePostsSitemap(int $limit, bool $compress): void
    {
        $this->info('📝 Generating posts sitemap...');

        $posts = Post::where('status', 'active')
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $sitemap = $this->generateSitemapXml($posts, 'front.blog-detail', 'slug');

        $filename = 'sitemap-posts.xml';
        File::put(public_path($filename), $sitemap);

        if ($compress) {
            $this->compressSitemap($filename);
        }

        $this->info("✅ Posts sitemap generated: {$filename} ({$posts->count()} posts)");
    }

    private function generateCategoriesSitemap(int $limit, bool $compress): void
    {
        $this->info('📂 Generating categories sitemap...');

        $categories = Category::where('status', 'active')
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $sitemap = $this->generateSitemapXml($categories, 'front.product-cat', 'slug');

        $filename = 'sitemap-categories.xml';
        File::put(public_path($filename), $sitemap);

        if ($compress) {
            $this->compressSitemap($filename);
        }

        $this->info("✅ Categories sitemap generated: {$filename} ({$categories->count()} categories)");
    }

    private function generateBrandsSitemap(int $limit, bool $compress): void
    {
        $this->info('🏷️  Generating brands sitemap...');

        $brands = Brand::where('status', 'active')
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $sitemap = $this->generateSitemapXml($brands, 'front.product-brand', 'slug');

        $filename = 'sitemap-brands.xml';
        File::put(public_path($filename), $sitemap);

        if ($compress) {
            $this->compressSitemap($filename);
        }

        $this->info("✅ Brands sitemap generated: {$filename} ({$brands->count()} brands)");
    }

    private function generateSitemapXml($items, string $route, string $paramKey): string
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        $baseUrl = config('app.url');

        foreach ($items as $item) {
            $url = route($route, [$paramKey => $item->slug]);
            $lastmod = $item->updated_at->format('Y-m-d\TH:i:s\Z');

            $sitemap .= '  <url>'."\n";
            $sitemap .= '    <loc>'.$url.'</loc>'."\n";
            $sitemap .= '    <lastmod>'.$lastmod.'</lastmod>'."\n";
            $sitemap .= '    <changefreq>weekly</changefreq>'."\n";
            $sitemap .= '    <priority>0.8</priority>'."\n";
            $sitemap .= '  </url>'."\n";
        }

        $sitemap .= '</urlset>';

        return $sitemap;
    }

    private function compressSitemap(string $filename): void
    {
        $filePath = public_path($filename);
        $compressedPath = $filePath.'.gz';

        $content = File::get($filePath);
        $compressed = gzencode($content, 9);

        File::put($compressedPath, $compressed);
        $this->info("📦 Compressed sitemap: {$filename}.gz");
    }
}
