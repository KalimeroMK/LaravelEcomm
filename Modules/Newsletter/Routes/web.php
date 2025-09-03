<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Newsletter\Http\Controllers\EmailTrackingController;
use Modules\Newsletter\Http\Controllers\NewsletterController;
use Modules\Newsletter\Http\Controllers\EmailTemplateController;
use Modules\Newsletter\Http\Controllers\EmailCampaignController;

Route::resource('newsletters', NewsletterController::class)->except('show');

// Email tracking routes
Route::get('email/track/open', [EmailTrackingController::class, 'trackOpen'])->name('email.track.open');
Route::get('email/track/click', [EmailTrackingController::class, 'trackClick'])->name('email.track.click');
Route::get('email/unsubscribe', [EmailTrackingController::class, 'unsubscribe'])->name('email.unsubscribe');

// Email Template Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('email-templates', EmailTemplateController::class)->names('email-templates');
    Route::get('email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');
    Route::post('email-templates/{emailTemplate}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('email-templates.duplicate');
    Route::post('email-templates/{emailTemplate}/set-default', [EmailTemplateController::class, 'setDefault'])->name('email-templates.set-default');
    Route::post('email-templates/{emailTemplate}/toggle-active', [EmailTemplateController::class, 'toggleActive'])->name('email-templates.toggle-active');
    
    // Email Campaign Routes
    Route::get('email-campaigns', [EmailCampaignController::class, 'index'])->name('email-campaigns.index');
    Route::get('email-campaigns/create', [EmailCampaignController::class, 'create'])->name('email-campaigns.create');
    Route::post('email-campaigns', [EmailCampaignController::class, 'store'])->name('email-campaigns.store');
    Route::post('email-campaigns/preview', [EmailCampaignController::class, 'preview'])->name('email-campaigns.preview');
    Route::get('email-campaigns/analytics', [EmailCampaignController::class, 'analytics'])->name('email-campaigns.analytics');
});
