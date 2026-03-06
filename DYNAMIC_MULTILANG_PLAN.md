# Dynamic Multi-Language System Architecture

## Overview
System where admin can add unlimited languages from database, and all content (products, categories, pages) becomes translatable.

## Core Philosophy
**Languages are data, not code.** Admin adds languages via UI, system adapts automatically.

---

## Database Schema

### 1. languages Table (Master language list)
```php
Schema::create('languages', function (Blueprint $table) {
    $table->id();
    $table->string('code', 10)->unique(); // 'en', 'mk', 'de', 'zh-CN'
    $table->string('name');               // 'English', 'Македонски', 'Deutsch'
    $table->string('native_name');        // 'English', 'Македонски', 'Deutsch'
    $table->string('flag', 10)->nullable(); // '🇬🇧', '🇲🇰', '🇩🇪' or image path
    $table->boolean('is_active')->default(true);
    $table->boolean('is_default')->default(false); // One default language
    $table->string('direction', 3)->default('ltr'); // 'ltr' or 'rtl'
    $table->integer('sort_order')->default(0);
    $table->json('meta')->nullable();     // Extra config (date_format, number_format)
    $table->timestamps();
    
    $table->index(['is_active', 'sort_order']);
});
```

**Seed default languages:**
```php
Language::create(['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'is_default' => true]);
Language::create(['code' => 'mk', 'name' => 'Macedonian', 'native_name' => 'Македонски']);
Language::create(['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch']);
// Admin can add more from UI!
```

### 2. translations Table (Universal translation storage)
```php
Schema::create('translations', function (Blueprint $table) {
    $table->id();
    $table->string('translatable_type');  // 'Product', 'Category', 'Page'
    $table->unsignedBigInteger('translatable_id');
    $table->string('locale', 10);         // 'en', 'mk'
    $table->string('field');              // 'name', 'description', 'slug'
    $table->longText('value');            // The actual translated text
    $table->timestamps();
    
    // Composite unique: One translation per entity/field/locale
    $table->unique(['translatable_type', 'translatable_id', 'locale', 'field'], 'unique_translation');
    $table->index(['translatable_type', 'translatable_id']);
    $table->index(['locale', 'field']);
});
```

**Alternative: Separate tables per model (better performance)**
```php
// product_translations table
- product_id, locale, name, description, slug, meta_title, meta_description

// category_translations table  
- category_id, locale, name, description, slug

// page_translations table
- page_id, locale, title, content, slug, meta_title, meta_description
```

**Decision:** Use separate tables for better performance and type safety.

---

## Architecture

### 1. HasTranslations Trait (Reusable)

```php
<?php

namespace Modules\Core\Traits;

trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::deleting(function ($model) {
            $model->translations()->delete();
        });
    }
    
    public function translations(): HasMany
    {
        $class = class_basename($this);
        return $this->hasMany("Modules\\{$this->getModuleName()}\\Models\\{$class}Translation");
    }
    
    public function translation(?string $locale = null): HasOne
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne($this->getTranslationModelClass())
            ->where('locale', $locale);
    }
    
    /**
     * Get translated attribute with fallback
     */
    public function getTranslated(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = Language::getDefault()->code;
        
        // Try requested locale
        $translation = $this->translations
            ->where('locale', $locale)
            ->first();
            
        if ($translation && $translation->$field) {
            return $translation->$field;
        }
        
        // Fallback to default locale
        if ($locale !== $defaultLocale) {
            $translation = $this->translations
                ->where('locale', $defaultLocale)
                ->first();
                
            if ($translation && $translation->$field) {
                return $translation->$field;
            }
        }
        
        // Fallback to model attribute
        return $this->getAttribute($field);
    }
    
    /**
     * Set translation
     */
    public function setTranslation(string $locale, string $field, $value): void
    {
        $this->translations()->updateOrCreate(
            ['locale' => $locale],
            [$field => $value]
        );
    }
    
    /**
     * Set multiple translations at once
     */
    public function setTranslations(string $locale, array $data): void
    {
        $translation = $this->translations()->firstOrCreate(['locale' => $locale]);
        $translation->fill($data)->save();
    }
    
    /**
     * Check if has translation for locale
     */
    public function hasTranslation(string $locale): bool
    {
        return $this->translations()->where('locale', $locale)->exists();
    }
    
    /**
     * Magic getter for translated attributes
     */
    public function getAttribute($key)
    {
        if (in_array($key, $this->translatable ?? [])) {
            return $this->getTranslated($key);
        }
        
        return parent::getAttribute($key);
    }
    
    private function getTranslationModelClass(): string
    {
        return get_class($this) . 'Translation';
    }
    
    private function getModuleName(): string
    {
        $class = get_class($this);
        preg_match('/Modules\\\([^\\]+)/', $class, $matches);
        return $matches[1] ?? 'Core';
    }
}
```

