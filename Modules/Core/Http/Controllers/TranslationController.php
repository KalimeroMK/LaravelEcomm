<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\TranslationService;

class TranslationController extends Controller
{
    public function __construct(
        private TranslationService $translationService
    ) {}
    
    /**
     * Get translations for a model
     */
    public function getModelTranslations(Request $request): JsonResponse
    {
        $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);
        
        $modelClass = $request->input('model_type');
        $modelId = $request->input('model_id');
        
        $model = $modelClass::findOrFail($modelId);
        $translations = $this->translationService->getModelTranslations($model);
        
        return response()->json([
            'success' => true,
            'data' => $translations,
        ]);
    }
    
    /**
     * Set translations for a model
     */
    public function setModelTranslations(Request $request): JsonResponse
    {
        $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'translations' => 'required|array',
        ]);
        
        $modelClass = $request->input('model_type');
        $modelId = $request->input('model_id');
        $translations = $request->input('translations');
        
        $model = $modelClass::findOrFail($modelId);
        $this->translationService->setModelTranslations($model, $translations);
        
        return response()->json([
            'success' => true,
            'message' => 'Translations saved successfully',
        ]);
    }
    
    /**
     * Get missing translations
     */
    public function getMissingTranslations(Request $request): JsonResponse
    {
        $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'locales' => 'required|array',
        ]);
        
        $modelClass = $request->input('model_type');
        $modelId = $request->input('model_id');
        $locales = $request->input('locales');
        
        $model = $modelClass::findOrFail($modelId);
        $missing = $this->translationService->getMissingTranslations($model, $locales);
        
        return response()->json([
            'success' => true,
            'data' => $missing,
        ]);
    }
    
    /**
     * Auto-translate text
     */
    public function autoTranslate(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string',
            'from_locale' => 'required|string',
            'to_locale' => 'required|string',
        ]);
        
        $text = $request->input('text');
        $fromLocale = $request->input('from_locale');
        $toLocale = $request->input('to_locale');
        
        $translation = $this->translationService->autoTranslate($text, $fromLocale, $toLocale);
        
        return response()->json([
            'success' => true,
            'data' => [
                'translation' => $translation,
                'needs_manual' => $translation === null,
            ],
        ]);
    }
}
