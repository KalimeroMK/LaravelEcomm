<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Newsletter\Http\Controllers\Api\NewsletterAnalyticsController;
use Modules\Newsletter\Http\Controllers\Api\NewsletterCampaignController;
use Modules\Newsletter\Http\Controllers\Api\NewsletterController;
use Modules\Newsletter\Http\Controllers\Api\EmailTemplateController;
use Modules\Newsletter\Http\Controllers\Api\EmailCampaignController;

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

// API Routes
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

// Email Template API Routes
Route::prefix('email-templates')->group(function () {
    Route::get('/', [EmailTemplateController::class, 'index'])->name('api.email-templates.index');
    Route::post('/', [EmailTemplateController::class, 'store'])->name('api.email-templates.store');
    Route::get('/{emailTemplate}', [EmailTemplateController::class, 'show'])->name('api.email-templates.show');
    Route::put('/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('api.email-templates.update');
    Route::delete('/{emailTemplate}', [EmailTemplateController::class, 'destroy'])->name('api.email-templates.destroy');
    Route::get('/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('api.email-templates.preview');
    Route::post('/{emailTemplate}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('api.email-templates.duplicate');
    Route::post('/{emailTemplate}/set-default', [EmailTemplateController::class, 'setDefault'])->name('api.email-templates.set-default');
    Route::post('/{emailTemplate}/toggle-active', [EmailTemplateController::class, 'toggleActive'])->name('api.email-templates.toggle-active');
    Route::get('/types/list', [EmailTemplateController::class, 'getTemplateTypes'])->name('api.email-templates.types');
    Route::get('/type/{type}', [EmailTemplateController::class, 'getByType'])->name('api.email-templates.by-type');
});

// Email Campaign API Routes
Route::prefix('email-campaigns')->group(function () {
    Route::get('/', [EmailCampaignController::class, 'index'])->name('api.email-campaigns.index');
    Route::get('/create', [EmailCampaignController::class, 'create'])->name('api.email-campaigns.create');
    Route::post('/', [EmailCampaignController::class, 'store'])->name('api.email-campaigns.store');
    Route::post('/preview', [EmailCampaignController::class, 'preview'])->name('api.email-campaigns.preview');
    Route::get('/analytics', [EmailCampaignController::class, 'analytics'])->name('api.email-campaigns.analytics');
});