### 2. Language Model with Caching

```php
<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class Language extends Core
{
    protected $fillable = [
        'code', 'name', 'native_name', 'flag', 
        'is_active', 'is_default', 'direction', 'sort_order', 'meta'
    ];
    
    protected $casts = [
        'is_active' => 'bool',
        'is_default' => 'bool',
        'meta' => 'array',
    ];
    
    // Cache key
    private const CACHE_KEY = 'languages.all';
    private const CACHE_TTL = 86400; // 24 hours
    
    protected static function boot(): void
    {
        parent::boot();
        
        static::saved(function () {
            Cache::forget(self::CACHE_KEY);
        });
        
        static::deleted(function () {
            Cache::forget(self::CACHE_KEY);
        });
    }
    
    /**
     * Get all active languages (cached)
     */
    public static function getActive(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }
    
    /**
     * Get default language
     */
    public static function getDefault(): self
    {
        return self::where('is_default', true)->first()
            ?? self::where('is_active', true)->first();
    }
    
    /**
     * Set as default (unset others)
     */
    public function setAsDefault(): void
    {
        self::where('is_default', true)->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }
    
    /**
     * Scope: Active only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Get flag emoji or image
     */
    public function getFlagAttribute($value): string
    {
        if (empty($value)) {
            // Return emoji based on country code
            return $this->getFlagEmoji($this->code);
        }
        return $value;
    }
    
    private function getFlagEmoji(string $code): string
    {
        // Simple mapping for common codes
        $map = [
            'en' => '🇬🇧', 'mk' => '🇲🇰', 'de' => '🇩🇪',
            'fr' => '🇫🇷', 'it' => '🇮🇹', 'es' => '🇪🇸',
            'ar' => '🇸🇦', 'ru' => '🇷🇺', 'zh' => '🇨🇳',
        ];
        return $map[$code] ?? '🌐';
    }
}
```

### 3. Actions

```php
<?php

// Actions/Translation/GetTranslationAction.php
namespace Modules\Core\Actions\Translation;

readonly class GetTranslationAction
{
    public function execute(
        object $model, 
        string $field, 
        ?string $locale = null
    ): ?string {
        return $model->getTranslated($field, $locale);
    }
}

// Actions/Translation/SetTranslationAction.php
namespace Modules\Core\Actions\Translation;

readonly class SetTranslationAction
{
    public function execute(
        object $model,
        string $locale,
        string $field,
        $value
    ): void {
        $model->setTranslation($locale, $field, $value);
    }
}

// Actions/Translation/SyncTranslationsAction.php
namespace Modules\Core\Actions\Translation;

readonly class SyncTranslationsAction
{
    public function execute(
        object $model,
        array $translations // ['en' => ['name' => '...'], 'mk' => [...]]
    ): void {
        foreach ($translations as $locale => $data) {
            $model->setTranslations($locale, $data);
        }
    }
}

// Actions/Translation/GetModelInLocaleAction.php
readonly class GetModelInLocaleAction
{
    public function execute(
        object $model,
        ?string $locale = null
    ): object {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = Language::getDefault()->code;
        
        // Load translation
        $translation = $model->translations
            ->where('locale', $locale)
            ->first();
            
        // If not found, try default
        if (!$translation && $locale !== $defaultLocale) {
            $translation = $model->translations
                ->where('locale', $defaultLocale)
                ->first();
        }
        
        // Apply translations to model (without saving)
        if ($translation) {
            foreach ($model->translatable as $field) {
                if ($translation->$field) {
                    $model->setAttribute($field, $translation->$field);
                }
            }
        }
        
        return $model;
    }
}
```

### 4. Middleware for Locale Detection

```php
<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Modules\Core\Models\Language;

class SetLocaleMiddleware
{
    public function handle($request, Closure $next)
    {
        // Priority: URL param > Session > Browser > Default
        $locale = $this->detectLocale($request);
        
        // Validate locale exists and is active
        if (!$this->isValidLocale($locale)) {
            $locale = Language::getDefault()->code;
        }
        
        app()->setLocale($locale);
        session()->put('locale', $locale);
        
        // Share with views
        view()->share('currentLocale', $locale);
        view()->share('availableLocales', Language::getActive());
        
        return $next($request);
    }
    
    private function detectLocale($request): string
    {
        // 1. Check URL parameter: /mk/product or ?locale=mk
        if ($request->segment(1) && $this->isValidLocale($request->segment(1))) {
            return $request->segment(1);
        }
        
        if ($request->query('locale')) {
            return $request->query('locale');
        }
        
        // 2. Check session
        if (session()->has('locale')) {
            return session('locale');
        }
        
        // 3. Check user preference
        if (auth()->check() && auth()->user()->preferred_locale) {
            return auth()->user()->preferred_locale;
        }
        
        // 4. Check browser
        $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
        if ($this->isValidLocale($browserLocale)) {
            return $browserLocale;
        }
        
        // 5. Default
        return Language::getDefault()->code;
    }
    
    private function isValidLocale(string $locale): bool
    {
        return Language::getActive()->contains('code', $locale);
    }
}
```

