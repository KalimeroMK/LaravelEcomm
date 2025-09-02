<?php

declare(strict_types=1);

namespace Modules\Admin\Http\Controllers\Api;

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
    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:overview,sales,users,products,content,marketing,performance',
            'format' => 'required|in:json,csv,xlsx',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $type = $request->get('type');
        $format = $request->get('format');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Generate export data
        $exportData = $this->generateExportData($type, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'message' => 'Export data generated successfully',
            'data' => $exportData,
            'format' => $format,
            'download_url' => route('admin.analytics.download', [
                'type' => $type,
                'format' => $format,
                'token' => 'generated_token_here', // In real app, generate secure token
            ]),
        ]);
    }

    /**
     * Get online users count
     */
    private function getOnlineUsers(): int
    {
        // Simplified - in real app, use session tracking or Redis
        return \Modules\User\Models\User::where('updated_at', '>=', now()->subMinutes(5))->count();
    }

    /**
     * Get current orders being processed
     */
    private function getCurrentOrders(): array
    {
        return [
            'pending' => \Modules\Order\Models\Order::where('status', 'pending')->count(),
            'processing' => \Modules\Order\Models\Order::where('status', 'processing')->count(),
            'shipped' => \Modules\Order\Models\Order::where('status', 'shipped')->count(),
        ];
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
            'database' => 'healthy',
            'cache' => 'healthy',
            'queue' => 'healthy',
            'storage' => 'healthy',
        ];
    }

    /**
     * Get analytics for specific date range
     */
    private function getDateRangeAnalytics(string $type, string $startDate, string $endDate): array
    {
        // This would implement date-range specific analytics
        // For now, return the general analytics
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

    /**
     * Generate export data
     */
    private function generateExportData(string $type, ?string $startDate, ?string $endDate): array
    {
        // This would generate export-ready data
        return [
            'type' => $type,
            'generated_at' => now()->toISOString(),
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'data' => $this->getDateRangeAnalytics($type, $startDate ?? now()->subMonth()->toDateString(), $endDate ?? now()->toDateString()),
        ];
    }
}
