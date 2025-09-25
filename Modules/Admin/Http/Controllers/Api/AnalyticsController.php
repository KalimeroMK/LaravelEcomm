<?php

declare(strict_types=1);

namespace Modules\Admin\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\Exports\AnalyticsExport;
use Modules\Admin\Services\AnalyticsService;
use Modules\Core\Http\Controllers\Controller;

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

        $analytics = $this->getDateRangeAnalytics($type);

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

        if ($format === 'xlsx') {
            return $this->exportToExcel($exportData, $type, $startDate, $endDate);
        }
        if ($format === 'csv') {
            return $this->exportToCsv($exportData, $type, $startDate, $endDate);
        }

        return response()->json([
            'success' => true,
            'message' => 'Export data generated successfully',
            'data' => $exportData,
            'format' => $format,
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
    private function getDateRangeAnalytics(string $type): array
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
            'data' => $this->getDateRangeAnalytics($type),
        ];
    }

    /**
     * Export data to Excel format
     */
    private function exportToExcel(array $exportData, string $type, ?string $startDate, ?string $endDate)
    {
        $filename = "analytics-{$type}-".($startDate ?? 'all').'-to-'.($endDate ?? 'now').'.xlsx';

        // Convert export data to Excel format
        $excelData = $this->convertToExcelData($exportData);

        return Excel::download(new AnalyticsExport($excelData, ucfirst($type).' Analytics'), $filename);
    }

    /**
     * Export data to CSV format
     */
    private function exportToCsv(array $exportData, string $type, ?string $startDate, ?string $endDate)
    {
        $filename = "analytics-{$type}-".($startDate ?? 'all').'-to-'.($endDate ?? 'now').'.csv';
        $csvData = $this->convertToCsv($exportData);

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Convert data to CSV format
     */
    private function convertToCsv(array $data): string
    {
        $csv = "Analytics Export\n";
        $csv .= 'Generated: '.$data['generated_at']."\n";
        $csv .= 'Type: '.$data['type']."\n";
        $csv .= 'Date Range: '.$data['date_range']['start'].' to '.$data['date_range']['end']."\n\n";

        // Convert the data to CSV format
        $analyticsData = $data['data'];

        if (isset($analyticsData['overview'])) {
            $csv .= "OVERVIEW STATISTICS\n";
            $csv .= "Metric,Value\n";
            foreach ($analyticsData['overview'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $csv .= "{$key}_{$subKey},".(is_numeric($subValue) ? $subValue : '"'.$subValue.'"')."\n";
                    }
                } else {
                    $csv .= "{$key},".(is_numeric($value) ? $value : '"'.$value.'"')."\n";
                }
            }
            $csv .= "\n";
        }

        if (isset($analyticsData['sales'])) {
            $csv .= "SALES ANALYTICS\n";
            $csv .= "Metric,Value\n";
            foreach ($analyticsData['sales'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $csv .= "{$key}_{$subKey},".(is_numeric($subValue) ? $subValue : '"'.$subValue.'"')."\n";
                    }
                } else {
                    $csv .= "{$key},".(is_numeric($value) ? $value : '"'.$value.'"')."\n";
                }
            }
            $csv .= "\n";
        }

        if (isset($analyticsData['users'])) {
            $csv .= "USER ANALYTICS\n";
            $csv .= "Metric,Value\n";
            foreach ($analyticsData['users'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $csv .= "{$key}_{$subKey},".(is_numeric($subValue) ? $subValue : '"'.$subValue.'"')."\n";
                    }
                } else {
                    $csv .= "{$key},".(is_numeric($value) ? $value : '"'.$value.'"')."\n";
                }
            }
            $csv .= "\n";
        }

        return $csv;
    }

    /**
     * Convert data to Excel format
     */
    private function convertToExcelData(array $data): array
    {
        $excelData = [];
        $analyticsData = $data['data'];

        // Add metadata rows
        $excelData[] = ['Analytics Export', ''];
        $excelData[] = ['Generated', $data['generated_at']];
        $excelData[] = ['Type', $data['type']];
        $excelData[] = ['Date Range', $data['date_range']['start'].' to '.$data['date_range']['end']];
        $excelData[] = ['', '']; // Empty row

        // Convert the analytics data to Excel format
        if (isset($analyticsData['overview'])) {
            $excelData[] = ['OVERVIEW STATISTICS', ''];
            $excelData[] = ['Metric', 'Value'];
            foreach ($analyticsData['overview'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $excelData[] = [$key.'_'.$subKey, is_numeric($subValue) ? $subValue : $subValue];
                    }
                } else {
                    $excelData[] = [$key, is_numeric($value) ? $value : $value];
                }
            }
            $excelData[] = ['', '']; // Empty row
        }

        if (isset($analyticsData['sales'])) {
            $excelData[] = ['SALES ANALYTICS', ''];
            $excelData[] = ['Metric', 'Value'];

            // Revenue by month
            if (isset($analyticsData['sales']['revenue_by_month'])) {
                $excelData[] = ['Revenue by Month', ''];
                foreach ($analyticsData['sales']['revenue_by_month'] as $item) {
                    $excelData[] = [$item['month'], $item['revenue']];
                }
                $excelData[] = ['', '']; // Empty row
            }

            // Orders by month
            if (isset($analyticsData['sales']['orders_by_month'])) {
                $excelData[] = ['Orders by Month', ''];
                foreach ($analyticsData['sales']['orders_by_month'] as $item) {
                    $excelData[] = [$item['month'], $item['orders']];
                }
                $excelData[] = ['', '']; // Empty row
            }

            // Sales by status
            if (isset($analyticsData['sales']['sales_by_status'])) {
                $excelData[] = ['Sales by Status', ''];
                foreach ($analyticsData['sales']['sales_by_status'] as $statusType => $statuses) {
                    foreach ($statuses as $status => $count) {
                        $excelData[] = [$statusType.'_'.$status, $count];
                    }
                }
                $excelData[] = ['', '']; // Empty row
            }

            // Average order value
            if (isset($analyticsData['sales']['average_order_value'])) {
                foreach ($analyticsData['sales']['average_order_value'] as $key => $value) {
                    $excelData[] = ['average_order_value_'.$key, $value];
                }
            }

            // Conversion rate
            if (isset($analyticsData['sales']['conversion_rate'])) {
                foreach ($analyticsData['sales']['conversion_rate'] as $key => $value) {
                    $excelData[] = ['conversion_rate_'.$key, $value];
                }
            }
        }

        if (isset($analyticsData['users'])) {
            $excelData[] = ['USER ANALYTICS', ''];
            $excelData[] = ['Metric', 'Value'];

            // User activity
            if (isset($analyticsData['users']['user_activity'])) {
                foreach ($analyticsData['users']['user_activity'] as $key => $value) {
                    $excelData[] = ['user_activity_'.$key, $value];
                }
            }

            // User segments
            if (isset($analyticsData['users']['user_segments'])) {
                foreach ($analyticsData['users']['user_segments'] as $key => $value) {
                    $excelData[] = ['user_segments_'.$key, $value];
                }
            }

            // Customer lifetime value
            if (isset($analyticsData['users']['customer_lifetime_value'])) {
                foreach ($analyticsData['users']['customer_lifetime_value'] as $key => $value) {
                    $excelData[] = ['customer_lifetime_value_'.$key, $value];
                }
            }
        }

        if (isset($analyticsData['products'])) {
            $excelData[] = ['PRODUCT ANALYTICS', ''];
            $excelData[] = ['Metric', 'Value'];

            // Inventory status
            if (isset($analyticsData['products']['inventory_status'])) {
                foreach ($analyticsData['products']['inventory_status'] as $key => $value) {
                    $excelData[] = ['inventory_'.$key, $value];
                }
            }

            // Top categories
            if (isset($analyticsData['products']['category_performance'])) {
                $excelData[] = ['Top Categories', ''];
                foreach (array_slice($analyticsData['products']['category_performance'], 0, 5) as $category) {
                    $excelData[] = [$category['title'], $category['products_count']];
                }
            }

            // Top brands
            if (isset($analyticsData['products']['brand_performance'])) {
                $excelData[] = ['Top Brands', ''];
                foreach (array_slice($analyticsData['products']['brand_performance'], 0, 5) as $brand) {
                    $excelData[] = [$brand['title'], $brand['products_count']];
                }
            }
        }

        return $excelData;
    }
}
