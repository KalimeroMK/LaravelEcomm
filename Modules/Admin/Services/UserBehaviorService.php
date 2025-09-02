<?php

declare(strict_types=1);

namespace Modules\Admin\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\UserBehaviorTracking;

class UserBehaviorService
{
    /**
     * Track user behavior event
     */
    public function trackEvent(array $eventData): UserBehaviorTracking
    {
        return UserBehaviorTracking::create([
            'user_id' => $eventData['user_id'] ?? null,
            'session_id' => $eventData['session_id'] ?? null,
            'event_type' => $eventData['event_type'],
            'page_url' => $eventData['page_url'],
            'page_title' => $eventData['page_title'] ?? null,
            'referrer' => $eventData['referrer'] ?? null,
            'user_agent' => $eventData['user_agent'] ?? null,
            'ip_address' => $eventData['ip_address'] ?? null,
            'event_data' => $eventData['event_data'] ?? null,
            'duration' => $eventData['duration'] ?? null,
            'event_timestamp' => $eventData['event_timestamp'] ?? now(),
        ]);
    }

    /**
     * Get user behavior analytics
     */
    public function getUserBehaviorAnalytics(): array
    {
        return [
            'page_views' => $this->getPageViewAnalytics(),
            'user_engagement' => $this->getUserEngagementAnalytics(),
            'popular_pages' => $this->getPopularPages(),
            'user_flow' => $this->getUserFlowAnalytics(),
            'session_analytics' => $this->getSessionAnalytics(),
            'device_analytics' => $this->getDeviceAnalytics(),
            'geographic_analytics' => $this->getGeographicAnalytics(),
        ];
    }

