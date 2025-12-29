<?php

declare(strict_types=1);

namespace Modules\Newsletter\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Newsletter\Jobs\SendNewsletterJob;
use Modules\Newsletter\Models\EmailAnalytics;
use Modules\Newsletter\Models\Newsletter;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

class NewsletterService
{
    /**
     * Send newsletter to all subscribers
     */
    public function sendNewsletterToAll(array $posts = [], array $products = []): array
    {
        $subscribers = Newsletter::where('is_validated', true)->get();
        $results = [
            'sent' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($subscribers as $subscriber) {
            try {
                $this->sendNewsletterToSubscriber($subscriber, $posts, $products);
                $results['sent']++;
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = $e->getMessage();
                Log::error('Failed to send newsletter to '.$subscriber->email.': '.$e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Send newsletter to specific subscriber
     */
    public function sendNewsletterToSubscriber(Newsletter $subscriber, array $posts = [], array $products = []): void
    {
        $campaignId = 'newsletter_'.now()->format('Y_m_d_H_i_s');

        // Track email analytics
        $analytics = EmailAnalytics::create([
            'email_type' => 'newsletter',
            'email_subject' => 'Latest News & Products',
            'recipient_email' => $subscriber->email,
            'user_id' => $subscriber->user_id ?? null,
            'campaign_id' => $campaignId,
            'sent_at' => now(),
            'metadata' => [
                'posts_count' => count($posts),
                'products_count' => count($products),
            ],
        ]);

        // Send the email
        SendNewsletterJob::dispatch($subscriber->email, $posts, $analytics->id);
    }

    /**
     * Send newsletter to segmented audience
     */
    public function sendNewsletterToSegment(string $segment, array $posts = [], array $products = []): array
    {
        $subscribers = $this->getSegmentSubscribers($segment);
        $results = [
            'sent' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($subscribers as $subscriber) {
            try {
                $this->sendNewsletterToSubscriber($subscriber, $posts, $products);
                $results['sent']++;
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = $e->getMessage();
                Log::error('Failed to send newsletter to '.$subscriber->email.': '.$e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Get subscribers by segment
     */
    public function getSegmentSubscribers(string $segment): Collection
    {
        return match ($segment) {
            'active_users' => $this->getActiveUsersSubscribers(),
            'new_subscribers' => $this->getNewSubscribers(),
            'premium_users' => $this->getPremiumUsersSubscribers(),
            'inactive_users' => $this->getInactiveUsersSubscribers(),
            'product_interested' => $this->getProductInterestedSubscribers(),
            'blog_readers' => $this->getBlogReadersSubscribers(),
            default => Newsletter::where('is_validated', true)->get(),
        };
    }

    /**
     * Get newsletter analytics
     */
    public function getNewsletterAnalytics(string $period = '30_days'): array
    {
        $dateRange = $this->getDateRange($period);

        $analytics = EmailAnalytics::ofType('newsletter')
            ->dateRange($dateRange['from'], $dateRange['to'])
            ->get();

        $totalSent = $analytics->count();
        $totalOpened = $analytics->whereNotNull('opened_at')->count();
        $totalClicked = $analytics->whereNotNull('clicked_at')->count();
        $totalBounced = $analytics->where('bounced', true)->count();
        $totalUnsubscribed = $analytics->where('unsubscribed', true)->count();

        return [
            'period' => $period,
            'date_range' => $dateRange,
            'total_sent' => $totalSent,
            'total_opened' => $totalOpened,
            'total_clicked' => $totalClicked,
            'total_bounced' => $totalBounced,
            'total_unsubscribed' => $totalUnsubscribed,
            'open_rate' => $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0,
            'click_rate' => $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0,
            'bounce_rate' => $totalSent > 0 ? round(($totalBounced / $totalSent) * 100, 2) : 0,
            'unsubscribe_rate' => $totalSent > 0 ? round(($totalUnsubscribed / $totalSent) * 100, 2) : 0,
        ];
    }

    /**
     * Get campaign analytics
     */
    public function getCampaignAnalytics(string $campaignId): array
    {
        $analytics = EmailAnalytics::ofCampaign($campaignId)->get();

        $totalSent = $analytics->count();
        $totalOpened = $analytics->opened()->count();
        $totalClicked = $analytics->clicked()->count();

        return [
            'campaign_id' => $campaignId,
            'total_sent' => $totalSent,
            'total_opened' => $totalOpened,
            'total_clicked' => $totalClicked,
            'open_rate' => $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0,
            'click_rate' => $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0,
        ];
    }

    /**
     * Get all campaigns analytics
     */
    public function getAllCampaignsAnalytics(): array
    {
        $analytics = EmailAnalytics::all();

        $totalSent = $analytics->count();
        $totalOpened = $analytics->where('opened_at', '!=', null)->count();
        $totalClicked = $analytics->where('clicked_at', '!=', null)->count();
        $totalBounced = $analytics->where('bounced', true)->count();

        return [
            'total_sent' => $totalSent,
            'total_opened' => $totalOpened,
            'total_clicked' => $totalClicked,
            'total_bounced' => $totalBounced,
            'open_rate' => $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0,
            'click_rate' => $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0,
            'bounce_rate' => $totalSent > 0 ? round(($totalBounced / $totalSent) * 100, 2) : 0,
            'campaigns' => $this->getCampaignsList(),
            'campaign_performance' => $this->getCampaignPerformanceData(),
        ];
    }

    /**
     * Get subscriber statistics
     */
    public function getSubscriberStats(): array
    {
        $totalSubscribers = Newsletter::count();
        $validatedSubscribers = Newsletter::where('is_validated', true)->count();
        $newThisMonth = Newsletter::where('created_at', '>=', now()->startOfMonth())->count();
        $unsubscribedThisMonth = EmailAnalytics::where('unsubscribed', true)
            ->where('unsubscribed_at', '>=', now()->startOfMonth())
            ->count();

        return [
            'total_subscribers' => $totalSubscribers,
            'validated_subscribers' => $validatedSubscribers,
            'new_this_month' => $newThisMonth,
            'unsubscribed_this_month' => $unsubscribedThisMonth,
            'validation_rate' => $totalSubscribers > 0 ? round(($validatedSubscribers / $totalSubscribers) * 100, 2) : 0,
        ];
    }

    /**
     * Get active users (users who have made purchases in last 6 months)
     */
    private function getActiveUsersSubscribers(): Collection
    {
        $activeUserIds = User::whereHas('orders', function ($query): void {
            $query->where('created_at', '>=', now()->subMonths(6))
                ->where('payment_status', 'paid');
        })->pluck('id');

        return Newsletter::whereIn('user_id', $activeUserIds)
            ->where('is_validated', true)
            ->get();
    }

    /**
     * Get new subscribers (subscribed in last 30 days)
     */
    private function getNewSubscribers(): Collection
    {
        return Newsletter::where('created_at', '>=', now()->subDays(30))
            ->where('is_validated', true)
            ->get();
    }

    /**
     * Get premium users (users with high-value orders)
     */
    private function getPremiumUsersSubscribers(): Collection
    {
        $premiumUserIds = User::whereHas('orders', function ($query): void {
            $query->where('total_amount', '>=', 500)
                ->where('payment_status', 'paid');
        })->pluck('id');

        return Newsletter::whereIn('user_id', $premiumUserIds)
            ->where('is_validated', true)
            ->get();
    }

    /**
     * Get inactive users (no activity in last 3 months)
     */
    private function getInactiveUsersSubscribers(): Collection
    {
        $inactiveUserIds = User::whereDoesntHave('orders', function ($query): void {
            $query->where('created_at', '>=', now()->subMonths(3));
        })->pluck('id');

        return Newsletter::whereIn('user_id', $inactiveUserIds)
            ->where('is_validated', true)
            ->get();
    }

    /**
     * Get product-interested subscribers (users who viewed products)
     */
    private function getProductInterestedSubscribers(): Collection
    {
        // This would require product view tracking
        // For now, return all validated subscribers
        return Newsletter::where('is_validated', true)->get();
    }

    /**
     * Get blog readers (users who commented on posts)
     */
    private function getBlogReadersSubscribers(): Collection
    {
        $blogReaderIds = User::whereHas('post_comments')->pluck('id');

        return Newsletter::whereIn('user_id', $blogReaderIds)
            ->where('is_validated', true)
            ->get();
    }

    /**
     * Get campaigns list
     */
    private function getCampaignsList(): array
    {
        // This would return a list of campaigns with their basic info
        // For now, return empty array as we don't have campaigns table yet
        return [];
    }

    /**
     * Get campaign performance data for charts
     */
    private function getCampaignPerformanceData(): array
    {
        // This would return performance data over time
        // For now, return sample data
        return [
            ['date' => '2025-01-01', 'sent' => 100, 'opened' => 25],
            ['date' => '2025-01-02', 'sent' => 150, 'opened' => 40],
            ['date' => '2025-01-03', 'sent' => 200, 'opened' => 60],
        ];
    }

    /**
     * Get date range for analytics
     */
    private function getDateRange(string $period): array
    {
        return match ($period) {
            '7_days' => [
                'from' => now()->subDays(7),
                'to' => now(),
            ],
            '30_days' => [
                'from' => now()->subDays(30),
                'to' => now(),
            ],
            '90_days' => [
                'from' => now()->subDays(90),
                'to' => now(),
            ],
            '1_year' => [
                'from' => now()->subYear(),
                'to' => now(),
            ],
            default => [
                'from' => now()->subDays(30),
                'to' => now(),
            ],
        };
    }
}
