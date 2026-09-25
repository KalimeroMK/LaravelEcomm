<?php

declare(strict_types=1);

namespace Modules\Language\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Language\Models\Language;

class LanguageDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => '🇬🇧',
                'is_default' => true,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 1,
            ],
            [
                'code' => 'mk',
                'name' => 'Macedonian',
                'native_name' => 'Македонски',
                'flag' => '🇲🇰',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 2,
            ],
            [
                'code' => 'de',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'flag' => '🇩🇪',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 3,
            ],
            [
                'code' => 'sq',
                'name' => 'Albanian',
                'native_name' => 'Shqip',
                'flag' => '🇦🇱',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 4,
            ],
            [
                'code' => 'fr',
                'name' => 'French',
                'native_name' => 'Français',
                'flag' => '🇫🇷',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 5,
            ],
            [
                'code' => 'it',
                'name' => 'Italian',
                'native_name' => 'Italiano',
                'flag' => '🇮🇹',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 6,
            ],
            [
                'code' => 'es',
                'name' => 'Spanish',
                'native_name' => 'Español',
                'flag' => '🇪🇸',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 7,
            ],
            [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'flag' => '🇸🇦',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'rtl',
                'sort_order' => 8,
            ],
        ];

        foreach ($languages as $language) {
            Language::firstOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}
