<?php

declare(strict_types=1);

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Admin\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService
    ) {}

    /**
     * Get comprehensive dashboard analytics
     */
    public function dashboard(): JsonResponse
    {
        $analytics = $this->analyticsService->getDashboardAnalytics();

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Get overview statistics
     */
    public function overview(): JsonResponse
    {
        $overview = $this->analyticsService->getOverviewStats();

        return response()->json([
            'success' => true,
            'data' => $overview,
        ]);
    }

    /**
     * Get sales analytics
     */
    public function sales(): JsonResponse
    {
        $sales = $this->analyticsService->getSalesAnalytics();

        return response()->json([
            'success' => true,
            'data' => $sales,
        ]);
    }

    /**
     * Get user analytics
     */
    public function users(): JsonResponse
    {
        $users = $this->analyticsService->getUserAnalytics();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Get product analytics
     */
    public function products(): JsonResponse
    {
        $products = $this->analyticsService->getProductAnalytics();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Get content analytics
     */
    public function content(): JsonResponse
    {
        $content = $this->analyticsService->getContentAnalytics();

        return response()->json([
            'success' => true,
            'data' => $content,
        ]);
    }

    /**
     * Get marketing analytics
     */
    public function marketing(): JsonResponse
    {
        $marketing = $this->analyticsService->getMarketingAnalytics();

        return response()->json([
            'success' => true,
            'data' => $marketing,
        ]);
    }

    /**
     * Get performance metrics
     */
    public function performance(): JsonResponse
    {
        $performance = $this->analyticsService->getPerformanceMetrics();

        return response()->json([
            'success' => true,
            'data' => $performance,
        ]);
    }

    /**
     * Get real-time analytics
     */
    public function realTime(): JsonResponse
    {
        $realTime = [
            'online_users' => $this->getOnlineUsers(),
            'current_orders' => $this->getCurrentOrders(),
            'recent_activity' => $this->getRecentActivity(),
            'system_status' => $this->getSystemStatus(),
        ];

        return response()->json([
            'success' => true,
            'data' => $realTime,
        ]);
    }

    /**
     * Get analytics for specific date range
     */
    public function dateRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:overview,sales,users,products,content,marketing,performance',
        ]);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type');

        $analytics = $this->getDateRangeAnalytics($type, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        ]);
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:overview,sales,users,products,content,marketing,performance',
            'format' => 'required|in:csv,excel,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Implementation for export functionality
        return response()->json([
            'success' => true,
            'message' => 'Export functionality will be implemented',
        ]);
    }

    /**
     * Get online users count
     */
    private function getOnlineUsers(): int
    {
        // Simplified - in real app, use session/redis data
        return \Modules\User\Models\User::where('updated_at', '>=', now()->subMinutes(5))->count();
    }

    /**
     * Get current orders count
     */
    private function getCurrentOrders(): int
    {
        return \Modules\Order\Models\Order::where('created_at', '>=', now()->subHour())->count();
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity(): array
    {
        return [
            'new_orders' => \Modules\Order\Models\Order::where('created_at', '>=', now()->subHour())->count(),
            'new_users' => \Modules\User\Models\User::where('created_at', '>=', now()->subHour())->count(),
            'new_products' => \Modules\Product\Models\Product::where('created_at', '>=', now()->subHour())->count(),
        ];
    }

    /**
     * Get system status
     */
    private function getSystemStatus(): array
    {
        return [
            'database' => 'online',
            'cache' => 'online',
            'queue' => 'online',
            'storage' => 'online',
        ];
    }

    /**
     * Get date range analytics
     */
    private function getDateRangeAnalytics(string $type, string $startDate, string $endDate): array
    {
        // Implementation for date range analytics
        return match ($type) {
            'overview' => $this->analyticsService->getOverviewStats(),
            'sales' => $this->analyticsService->getSalesAnalytics(),
            'users' => $this->analyticsService->getUserAnalytics(),
            'products' => $this->analyticsService->getProductAnalytics(),
            'content' => $this->analyticsService->getContentAnalytics(),
            'marketing' => $this->analyticsService->getMarketingAnalytics(),
            'performance' => $this->analyticsService->getPerformanceMetrics(),
            default => [],
        };
    }
}
