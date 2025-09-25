<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Newsletter\Models\EmailAnalytics;

class EmailTrackingController extends Controller
{
    /**
     * Track email opens
     */
    public function trackOpen(Request $request): Response
    {
        $analyticsId = $request->get('id');

        if ($analyticsId) {
            $analytics = EmailAnalytics::find($analyticsId);
            if ($analytics) {
                $analytics->markAsOpened();
            }
        }

        // Return a 1x1 transparent pixel
        $pixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        return response($pixel, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Track email clicks
     */
    public function trackClick(Request $request): \Illuminate\Http\RedirectResponse
    {
        $analyticsId = $request->get('id');
        $url = $request->get('url');

        if ($analyticsId && $url) {
            $analytics = EmailAnalytics::find($analyticsId);
            if ($analytics) {
                $analytics->markAsClicked($url);
            }
        }

        return redirect($url ?? '/');
    }

    /**
     * Handle unsubscribe requests
     */
    public function unsubscribe(Request $request): \Illuminate\View\View
    {
        $email = $request->get('email');
        $analyticsId = $request->get('id');

        if ($email) {
            // Mark all analytics for this email as unsubscribed
            EmailAnalytics::where('recipient_email', $email)
                ->where('unsubscribed', false)
                ->update([
                    'unsubscribed' => true,
                    'unsubscribed_at' => now(),
                ]);

            // Also mark newsletter subscription as invalid
            \Modules\Newsletter\Models\Newsletter::where('email', $email)
                ->update(['is_validated' => false]);
        }

        if ($analyticsId) {
            $analytics = EmailAnalytics::find($analyticsId);
            if ($analytics) {
                $analytics->markAsUnsubscribed();
            }
        }

        return view('newsletter::unsubscribe-success', ['email' => $email]);
    }
}
