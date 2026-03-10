<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 10)->unique(); // 'en', 'mk', 'de', 'zh-CN'
            $table->string('name');               // 'English'
            $table->string('native_name');        // 'English', 'Македонски'
            $table->string('flag', 10)->nullable(); // Emoji or image path
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->string('direction', 3)->default('ltr'); // 'ltr' or 'rtl'
            $table->integer('sort_order')->default(0);
            $table->json('meta')->nullable();     // Extra config
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
        
        // Seed default languages
        $this->seedDefaultLanguages();
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
    
    private function seedDefaultLanguages(): void
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => '🇬🇧',
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'mk',
                'name' => 'Macedonian',
                'native_name' => 'Македонски',
                'flag' => '🇲🇰',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'de',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'flag' => '🇩🇪',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'es',
                'name' => 'Spanish',
                'native_name' => 'Español',
                'flag' => '🇪🇸',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'fr',
                'name' => 'French',
                'native_name' => 'Français',
                'flag' => '🇫🇷',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'it',
                'name' => 'Italian',
                'native_name' => 'Italiano',
                'flag' => '🇮🇹',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'flag' => '🇸🇦',
                'is_default' => false,
                'is_active' => true,
                'direction' => 'rtl',
                'sort_order' => 7,
            ],
        ];
        
        foreach ($languages as $lang) {
            \Modules\Language\Models\Language::create($lang);
        }
    }
};