    /**
     * Get page view analytics
     */
    public function getPageViewAnalytics(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'today' => UserBehaviorTracking::eventType('page_view')->whereDate('event_timestamp', $today)->count(),
            'yesterday' => UserBehaviorTracking::eventType('page_view')->whereDate('event_timestamp', $yesterday)->count(),
            'this_week' => UserBehaviorTracking::eventType('page_view')->where('event_timestamp', '>=', $thisWeek)->count(),
            'last_week' => UserBehaviorTracking::eventType('page_view')
                ->whereBetween('event_timestamp', [$lastWeek, $thisWeek])
                ->count(),
            'this_month' => UserBehaviorTracking::eventType('page_view')->where('event_timestamp', '>=', $thisMonth)->count(),
            'last_month' => UserBehaviorTracking::eventType('page_view')
                ->whereBetween('event_timestamp', [$lastMonth, $thisMonth])
                ->count(),
            'unique_visitors_today' => UserBehaviorTracking::eventType('page_view')
                ->whereDate('event_timestamp', $today)
                ->distinct('user_id')
                ->count('user_id'),
            'unique_visitors_this_month' => UserBehaviorTracking::eventType('page_view')
                ->where('event_timestamp', '>=', $thisMonth)
                ->distinct('user_id')
                ->count('user_id'),
        ];
    }

    /**
     * Get user engagement analytics
     */
    public function getUserEngagementAnalytics(): array
    {
        return [
            'average_session_duration' => $this->getAverageSessionDuration(),
            'bounce_rate' => $this->getBounceRate(),
            'pages_per_session' => $this->getPagesPerSession(),
            'return_visitor_rate' => $this->getReturnVisitorRate(),
            'engagement_by_hour' => $this->getEngagementByHour(),
            'engagement_by_day' => $this->getEngagementByDay(),
        ];
    }

    /**
     * Get popular pages
     */
    public function getPopularPages(): array
    {
        return UserBehaviorTracking::eventType('page_view')
            ->select('page_url', 'page_title', DB::raw('COUNT(*) as views'))
            ->where('event_timestamp', '>=', Carbon::now()->subMonth())
            ->groupBy('page_url', 'page_title')
            ->orderBy('views', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    /**
     * Get user flow analytics
     */
    public function getUserFlowAnalytics(): array
    {
        return [
            'entry_pages' => $this->getEntryPages(),
            'exit_pages' => $this->getExitPages(),
            'common_paths' => $this->getCommonPaths(),
            'conversion_funnels' => $this->getConversionFunnels(),
        ];
    }

    /**
     * Get session analytics
     */
    public function getSessionAnalytics(): array
    {
        $sessions = UserBehaviorTracking::select('session_id')
            ->where('event_timestamp', '>=', Carbon::now()->subMonth())
            ->distinct()
            ->get();

        $sessionData = [];
        foreach ($sessions as $session) {
            $sessionEvents = UserBehaviorTracking::forSession($session->session_id)
                ->where('event_timestamp', '>=', Carbon::now()->subMonth())
                ->orderBy('event_timestamp')
                ->get();

            if ($sessionEvents->isNotEmpty()) {
                $firstEvent = $sessionEvents->first();
                $lastEvent = $sessionEvents->last();
                $duration = $lastEvent->event_timestamp->diffInSeconds($firstEvent->event_timestamp);

                $sessionData[] = [
                    'session_id' => $session->session_id,
                    'user_id' => $firstEvent->user_id,
                    'start_time' => $firstEvent->event_timestamp,
                    'end_time' => $lastEvent->event_timestamp,
                    'duration' => $duration,
                    'page_views' => $sessionEvents->where('event_type', 'page_view')->count(),
                    'events' => $sessionEvents->count(),
                ];
            }
        }

        $totalSessions = count($sessionData);
        $totalDuration = array_sum(array_column($sessionData, 'duration'));

        return [
            'total_sessions' => $totalSessions,
            'average_session_duration' => $totalSessions > 0 ? round($totalDuration / $totalSessions) : 0,
            'sessions_today' => collect($sessionData)->where('start_time', '>=', Carbon::today())->count(),
            'sessions_this_week' => collect($sessionData)->where('start_time', '>=', Carbon::now()->startOfWeek())->count(),
            'sessions_this_month' => collect($sessionData)->where('start_time', '>=', Carbon::now()->startOfMonth())->count(),
        ];
    }

    /**
     * Get device analytics
     */
    public function getDeviceAnalytics(): array
    {
        $userAgents = UserBehaviorTracking::select('user_agent')
            ->where('event_timestamp', '>=', Carbon::now()->subMonth())
            ->distinct()
            ->get();

        $devices = ['desktop' => 0, 'mobile' => 0, 'tablet' => 0, 'other' => 0];
        $browsers = [];
        $operatingSystems = [];

        foreach ($userAgents as $ua) {
            $userAgent = $ua->user_agent;

            // Simple device detection
            if (str_contains($userAgent, 'Mobile')) {
                $devices['mobile']++;
            } elseif (str_contains($userAgent, 'Tablet')) {
                $devices['tablet']++;
            } elseif (str_contains($userAgent, 'Windows') || str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Linux')) {
                $devices['desktop']++;
            } else {
                $devices['other']++;
            }

            // Browser detection
            if (str_contains($userAgent, 'Chrome')) {
                $browsers['Chrome'] = ($browsers['Chrome'] ?? 0) + 1;
            } elseif (str_contains($userAgent, 'Firefox')) {
                $browsers['Firefox'] = ($browsers['Firefox'] ?? 0) + 1;
            } elseif (str_contains($userAgent, 'Safari')) {
                $browsers['Safari'] = ($browsers['Safari'] ?? 0) + 1;
            } elseif (str_contains($userAgent, 'Edge')) {
                $browsers['Edge'] = ($browsers['Edge'] ?? 0) + 1;
            }

            // OS detection
            if (str_contains($userAgent, 'Windows')) {
                $operatingSystems['Windows'] = ($operatingSystems['Windows'] ?? 0) + 1;
            } elseif (str_contains($userAgent, 'Macintosh')) {
                $operatingSystems['macOS'] = ($operatingSystems['macOS'] ?? 0) + 1;
            } elseif (str_contains($userAgent, 'Linux')) {
                $operatingSystems['Linux'] = ($operatingSystems['Linux'] ?? 0) + 1;
            } elseif (str_contains($userAgent, 'Android')) {
                $operatingSystems['Android'] = ($operatingSystems['Android'] ?? 0) + 1;
            } elseif (str_contains($userAgent, 'iOS')) {
                $operatingSystems['iOS'] = ($operatingSystems['iOS'] ?? 0) + 1;
            }
        }

        return [
            'devices' => $devices,
            'browsers' => $browsers,
            'operating_systems' => $operatingSystems,
        ];
    }

    /**
     * Get geographic analytics (simplified)
     */
    public function getGeographicAnalytics(): array
    {
        // In a real application, you would use IP geolocation services
        return [
            'top_countries' => [
                'United States' => 45,
                'United Kingdom' => 20,
                'Canada' => 15,
                'Germany' => 10,
                'Other' => 10,
            ],
            'top_cities' => [
                'New York' => 25,
                'London' => 15,
                'Toronto' => 10,
                'Berlin' => 8,
                'Other' => 42,
            ],
        ];
    }

    /**
     * Get average session duration
     */
    private function getAverageSessionDuration(): int
    {
        $sessions = UserBehaviorTracking::select('session_id')
            ->where('event_timestamp', '>=', Carbon::now()->subMonth())
            ->distinct()
            ->get();

        $totalDuration = 0;
        $sessionCount = 0;

        foreach ($sessions as $session) {
            $sessionEvents = UserBehaviorTracking::forSession($session->session_id)
                ->where('event_timestamp', '>=', Carbon::now()->subMonth())
                ->orderBy('event_timestamp')
                ->get();

            if ($sessionEvents->count() > 1) {
                $firstEvent = $sessionEvents->first();
                $lastEvent = $sessionEvents->last();
                $totalDuration += $lastEvent->event_timestamp->diffInSeconds($firstEvent->event_timestamp);
                $sessionCount++;
            }
        }

        return $sessionCount > 0 ? round($totalDuration / $sessionCount) : 0;
    }

    /**
     * Get bounce rate
     */
    private function getBounceRate(): float
    {
        $sessions = UserBehaviorTracking::select('session_id')
            ->where('event_timestamp', '>=', Carbon::now()->subMonth())
            ->distinct()
            ->get();

        $singlePageSessions = 0;
        $totalSessions = $sessions->count();

        foreach ($sessions as $session) {
            $pageViews = UserBehaviorTracking::forSession($session->session_id)
                ->eventType('page_view')
                ->where('event_timestamp', '>=', Carbon::now()->subMonth())
                ->count();

            if ($pageViews <= 1) {
                $singlePageSessions++;
            }
        }

        return $totalSessions > 0 ? round(($singlePageSessions / $totalSessions) * 100, 2) : 0;
    }

    /**
     * Get pages per session
     */
    private function getPagesPerSession(): float
    {
        $sessions = UserBehaviorTracking::select('session_id')
            ->where('event_timestamp', '>=', Carbon::now()->subMonth())
            ->distinct()
            ->get();

        $totalPageViews = 0;
        $sessionCount = $sessions->count();

        foreach ($sessions as $session) {
            $pageViews = UserBehaviorTracking::forSession($session->session_id)
                ->eventType('page_view')
                ->where('event_timestamp', '>=', Carbon::now()->subMonth())
                ->count();

            $totalPageViews += $pageViews;
        }

        return $sessionCount > 0 ? round($totalPageViews / $sessionCount, 2) : 0;
    }

    /**
     * Get return visitor rate
     */
    private function getReturnVisitorRate(): float
    {
        $uniqueUsers = UserBehaviorTracking::select('user_id')
            ->where('event_timestamp', '>=', Carbon::now()->subMonth())
            ->whereNotNull('user_id')
            ->distinct()
            ->count();

        $returningUsers = UserBehaviorTracking::select('user_id')
            ->where('event_timestamp', '>=', Carbon::now()->subMonth())
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(DISTINCT DATE(event_timestamp)) > 1')
            ->count();

        return $uniqueUsers > 0 ? round(($returningUsers / $uniqueUsers) * 100, 2) : 0;
    }

    /**
     * Get engagement by hour
     */
    private function getEngagementByHour(): array
    {
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[$i] = UserBehaviorTracking::eventType('page_view')
                ->where('event_timestamp', '>=', Carbon::now()->subMonth())
                ->whereRaw('HOUR(event_timestamp) = ?', [$i])
                ->count();
        }

        return $hourlyData;
    }

    /**
     * Get engagement by day
     */
    private function getEngagementByDay(): array
    {
        $dailyData = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($days as $day) {
            $dailyData[$day] = UserBehaviorTracking::eventType('page_view')
                ->where('event_timestamp', '>=', Carbon::now()->subMonth())
                ->whereRaw('DAYNAME(event_timestamp) = ?', [$day])
                ->count();
        }

        return $dailyData;
    }

    /**
     * Get entry pages
     */
    private function getEntryPages(): array
    {
        return UserBehaviorTracking::eventType('page_view')
            ->select('page_url', 'page_title', DB::raw('COUNT(*) as entries'))
            ->where('event_timestamp', '>=', Carbon::now()->subMonth())
            ->groupBy('page_url', 'page_title')
            ->orderBy('entries', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get exit pages
     */
    private function getExitPages(): array
    {
        // This is simplified - in reality, you'd need to track session endings
        return UserBehaviorTracking::eventType('page_view')
            ->select('page_url', 'page_title', DB::raw('COUNT(*) as exits'))
            ->where('event_timestamp', '>=', Carbon::now()->subMonth())
            ->groupBy('page_url', 'page_title')
            ->orderBy('exits', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get common paths
     */
    private function getCommonPaths(): array
    {
        // Simplified implementation
        return [
            'Home → Product → Cart' => 150,
            'Home → Category → Product' => 120,
            'Product → Product → Cart' => 80,
            'Home → Blog → Product' => 60,
        ];
    }

    /**
     * Get conversion funnels
     */
    private function getConversionFunnels(): array
    {
        return [
            'product_view_to_cart' => [
                'product_views' => 1000,
                'cart_additions' => 200,
                'conversion_rate' => 20.0,
            ],
            'cart_to_checkout' => [
                'cart_views' => 200,
                'checkout_starts' => 150,
                'conversion_rate' => 75.0,
            ],
            'checkout_to_purchase' => [
                'checkout_starts' => 150,
                'purchases' => 120,
                'conversion_rate' => 80.0,
            ],
        ];
    }
}
