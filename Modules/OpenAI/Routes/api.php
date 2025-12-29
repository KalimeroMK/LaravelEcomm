<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\OpenAI\Http\Controllers\Api\OpenAIController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::middleware(['auth:sanctum'])->prefix('openai')->name('api.openai.')->group(function (): void {
    Route::post('generate-text', [OpenAIController::class, 'generateText'])->name('generate-text');
    Route::post('chat-completion', [OpenAIController::class, 'chatCompletion'])->name('chat-completion');
    Route::post('text-completion', [OpenAIController::class, 'textCompletion'])->name('text-completion');
});
