<?php

declare(strict_types=1);

namespace Modules\Language\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Language\Actions\CreateLanguageAction;
use Modules\Language\Actions\DeleteLanguageAction;
use Modules\Language\Actions\UpdateLanguageAction;
use Modules\Language\DTOs\LanguageDTO;
use Modules\Language\Http\Requests\LanguageRequest;
use Modules\Language\Models\Language;

class LanguageController extends CoreController
{
    public function __construct(
        private readonly CreateLanguageAction $createAction,
        private readonly UpdateLanguageAction $updateAction,
        private readonly DeleteLanguageAction $deleteAction,
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(): JsonResponse
    {
        $languages = Language::active()->ordered()->get([
            'id', 'code', 'name', 'native_name', 'flag', 'direction', 'is_default'
        ]);

        return response()->json([
            'success' => true,
            'data' => $languages,
        ]);
    }

    public function current(): JsonResponse
    {
        $locale = app()->getLocale();
        $language = Language::where('code', $locale)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'locale' => $locale,
                'language' => $language,
                'is_rtl' => $language?->direction === 'rtl',
            ],
        ]);
    }

    public function default(): JsonResponse
    {
        $language = Language::default()->first();

        return response()->json([
            'success' => true,
            'data' => $language,
        ]);
    }

    public function setLocale(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'locale' => 'required|string|size:2',
        ]);

        $locale = $validated['locale'];

        if (! Language::isValidCode($locale)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid locale',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => ['locale' => $locale],
        ]);
    }

    public function adminIndex(): JsonResponse
    {
        $languages = Language::ordered()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $languages,
        ]);
    }

    public function store(LanguageRequest $request): JsonResponse
    {
        $dto = LanguageDTO::fromRequest($request->validated());
        
        $language = $this->createAction->execute($dto);

        return response()->json([
            'success' => true,
            'message' => __('language::messages.created'),
            'data' => $language,
        ], 201);
    }

    public function update(LanguageRequest $request, Language $language): JsonResponse
    {
        $dto = LanguageDTO::fromRequest($request->validated());
        
        try {
            $language = $this->updateAction->execute($language, $dto);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => __('language::messages.updated'),
            'data' => $language,
        ]);
    }

    public function destroy(Language $language): JsonResponse
    {
        try {
            $this->deleteAction->execute($language);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => __('language::messages.deleted'),
        ]);
    }
}
