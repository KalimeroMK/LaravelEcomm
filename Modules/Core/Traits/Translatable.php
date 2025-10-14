<?php

declare(strict_types=1);

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

trait Translatable
{
    /**
     * Get translated attribute value
     */
    public function getTranslatedAttribute(string $attribute, ?string $locale = null): ?string
    {
        $locale = $locale ?? App::getLocale();
        
        // Check if translation exists in database
        $translation = $this->getTranslation($attribute, $locale);
        
        if ($translation) {
            return $translation;
        }
        
        // Fallback to original attribute
        return $this->getAttribute($attribute);
    }
    
    /**
     * Get translation from database
     */
    protected function getTranslation(string $attribute, string $locale): ?string
    {
        $key = $this->getTranslationKey($attribute);
        
        return \DB::table('ltm_translations')
            ->where('key', $key)
            ->where('locale', $locale)
            ->where('group', 'models')
            ->value('value');
    }
    
    /**
     * Generate translation key for attribute
     */
    protected function getTranslationKey(string $attribute): string
    {
        $modelClass = class_basename($this);
        $modelName = strtolower($modelClass);
        
        return "{$modelName}.{$this->getKey()}.{$attribute}";
    }
    
    /**
     * Set translation for attribute
     */
    public function setTranslation(string $attribute, string $locale, string $value): void
    {
        $key = $this->getTranslationKey($attribute);
        
        \DB::table('ltm_translations')->updateOrInsert(
            [
                'key' => $key,
                'locale' => $locale,
                'group' => 'models',
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
     * Get all translations for this model
     */
    public function getTranslations(): array
    {
        $translations = [];
        
        $keys = \DB::table('ltm_translations')
            ->where('group', 'models')
            ->where('key', 'like', $this->getTranslationKey('%'))
            ->get();
            
        foreach ($keys as $translation) {
            $parts = explode('.', $translation->key);
            $attribute = end($parts);
            $locale = $translation->locale;
            
            $translations[$attribute][$locale] = $translation->value;
        }
        
        return $translations;
    }
    
    /**
     * Delete all translations for this model
     */
    public function deleteTranslations(): void
    {
        \DB::table('ltm_translations')
            ->where('group', 'models')
            ->where('key', 'like', $this->getTranslationKey('%'))
            ->delete();
    }
}
