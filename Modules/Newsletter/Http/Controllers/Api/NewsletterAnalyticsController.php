<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Controllers\Controller;
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

    /**
     * Export newsletter analytics
     */
    public function export(Request $request): Response|JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'campaign_type' => 'nullable|string',
        ]);

        $format = $request->get('format');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $request->get('campaign_type', 'all');

        $exportData = $this->generateExportData();

        if ($format === 'xlsx') {
            return $this->exportToExcel($exportData, $startDate, $endDate);
        }
        if ($format === 'csv') {
            return $this->exportToCsv($exportData, $startDate, $endDate);
        }

        return response()->json([
            'success' => true,
            'message' => 'Export data generated successfully',
            'data' => $exportData,
            'format' => $format,
        ]);

    }

    /**
     * Generate export data
     */
    private function generateExportData(): array
    {
        $analytics = $this->newsletterService->getNewsletterAnalytics('30_days');
        $subscriberStats = $this->newsletterService->getSubscriberStats();
        $segments = $this->getSegmentData();

        return [
            'overview' => [
                'Total Emails Sent' => $analytics['total_sent'],
                'Total Opened' => $analytics['total_opened'],
                'Total Clicked' => $analytics['total_clicked'],
                'Open Rate (%)' => $analytics['open_rate'],
                'Click Rate (%)' => $analytics['click_rate'],
                'Bounce Rate (%)' => $analytics['bounce_rate'],
                'Unsubscribe Rate (%)' => $analytics['unsubscribe_rate'],
            ],
            'subscribers' => [
                'Total Subscribers' => $subscriberStats['total_subscribers'],
                'Validated Subscribers' => $subscriberStats['validated_subscribers'],
                'New This Month' => $subscriberStats['new_this_month'],
                'Unsubscribed This Month' => $subscriberStats['unsubscribed_this_month'],
                'Validation Rate (%)' => $subscriberStats['validation_rate'],
            ],
            'segments' => $segments,
            'performance' => [
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                'emails_sent' => [100, 150, 200, 180],
                'opens' => [25, 40, 60, 55],
                'clicks' => [5, 8, 12, 10],
            ],
            'campaign_types' => [
                'labels' => ['Newsletter', 'Promotional', 'Transactional', 'Abandoned Cart'],
                'data' => [45, 30, 15, 10],
            ],
            'campaigns' => [
                [
                    'name' => 'Weekly Newsletter',
                    'type' => 'Newsletter',
                    'sent' => 1000,
                    'opened' => 250,
                    'clicked' => 50,
                    'open_rate' => 25.0,
                    'click_rate' => 5.0,
                    'date' => '2025-01-01',
                ],
                [
                    'name' => 'Product Promotion',
                    'type' => 'Promotional',
                    'sent' => 500,
                    'opened' => 150,
                    'clicked' => 30,
                    'open_rate' => 30.0,
                    'click_rate' => 6.0,
                    'date' => '2025-01-02',
                ],
            ],
        ];
    }

    /**
     * Get segment data
     */
    private function getSegmentData(): array
    {
        return [
            'Active Users' => $this->newsletterService->getSegmentSubscribers('active_users')->count(),
            'New Subscribers' => $this->newsletterService->getSegmentSubscribers('new_subscribers')->count(),
            'Premium Users' => $this->newsletterService->getSegmentSubscribers('premium_users')->count(),
            'Inactive Users' => $this->newsletterService->getSegmentSubscribers('inactive_users')->count(),
            'Product Interested' => $this->newsletterService->getSegmentSubscribers('product_interested')->count(),
            'Blog Readers' => $this->newsletterService->getSegmentSubscribers('blog_readers')->count(),
        ];
    }

    /**
     * Export to Excel
     */
    private function exportToExcel(array $data, ?string $startDate, ?string $endDate): Response
    {
        $exportData = $this->convertToExcelData($data);
        $title = 'Email Analytics';
        if ($startDate && $endDate) {
            $title .= " ({$startDate} to {$endDate})";
        }

        return Excel::download(
            new EmailAnalyticsExport($exportData, $title),
            'email-analytics-'.($startDate ?? 'all').'-to-'.($endDate ?? 'all').'.xlsx'
        );
    }

    /**
     * Export to CSV
     */
    private function exportToCsv(array $data, ?string $startDate, ?string $endDate): Response
    {
        $exportData = $this->convertToExcelData($data);
        $title = 'Email Analytics';
        if ($startDate && $endDate) {
            $title .= " ({$startDate} to {$endDate})";
        }

        return Excel::download(
            new EmailAnalyticsExport($exportData, $title),
            'email-analytics-'.($startDate ?? 'all').'-to-'.($endDate ?? 'all').'.csv'
        );
    }

    /**
     * Convert data to Excel format
     */
    private function convertToExcelData(array $data): array
    {
        $excelData = [];

        // Overview data
        foreach ($data['overview'] as $key => $value) {
            $excelData[] = [$key, $value];
        }

        // Add separator
        $excelData[] = ['', ''];

        // Subscriber data
        foreach ($data['subscribers'] as $key => $value) {
            $excelData[] = [$key, $value];
        }

        // Add separator
        $excelData[] = ['', ''];

        // Segment data
        foreach ($data['segments'] as $key => $value) {
            $excelData[] = [$key, $value];
        }

        return $excelData;
    }
}

/**
 * Email Analytics Export Class
 */
class EmailAnalyticsExport implements FromArray, WithHeadings, WithTitle
{
    protected array $data;

    protected string $title;

    public function __construct(array $data, string $title = 'Email Analytics')
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return $this->title;
    }
}