### 5. Language Switcher Component

```php
<?php

namespace Modules\Core\View\Components;

use Illuminate\View\Component;
use Modules\Core\Models\Language;

class LanguageSwitcher extends Component
{
    public function render()
    {
        return view('core::components.language-switcher', [
            'languages' => Language::getActive(),
            'currentLocale' => app()->getLocale(),
        ]);
    }
}
```

```blade
{{-- resources/views/components/language-switcher.blade.php --}}
<div class="language-switcher dropdown">
    <button class="dropdown-toggle" data-toggle="dropdown">
        @php($current = $languages->firstWhere('code', $currentLocale))
        <span class="flag">{{ $current->flag }}</span>
        <span class="code">{{ strtoupper($current->code) }}</span>
    </button>
    <div class="dropdown-menu">
        @foreach($languages as $lang)
            @if($lang->code !== $currentLocale)
                <a class="dropdown-item" href="{{ route('locale.switch', $lang->code) }}">
                    <span class="flag">{{ $lang->flag }}</span>
                    {{ $lang->native_name }}
                </a>
            @endif
        @endforeach
    </div>
</div>
```

---

## Admin UI for Language Management

### LanguageController (Admin)

```php
<?php

namespace Modules\Core\Http\Controllers;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::orderBy('sort_order')->get();
        return view('core::languages.index', compact('languages'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:languages|size:2',
            'name' => 'required|string',
            'native_name' => 'required|string',
            'direction' => 'in:ltr,rtl',
        ]);
        
        $language = Language::create($request->all());
        
        // Create translation tables if using separate tables approach
        // Or they auto-create on first translation
        
        return redirect()->back()->with('success', "Language {$language->name} added!");
    }
    
    public function setDefault(Language $language)
    {
        $language->setAsDefault();
        return redirect()->back()->with('success', "{$language->name} is now default");
    }
    
    public function toggleActive(Language $language)
    {
        $language->update(['is_active' => !$language->is_active]);
        return redirect()->back();
    }
    
    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return redirect()->back()->with('error', 'Cannot delete default language');
        }
        
        // Delete all translations for this language
        DB::table('product_translations')->where('locale', $language->code)->delete();
        DB::table('category_translations')->where('locale', $language->code)->delete();
        
        $language->delete();
        return redirect()->back()->with('success', 'Language deleted');
    }
}
```

### Product Translation UI

```php
<?php

namespace Modules\Product\Http\Controllers;

class ProductTranslationController extends Controller
{
    public function __construct(
        private readonly SyncTranslationsAction $syncTranslations,
    ) {}
    
    public function edit(Product $product)
    {
        $languages = Language::getActive();
        $product->load('translations');
        
        return view('product::translations.edit', compact('product', 'languages'));
    }
    
    public function update(Request $request, Product $product)
    {
        // Request contains: translations[en][name], translations[en][description], etc.
        $request->validate([
            'translations' => 'required|array',
            'translations.*.name' => 'required|string',
        ]);
        
        $this->syncTranslations->execute($product, $request->translations);
        
        return redirect()->back()->with('success', 'Translations saved');
    }
}
```

```blade
{{-- product::translations.edit --}}
<form method="POST" action="{{ route('admin.products.translations.update', $product) }}">
    @csrf
    
    <ul class="nav nav-tabs">
        @foreach($languages as $lang)
            <li class="nav-item">
                <a class="nav-link {{ $loop->first ? 'active' : '' }}" 
                   data-toggle="tab" href="#lang-{{ $lang->code }}">
                    {{ $lang->flag }} {{ $lang->native_name }}
                </a>
            </li>
        @endforeach
    </ul>
    
    <div class="tab-content">
        @foreach($languages as $lang)
            @php($translation = $product->translations->where('locale', $lang->code)->first())
            <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="lang-{{ $lang->code }}">
                <input type="hidden" name="translations[{{ $lang->code }}][locale]" value="{{ $lang->code }}">
                
                <div class="form-group">
                    <label>Name ({{ $lang->code }})</label>
                    <input type="text" 
                           name="translations[{{ $lang->code }}][name]" 
                           value="{{ $translation->name ?? '' }}"
                           class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Description ({{ $lang->code }})</label>
                    <textarea name="translations[{{ $lang->code }}][description]" 
                              class="form-control summernote">{{ $translation->description ?? '' }}</textarea>
                </div>
                
                <div class="form-group">
                    <label>Slug ({{ $lang->code }})</label>
                    <input type="text" 
                           name="translations[{{ $lang->code }}][slug]" 
                           value="{{ $translation->slug ?? '' }}"
                           class="form-control">
                </div>
            </div>
        @endforeach
    </div>
    
    <button type="submit" class="btn btn-primary">Save Translations</button>
</form>
```

