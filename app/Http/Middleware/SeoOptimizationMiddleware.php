<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeoOptimizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply SEO optimizations to HTML responses
        if (
            $response->headers->get('Content-Type') &&
            str_contains($response->headers->get('Content-Type'), 'text/html')
        ) {

            $content = $response->getContent();

            // Apply SEO optimizations
            $content = $this->optimizeImages($content);
            $content = $this->addLazyLoading($content);
            $content = $this->optimizeInlineStyles($content);
            $content = $this->addPreloadHints($content);

            $response->setContent($content);
        }

        return $response;
    }

    /**
     * Optimize images for SEO and performance
     */
    private function optimizeImages(string $content): string
    {
        // Add loading="lazy" to images that don't have it
        $content = preg_replace(
            '/<img(?![^>]*loading=)([^>]*)>/i',
            '<img loading="lazy"$1>',
            $content
        );

        // Add alt attributes to images that don't have them
        $content = preg_replace(
            '/<img(?![^>]*alt=)([^>]*)>/i',
            '<img alt="Image"$1>',
            $content
        );

        // Add width and height attributes for better CLS
        $content = preg_replace_callback(
            '/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i',
            function ($matches) {
                $beforeSrc = $matches[1];
                $src = $matches[2];
                $afterSrc = $matches[3];

                // Skip if already has width/height
                if (
                    str_contains($beforeSrc.$afterSrc, 'width=') ||
                    str_contains($beforeSrc.$afterSrc, 'height=')
                ) {
                    return $matches[0];
                }

                // Add default dimensions for better CLS
                return '<img'.$beforeSrc.'src="'.$src.'"'.$afterSrc.' width="300" height="200">';
            },
            $content
        );

        return $content;
    }

    /**
     * Add lazy loading to iframes
     */
    private function addLazyLoading(string $content): string
    {
        // Add loading="lazy" to iframes that don't have it
        $content = preg_replace(
            '/<iframe(?![^>]*loading=)([^>]*)>/i',
            '<iframe loading="lazy"$1>',
            $content
        );

        return $content;
    }

    /**
     * Optimize inline styles for better performance
     */
    private function optimizeInlineStyles(string $content): string
    {
        // Remove unnecessary whitespace from inline styles
        $content = preg_replace_callback(
            '/style=["\']([^"\']+)["\']/i',
            function ($matches) {
                $style = $matches[1];
                // Remove extra whitespace and semicolons
                $style = preg_replace('/\s+/', ' ', $style);
                $style = preg_replace('/;\s*/', ';', $style);
                $style = mb_trim($style, '; ');

                return 'style="'.$style.'"';
            },
            $content
        );

        return $content;
    }

    /**
     * Add preload hints for critical resources
     */
    private function addPreloadHints(string $content): string
    {
        // Add preload hints for critical CSS
        if (str_contains($content, '<head>')) {
            $preloadHints = '
    <!-- Preload critical resources -->
    <link rel="preload" href="/assets/css/critical.css" as="style">
    <link rel="preload" href="/assets/js/critical.js" as="script">
    <link rel="preload" href="/assets/fonts/main.woff2" as="font" type="font/woff2" crossorigin>
';

            $content = str_replace('<head>', '<head>'.$preloadHints, $content);
        }

        return $content;
    }
}
