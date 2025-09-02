<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Newsletter\Http\Controllers\Api\NewsletterAnalyticsController;
use Modules\Newsletter\Http\Controllers\Api\NewsletterCampaignController;
use Modules\Newsletter\Http\Controllers\Api\NewsletterController;

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

Route::apiResource('newsletters', NewsletterController::class)->names('api.newsletters')->only(['index', 'store', 'destroy']);

// Newsletter Analytics Routes
Route::prefix('newsletter')->group(function () {
    Route::get('analytics', [NewsletterAnalyticsController::class, 'index'])->name('api.newsletter.analytics');
    Route::get('analytics/campaign/{campaignId}', [NewsletterAnalyticsController::class, 'campaign'])->name('api.newsletter.analytics.campaign');
    Route::get('analytics/subscribers', [NewsletterAnalyticsController::class, 'subscribers'])->name('api.newsletter.analytics.subscribers');
    Route::get('analytics/segments', [NewsletterAnalyticsController::class, 'segments'])->name('api.newsletter.analytics.segments');
});

// Newsletter Campaign Routes
Route::prefix('newsletter')->group(function () {
    Route::post('campaigns/send-all', [NewsletterCampaignController::class, 'sendToAll'])->name('api.newsletter.campaigns.send-all');
    Route::post('campaigns/send-segment', [NewsletterCampaignController::class, 'sendToSegment'])->name('api.newsletter.campaigns.send-segment');
    Route::get('campaigns/segments', [NewsletterCampaignController::class, 'segments'])->name('api.newsletter.campaigns.segments');
});
