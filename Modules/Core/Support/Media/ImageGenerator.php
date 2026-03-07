<?php

declare(strict_types=1);

namespace Modules\Core\Support\Media;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageGenerator
{
    /**
     * Generate a placeholder image locally without internet connection.
     */
    public static function generatePlaceholder(
        int $width = 800,
        int $height = 600,
        string $text = 'Product',
        ?string $backgroundColor = null,
        string $textColor = '#ffffff'
    ): string {
        // Create image manager
        $manager = new ImageManager(new Driver());

        // Generate random background color if not provided
        if (! $backgroundColor) {
            $backgroundColor = sprintf(
                '#%02x%02x%02x',
                rand(100, 200),
                rand(100, 200),
                rand(100, 200)
            );
        }

        // Create image
        $image = $manager->create($width, $height)->fill($backgroundColor);

        // Add text – use first available font
        $fontPath = public_path('frontend/themes/modern/fonts/OpenSans-Regular.ttf');
        if (! is_file($fontPath)) {
            $fontPath = public_path('frontend/themes/modern/fonts/font-awesome/fonts/fontawesome-webfont.ttf');
        }
        $image->text($text, $width / 2, $height / 2, function ($font) use ($textColor, $width, $fontPath) {
            if (is_file($fontPath)) {
                $font->filename($fontPath);
            }
            $font->size(min((int) ($width / 10), 48));
            $font->color($textColor);
            $font->align('center');
            $font->valign('middle');
        });

        // Save to temporary file
        $tempPath = tempnam(sys_get_temp_dir(), 'img_').'.jpg';
        $image->toJpeg(85)->save($tempPath);

        return $tempPath;
    }

    /**
     * Generate a product placeholder image.
     */
    public static function generateProductImage(string $productName, int $index = 0): string
    {
        $colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
            '#6C5CE7', '#A29BFE', '#FD79A8', '#FDCB6E', '#00B894',
        ];

        $color = $colors[$index % count($colors)];
        $text = mb_substr($productName, 0, 20);

        return self::generatePlaceholder(800, 800, $text, $color);
    }

    /**
     * Generate a blog post featured image.
     */
    public static function generateBlogImage(string $postTitle): string
    {
        $colors = ['#2C3E50', '#34495E', '#7F8C8D', '#95A5A6'];
        $color = $colors[array_rand($colors)];
        $text = 'Blog: '.mb_substr($postTitle, 0, 15);

        return self::generatePlaceholder(1200, 800, $text, $color);
    }

    /**
     * Generate a category image.
     */
    public static function generateCategoryImage(string $categoryName, int $index = 0): string
    {
        $colors = [
            '#E74C3C', '#3498DB', '#2ECC71', '#F39C12', '#9B59B6',
            '#1ABC9C', '#E67E22', '#34495E', '#16A085', '#C0392B',
        ];

        $color = $colors[$index % count($colors)];
        $text = mb_substr($categoryName, 0, 25);

        return self::generatePlaceholder(600, 400, $text, $color);
    }

    /**
     * Generate a user avatar image.
     */
    public static function generateAvatarImage(string $userName): string
    {
        // Get initials
        $nameParts = explode(' ', $userName);
        $initials = '';
        foreach (array_slice($nameParts, 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        $colors = [
            '#3498DB', '#E74C3C', '#2ECC71', '#F39C12', '#9B59B6',
            '#1ABC9C', '#D35400', '#C0392B', '#16A085', '#27AE60',
        ];

        $color = $colors[ord($initials[0] ?? 'A') % count($colors)];

        return self::generatePlaceholder(400, 400, $initials, $color);
    }

    /**
     * Generate a banner image.
     */
    public static function generateBannerImage(string $bannerTitle): string
    {
        $text = mb_substr($bannerTitle, 0, 25);

        return self::generatePlaceholder(1920, 600, $text, '#2C3E50');
    }

    /**
     * Generate a bundle image.
     */
    public static function generateBundleImage(string $bundleName, int $index = 0): string
    {
        $colors = ['#8E44AD', '#2980B9', '#16A085', '#F39C12', '#C0392B'];
        $color = $colors[$index % count($colors)];
        $text = 'Bundle: '.mb_substr($bundleName, 0, 15);

        return self::generatePlaceholder(800, 600, $text, $color);
    }
}
