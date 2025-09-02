<?php

declare(strict_types=1);

namespace Modules\Admin\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Admin\Services\UserBehaviorService;

class UserBehaviorController extends Controller
{
    public function __construct(
        private readonly UserBehaviorService $userBehaviorService
    ) {}

    /**
     * Track user behavior event
     */
    public function track(Request $request): JsonResponse
    {
        $request->validate([
            'event_type' => 'required|string|max:50',
            'page_url' => 'required|string|max:500',
            'page_title' => 'nullable|string|max:200',
            'event_data' => 'nullable|array',
            'duration' => 'nullable|integer|min:0',
            'event_timestamp' => 'nullable|date',
        ]);

        try {
            $eventData = [
                'user_id' => $request->get('user_id'),
                'session_id' => $request->get('session_id'),
                'event_type' => $request->get('event_type'),
                'page_url' => $request->get('page_url'),
                'page_title' => $request->get('page_title'),
                'referrer' => $request->get('referrer'),
                'user_agent' => $request->get('user_agent'),
                'ip_address' => $request->ip(),
                'event_data' => $request->get('event_data'),
                'duration' => $request->get('duration'),
                'event_timestamp' => $request->get('event_timestamp'),
            ];

            $tracking = $this->userBehaviorService->trackEvent($eventData);

            return response()->json([
                'success' => true,
                'message' => 'Event tracked successfully',
                'tracking_id' => $tracking->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user behavior analytics
     */
    public function analytics(): JsonResponse
    {
        try {
            $analytics = $this->userBehaviorService->getUserBehaviorAnalytics();

            return response()->json([
                'success' => true,
                'data' => $analytics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get page view analytics
     */
    public function pageViews(): JsonResponse
    {
        try {
            $pageViews = $this->userBehaviorService->getPageViewAnalytics();

            return response()->json([
                'success' => true,
                'data' => $pageViews,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get page view analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user engagement analytics
     */
    public function engagement(): JsonResponse
    {
        try {
            $engagement = $this->userBehaviorService->getUserEngagementAnalytics();

            return response()->json([
                'success' => true,
                'data' => $engagement,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get engagement analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular pages
     */
    public function popularPages(): JsonResponse
    {
        try {
            $popularPages = $this->userBehaviorService->getPopularPages();

            return response()->json([
                'success' => true,
                'data' => $popularPages,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get popular pages',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get session analytics
     */
    public function sessions(): JsonResponse
    {
        try {
            $sessions = $this->userBehaviorService->getSessionAnalytics();

            return response()->json([
                'success' => true,
                'data' => $sessions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get session analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get device analytics
     */
    public function devices(): JsonResponse
    {
        try {
            $devices = $this->userBehaviorService->getDeviceAnalytics();

            return response()->json([
                'success' => true,
                'data' => $devices,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get device analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get geographic analytics
     */
    public function geographic(): JsonResponse
    {
        try {
            $geographic = $this->userBehaviorService->getGeographicAnalytics();

            return response()->json([
                'success' => true,
                'data' => $geographic,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get geographic analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