---

## API Endpoints

```php
<?php

// Routes/api.php
Route::prefix('languages')->group(function () {
    Route::get('/', [LanguageController::class, 'index']);           // List all active
    Route::get('/default', [LanguageController::class, 'default']);  // Get default
});

Route::prefix('locale')->group(function () {
    Route::post('/switch/{locale}', [LocaleController::class, 'switch']); // Switch locale
    Route::get('/current', [LocaleController::class, 'current']);        // Get current
});

// Product translations
Route::prefix('products/{product}')->group(function () {
    Route::get('/translations', [ProductTranslationController::class, 'index']);
    Route::get('/translations/{locale}', [ProductTranslationController::class, 'show']);
    Route::put('/translations/{locale}', [ProductTranslationController::class, 'update']);
    Route::post('/translations/sync', [ProductTranslationController::class, 'sync']);
    Route::get('/localized', [ProductController::class, 'showLocalized']);
});
```

```php
<?php

// Api/LanguageController.php
class LanguageController extends CoreController
{
    public function index(): JsonResponse
    {
        return $this->respond(LanguageResource::collection(Language::getActive()));
    }
    
    public function default(): JsonResponse
    {
        return $this->respond(new LanguageResource(Language::getDefault()));
    }
}

// Api/LocaleController.php
class LocaleController extends CoreController
{
    public function switch(string $locale): JsonResponse
    {
        if (!Language::where('code', $locale)->where('is_active', true)->exists()) {
            return $this->setStatusCode(400)->setMessage('Invalid locale')->respond();
        }
        
        session()->put('locale', $locale);
        app()->setLocale($locale);
        
        return $this->setMessage('Locale switched')->respond(['locale' => $locale]);
    }
    
    public function current(): JsonResponse
    {
        return $this->respond([
            'locale' => app()->getLocale(),
            'language' => new LanguageResource(
                Language::where('code', app()->getLocale())->first()
            ),
        ]);
    }
}
```

---

## Frontend URL Strategy

### Option 1: URL Prefix (Recommended)
```
/en/products/laptop-dell
/mk/products/лаптоп-дел
/de/produkte/laptop-dell
```

**Implementation:**
```php
// RouteServiceProvider
public function boot(): void
{
    $languages = Language::getActive()->pluck('code')->implode('|');
    
    Route::pattern('locale', $languages);
    
    Route::prefix('{locale?}')->middleware(['web', 'locale'])->group(function () {
        // All web routes here
        Route::get('/products/{slug}', [FrontController::class, 'productDetail']);
    });
}
```

### Option 2: Subdomain
```
en.shop.com/products/laptop
mk.shop.com/produkti/laptop
```

### Option 3: Query Parameter (Simplest)
```
/products/laptop?locale=mk
```

---

## Migration Plan

### Step 1: Database (Day 1)
- Create `languages` table
- Create translation tables (product_translations, category_translations, etc.)
- Seed default languages

### Step 2: Core Infrastructure (Day 1-2)
- Create `HasTranslations` trait
- Create Language model with caching
- Create Actions (GetTranslation, SetTranslation, SyncTranslations)
- Create `SetLocaleMiddleware`

### Step 3: Admin UI (Day 2-3)
- Language management CRUD
- Product translation UI (tabs per language)
- Category translation UI

### Step 4: API (Day 3)
- Language API endpoints
- Product translation API endpoints
- Locale switching endpoint

### Step 5: Frontend (Day 3-4)
- Language switcher component
- Update all views to use translated content
- URL localization

### Step 6: Testing (Day 4)
- Unit tests for actions
- Feature tests for API
- Integration tests for switching

**Total: 4 days**

---

## Benefits of This Architecture

1. **Dynamic**: Admin can add any language from UI
2. **Scalable**: Caching makes it fast
3. **Flexible**: Separate tables per model for type safety
4. **Fallback**: Automatic fallback to default language
5. **Clean**: Trait-based, reusable across any model
6. **API-First**: Same actions for Web and API
7. **SEO-Friendly**: Localized URLs (/en/, /mk/)
