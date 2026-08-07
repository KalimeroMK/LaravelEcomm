<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Newsletter\Http\Controllers\Api\EmailCampaignController;
use Modules\Newsletter\Http\Controllers\Api\EmailTemplateController;
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

/*
 * Every route in this file is back-office functionality: subscriber records,
 * subscriber analytics, mass-send triggers and email template management.
 *
 * These groups previously carried no middleware at all, which left
 * POST newsletter/campaigns/send-all callable by anyone on the internet - an
 * open relay to the entire subscriber list - alongside unauthenticated read
 * access to subscriber data and write access to outgoing email templates.
 *
 * Public newsletter signup is NOT here; it lives on the storefront route
 * `/subscribe` in Modules/Front/Routes/web.php and is unaffected.
 */
Route::middleware(['auth:sanctum', 'role:admin|super-admin'])->group(function (): void {
    // Subscriber management
    Route::apiResource('newsletters', NewsletterController::class)->names('api.newsletters');

    // Newsletter Analytics Routes
    Route::prefix('newsletter')->group(function (): void {
        Route::get('analytics', [NewsletterAnalyticsController::class, 'index'])->name('api.newsletter.analytics');
        Route::post('analytics', [NewsletterAnalyticsController::class, 'index'])->name('api.newsletter.analytics.post');
        Route::get('analytics/campaign/{campaignId}', [NewsletterAnalyticsController::class, 'campaign'])->name('api.newsletter.analytics.campaign');
        Route::get('analytics/subscribers', [NewsletterAnalyticsController::class, 'subscribers'])->name('api.newsletter.analytics.subscribers');
        Route::get('analytics/segments', [NewsletterAnalyticsController::class, 'segments'])->name('api.newsletter.analytics.segments');
        Route::post('analytics/export', [NewsletterAnalyticsController::class, 'export'])->name('api.newsletter.analytics.export');
    });

    // Newsletter Campaign Routes
    Route::prefix('newsletter')->group(function (): void {
        Route::post('campaigns/send-all', [NewsletterCampaignController::class, 'sendToAll'])->name('api.newsletter.campaigns.send-all');
        Route::post('campaigns/send-segment', [NewsletterCampaignController::class, 'sendToSegment'])->name('api.newsletter.campaigns.send-segment');
        Route::get('campaigns/segments', [NewsletterCampaignController::class, 'segments'])->name('api.newsletter.campaigns.segments');
    });

    // Email Template API Routes
    Route::prefix('email-templates')->group(function (): void {
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
    Route::prefix('email-campaigns')->group(function (): void {
        Route::get('/', [EmailCampaignController::class, 'index'])->name('api.email-campaigns.index');
        Route::get('/create', [EmailCampaignController::class, 'create'])->name('api.email-campaigns.create');
        Route::post('/', [EmailCampaignController::class, 'store'])->name('api.email-campaigns.store');
        Route::post('/preview', [EmailCampaignController::class, 'preview'])->name('api.email-campaigns.preview');
        Route::get('/analytics', [EmailCampaignController::class, 'analytics'])->name('api.email-campaigns.analytics');
    });
});
