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
        ];

        foreach ($languages as $language) {
            Language::firstOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}
