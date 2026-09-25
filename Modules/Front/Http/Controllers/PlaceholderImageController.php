<?php

declare(strict_types=1);

namespace Modules\Front\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Support\Media\ImageGenerator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PlaceholderImageController extends Controller
{
    /**
     * Serve a generated placeholder image (banner, product, post, bundle).
     * Used by sliders and listings when no real image is set.
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        $type = $request->query('type', 'product');
        $text = $request->query('text', '');
        $index = (int) $request->query('index', 0);
        $label = mb_substr((string) $text, 0, 50) ?: 'Image';

        // Generate each unique placeholder once and reuse the file — a GD
        // render (plus an orphaned temp file) per request does not scale.
        $cacheDir = storage_path('app/public/placeholders');
        $cachedPath = $cacheDir.'/'.md5("{$type}|{$label}|{$index}").'.jpg';

        if (! is_file($cachedPath)) {
            if (! is_dir($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }

            $tempPath = match ($type) {
                'banner' => ImageGenerator::generateBannerImage($label),
                'product' => ImageGenerator::generateProductImage($label, $index),
                'post', 'blog' => ImageGenerator::generateBlogImage($label),
                'category' => ImageGenerator::generateCategoryImage($label, $index),
                'bundle' => ImageGenerator::generateBundleImage($label, $index),
                default => ImageGenerator::generateProductImage($label, $index),
            };

            rename($tempPath, $cachedPath);
        }

        return response()->file($cachedPath, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }
}
