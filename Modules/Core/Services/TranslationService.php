<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    /**
     * Get translation for a specific key and locale
     */
    public function getTranslation(string $key, string $locale, ?string $group = 'models'): ?string
    {
        return DB::table('ltm_translations')
            ->where('key', $key)
            ->where('locale', $locale)
            ->where('group', $group)
            ->value('value');
    }
    
    /**
     * Set translation for a specific key and locale
     */
    public function setTranslation(string $key, string $locale, string $value, string $group = 'models'): void
    {
        DB::table('ltm_translations')->updateOrInsert(
            [
                'key' => $key,
                'locale' => $locale,
                'group' => $group,
            ],
            [
                'value' => $value,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
    
    /**
     * Get all translations for a model
     */
    public function getModelTranslations(Model $model): array
    {
        $translations = [];
        
        $modelClass = class_basename($model);
        $modelName = strtolower($modelClass);
        $keyPattern = "{$modelName}.{$model->getKey()}.";
        
        $keys = DB::table('ltm_translations')
            ->where('group', 'models')
            ->where('key', 'like', $keyPattern . '%')
            ->get();
            
        foreach ($keys as $translation) {
            $attribute = str_replace($keyPattern, '', $translation->key);
            $locale = $translation->locale;
            
            $translations[$attribute][$locale] = $translation->value;
        }
        
        return $translations;
    }
    
    /**
     * Set translations for a model
     */
    public function setModelTranslations(Model $model, array $translations): void
    {
        $modelClass = class_basename($model);
        $modelName = strtolower($modelClass);
        $keyPrefix = "{$modelName}.{$model->getKey()}.";
        
        foreach ($translations as $attribute => $localeTranslations) {
            foreach ($localeTranslations as $locale => $value) {
                $key = $keyPrefix . $attribute;
                $this->setTranslation($key, $locale, $value, 'models');
            }
        }
    }
    
    /**
     * Delete all translations for a model
     */
    public function deleteModelTranslations(Model $model): void
    {
        $modelClass = class_basename($model);
        $modelName = strtolower($modelClass);
        $keyPattern = "{$modelName}.{$model->getKey()}.";
        
        DB::table('ltm_translations')
            ->where('group', 'models')
            ->where('key', 'like', $keyPattern . '%')
            ->delete();
    }
    
    /**
     * Get missing translations for a model
     */
    public function getMissingTranslations(Model $model, array $locales): array
    {
        $missing = [];
        $translations = $this->getModelTranslations($model);
        
        foreach ($translations as $attribute => $localeTranslations) {
            foreach ($locales as $locale) {
                if (!isset($localeTranslations[$locale])) {
                    $missing[$attribute][] = $locale;
                }
            }
        }
        
        return $missing;
    }
    
    /**
     * Auto-translate using AI (if available)
     */
    public function autoTranslate(string $text, string $fromLocale, string $toLocale): ?string
    {
        // This would integrate with OpenAI or other translation services
        // For now, return null to indicate manual translation needed
        return null;
    }
}
