<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Newsletter\Services\NewsletterService;

class NewsletterAnalyticsController extends Controller
{
    public function __construct(
        private readonly NewsletterService $newsletterService
    ) {}

    /**
     * Get newsletter analytics
     */
    public function index(Request $request): JsonResponse
    {
        $period = $request->get('period', '30_days');
        $analytics = $this->newsletterService->getNewsletterAnalytics($period);

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Get campaign analytics
     */
    public function campaign(string $campaignId): JsonResponse
    {
        $analytics = $this->newsletterService->getCampaignAnalytics($campaignId);

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Get subscriber statistics
     */
    public function subscribers(): JsonResponse
    {
        $stats = $this->newsletterService->getSubscriberStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get segment statistics
     */
    public function segments(): JsonResponse
    {
        $segments = [
            'active_users' => $this->newsletterService->getSegmentSubscribers('active_users')->count(),
            'new_subscribers' => $this->newsletterService->getSegmentSubscribers('new_subscribers')->count(),
            'premium_users' => $this->newsletterService->getSegmentSubscribers('premium_users')->count(),
            'inactive_users' => $this->newsletterService->getSegmentSubscribers('inactive_users')->count(),
            'product_interested' => $this->newsletterService->getSegmentSubscribers('product_interested')->count(),
            'blog_readers' => $this->newsletterService->getSegmentSubscribers('blog_readers')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $segments,
        ]);
    }
}
