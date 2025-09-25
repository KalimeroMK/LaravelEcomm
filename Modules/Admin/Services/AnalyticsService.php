<?php

declare(strict_types=1);

namespace Modules\Admin\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Newsletter\Models\EmailAnalytics;
use Modules\Newsletter\Models\Newsletter;
use Modules\Order\Models\Order;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;
use Modules\ProductStats\Models\ProductImpression;
use Modules\User\Models\User;

class AnalyticsService
{
    /**
     * Get comprehensive dashboard analytics
     */
    public function getDashboardAnalytics(): array
    {
        return [
            'overview' => $this->getOverviewStats(),
            'sales' => $this->getSalesAnalytics(),
            'users' => $this->getUserAnalytics(),
            'products' => $this->getProductAnalytics(),
            'content' => $this->getContentAnalytics(),
            'marketing' => $this->getMarketingAnalytics(),
            'performance' => $this->getPerformanceMetrics(),
        ];
    }

    /**
     * Get overview statistics
     */
    public function getOverviewStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'total_revenue' => [
                'current' => Order::where('payment_status', 'paid')->sum('total_amount'),
                'today' => Order::where('payment_status', 'paid')->whereDate('created_at', $today)->sum('total_amount'),
                'yesterday' => Order::where('payment_status', 'paid')->whereDate('created_at', $yesterday)->sum('total_amount'),
                'this_month' => Order::where('payment_status', 'paid')->where('created_at', '>=', $thisMonth)->sum('total_amount'),
                'last_month' => Order::where('payment_status', 'paid')
                    ->whereBetween('created_at', [$lastMonth, $thisMonth])
                    ->sum('total_amount'),
            ],
            'total_orders' => [
                'current' => Order::count(),
                'today' => Order::whereDate('created_at', $today)->count(),
                'yesterday' => Order::whereDate('created_at', $yesterday)->count(),
                'this_month' => Order::where('created_at', '>=', $thisMonth)->count(),
                'last_month' => Order::whereBetween('created_at', [$lastMonth, $thisMonth])->count(),
            ],
            'total_customers' => [
                'current' => User::count(),
                'today' => User::whereDate('created_at', $today)->count(),
                'yesterday' => User::whereDate('created_at', $yesterday)->count(),
                'this_month' => User::where('created_at', '>=', $thisMonth)->count(),
                'last_month' => User::whereBetween('created_at', [$lastMonth, $thisMonth])->count(),
            ],
            'total_products' => [
                'current' => Product::where('status', 'active')->count(),
                'active' => Product::where('status', 'active')->count(),
                'inactive' => Product::where('status', 'inactive')->count(),
                'out_of_stock' => Product::where('stock', 0)->count(),
                'low_stock' => Product::where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            ],
        ];
    }

    /**
     * Get sales analytics
     */
    public function getSalesAnalytics(): array
    {
        return [
            'revenue_by_month' => $this->getRevenueByMonth(),
            'orders_by_month' => $this->getOrdersByMonth(),
            'top_selling_products' => $this->getTopSellingProducts(),
            'sales_by_status' => $this->getSalesByStatus(),
            'average_order_value' => $this->getAverageOrderValue(),
            'conversion_rate' => $this->getConversionRate(),
        ];
    }

    /**
     * Get user analytics
     */
    public function getUserAnalytics(): array
    {
        return [
            'user_registrations' => $this->getUserRegistrations(),
            'user_activity' => $this->getUserActivity(),
            'user_segments' => $this->getUserSegments(),
            'customer_lifetime_value' => $this->getCustomerLifetimeValue(),
        ];
    }

    /**
     * Get product analytics
     */
    public function getProductAnalytics(): array
    {
        return [
            'product_performance' => $this->getProductPerformance(),
            'category_performance' => $this->getCategoryPerformance(),
            'brand_performance' => $this->getBrandPerformance(),
            'inventory_status' => $this->getInventoryStatus(),
        ];
    }

    /**
     * Get content analytics
     */
    public function getContentAnalytics(): array
    {
        return [
            'blog_performance' => $this->getBlogPerformance(),
            'content_engagement' => $this->getContentEngagement(),
        ];
    }

    /**
     * Get marketing analytics
     */
    public function getMarketingAnalytics(): array
    {
        return [
            'email_campaigns' => $this->getEmailCampaignAnalytics(),
            'newsletter_stats' => $this->getNewsletterStats(),
            'abandoned_carts' => $this->getAbandonedCartStats(),
        ];
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'page_views' => $this->getPageViews(),
            'bounce_rate' => $this->getBounceRate(),
            'session_duration' => $this->getSessionDuration(),
            'traffic_sources' => $this->getTrafficSources(),
        ];
    }

    /**
     * Get revenue by month for the last 12 months
     */
    private function getRevenueByMonth(): array
    {
        $revenue = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as revenue')
        )
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return $revenue->map(function ($item): array {
            return [
                'month' => Carbon::create($item->year, $item->month)->format('M Y'),
                'revenue' => (float) $item->revenue,
            ];
        })->toArray();
    }

    /**
     * Get orders by month for the last 12 months
     */
    private function getOrdersByMonth(): array
    {
        $orders = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return $orders->map(function ($item): array {
            return [
                'month' => Carbon::create($item->year, $item->month)->format('M Y'),
                'orders' => (int) $item->count,
            ];
        })->toArray();
    }

    /**
     * Get top selling products
     */
    private function getTopSellingProducts(): array
    {
        $topProducts = DB::table('carts')
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->join('orders', 'carts.order_id', '=', 'orders.id')
            ->select(
                'products.id',
                'products.title',
                'products.price',
                DB::raw('SUM(carts.quantity) as total_sold'),
                DB::raw('SUM(carts.amount) as total_revenue')
            )
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('products.id', 'products.title', 'products.price')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        return $topProducts->toArray();
    }

    /**
     * Get sales by status
     */
    private function getSalesByStatus(): array
    {
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $paymentStatusCounts = Order::select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->get()
            ->pluck('count', 'payment_status')
            ->toArray();

        return [
            'order_status' => $statusCounts,
            'payment_status' => $paymentStatusCounts,
        ];
    }

    /**
     * Get average order value
     */
    private function getAverageOrderValue(): array
    {
        $paidOrders = Order::where('payment_status', 'paid');

        return [
            'overall' => $paidOrders->avg('total_amount'),
            'this_month' => $paidOrders->where('created_at', '>=', Carbon::now()->startOfMonth())->avg('total_amount'),
            'last_month' => $paidOrders->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->startOfMonth(),
            ])->avg('total_amount'),
        ];
    }

    /**
     * Get conversion rate
     */
    private function getConversionRate(): array
    {
        $totalVisitors = User::count(); // Simplified - in real app, use session/analytics data
        $totalOrders = Order::where('payment_status', 'paid')->count();

        $conversionRate = $totalVisitors > 0 ? ($totalOrders / $totalVisitors) * 100 : 0;

        return [
            'overall' => round($conversionRate, 2),
            'this_month' => $this->getMonthlyConversionRate(),
        ];
    }

    /**
     * Get monthly conversion rate
     */
    private function getMonthlyConversionRate(): float
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlyVisitors = User::where('created_at', '>=', $thisMonth)->count();
        $monthlyOrders = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $thisMonth)
            ->count();

        return $monthlyVisitors > 0 ? round(($monthlyOrders / $monthlyVisitors) * 100, 2) : 0;
    }

    /**
     * Get user registrations for the last 30 days
     */
    private function getUserRegistrations(): array
    {
        $registrations = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', $date)->count();
            $registrations[$date] = $count;
        }

        return $registrations;
    }

    /**
     * Get user activity metrics
     */
    private function getUserActivity(): array
    {
        return [
            'active_users_today' => User::whereDate('updated_at', Carbon::today())->count(),
            'active_users_this_week' => User::where('updated_at', '>=', Carbon::now()->subWeek())->count(),
            'active_users_this_month' => User::where('updated_at', '>=', Carbon::now()->startOfMonth())->count(),
            'new_users_today' => User::whereDate('created_at', Carbon::today())->count(),
        ];
    }

    /**
     * Get user segments
     */
    private function getUserSegments(): array
    {
        return [
            'customers_with_orders' => User::whereHas('orders')->count(),
            'customers_without_orders' => User::whereDoesntHave('orders')->count(),
            'repeat_customers' => User::whereHas('orders', function ($query): void {
                $query->havingRaw('COUNT(*) > 1');
            })->count(),
            'high_value_customers' => User::whereHas('orders', function ($query): void {
                $query->where('total_amount', '>=', 500);
            })->count(),
        ];
    }

    /**
     * Get customer lifetime value
     */
    private function getCustomerLifetimeValue(): array
    {
        $customersWithOrders = User::whereHas('orders')->with('orders')->get();

        $totalLifetimeValue = $customersWithOrders->sum(function ($user) {
            return $user->orders->where('payment_status', 'paid')->sum('total_amount');
        });

        $averageLifetimeValue = $customersWithOrders->count() > 0
            ? $totalLifetimeValue / $customersWithOrders->count()
            : 0;

        return [
            'average' => round($averageLifetimeValue, 2),
            'total' => $totalLifetimeValue,
        ];
    }

    /**
     * Get product performance metrics
     */
    private function getProductPerformance(): array
    {
        $products = Product::with(['clicks', 'impressions'])->get();

        return $products->map(function ($product): array {
            $clicks = $product->clicks->count();
            $impressions = $product->impressions->count();
            $ctr = $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0;

            return [
                'id' => $product->id,
                'title' => $product->title,
                'clicks' => $clicks,
                'impressions' => $impressions,
                'ctr' => $ctr,
                'views' => $impressions, // Assuming impressions = views
            ];
        })->sortByDesc('clicks')->take(10)->values()->toArray();
    }

    /**
     * Get category performance
     */
    private function getCategoryPerformance(): array
    {
        return Category::withCount(['products'])
            ->orderBy('products_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($category): array {
                return [
                    'id' => $category->id,
                    'title' => $category->title,
                    'products_count' => $category->products_count,
                ];
            })->toArray();
    }

    /**
     * Get brand performance
     */
    private function getBrandPerformance(): array
    {
        return Brand::withCount(['products'])
            ->orderBy('products_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($brand): array {
                return [
                    'id' => $brand->id,
                    'title' => $brand->title,
                    'products_count' => $brand->products_count,
                ];
            })->toArray();
    }

    /**
     * Get inventory status
     */
    private function getInventoryStatus(): array
    {
        return [
            'total_products' => Product::count(),
            'in_stock' => Product::where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock' => Product::where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            'total_value' => Product::sum(DB::raw('price * stock')),
        ];
    }

    /**
     * Get blog performance
     */
    private function getBlogPerformance(): array
    {
        return Post::select('id', 'title', 'created_at', 'status')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get content engagement
     */
    private function getContentEngagement(): array
    {
        return [
            'total_posts' => Post::count(),
            'published_posts' => Post::where('status', 'active')->count(),
            'draft_posts' => Post::where('status', 'inactive')->count(),
            'posts_this_month' => Post::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
        ];
    }

    /**
     * Get email campaign analytics
     */
    private function getEmailCampaignAnalytics(): array
    {
        $analytics = EmailAnalytics::select(
            DB::raw('COUNT(*) as total_sent'),
            DB::raw('COUNT(opened_at) as total_opened'),
            DB::raw('COUNT(clicked_at) as total_clicked'),
            DB::raw('COUNT(CASE WHEN bounced = 1 THEN 1 END) as total_bounced'),
            DB::raw('COUNT(CASE WHEN unsubscribed = 1 THEN 1 END) as total_unsubscribed')
        )->first();

        $totalSent = $analytics->total_sent ?? 0;
        $totalOpened = $analytics->total_opened ?? 0;
        $totalClicked = $analytics->total_clicked ?? 0;

        return [
            'total_sent' => $totalSent,
            'total_opened' => $totalOpened,
            'total_clicked' => $totalClicked,
            'open_rate' => $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0,
            'click_rate' => $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0,
            'bounce_rate' => $totalSent > 0 ? round((($analytics->total_bounced ?? 0) / $totalSent) * 100, 2) : 0,
            'unsubscribe_rate' => $totalSent > 0 ? round((($analytics->total_unsubscribed ?? 0) / $totalSent) * 100, 2) : 0,
        ];
    }

    /**
     * Get newsletter statistics
     */
    private function getNewsletterStats(): array
    {
        return [
            'total_subscribers' => Newsletter::count(),
            'validated_subscribers' => Newsletter::where('is_validated', true)->count(),
            'new_this_month' => Newsletter::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
        ];
    }

    /**
     * Get abandoned cart statistics
     */
    private function getAbandonedCartStats(): array
    {
        // This would use the AbandonedCart model we created earlier
        return [
            'total_abandoned' => 0, // Placeholder - implement with AbandonedCart model
            'recovery_rate' => 0, // Placeholder
            'revenue_recovered' => 0, // Placeholder
        ];
    }

    /**
     * Get page views (simplified)
     */
    private function getPageViews(): array
    {
        // In a real application, this would come from analytics tracking
        return [
            'today' => ProductImpression::whereDate('created_at', Carbon::today())->count(),
            'this_week' => ProductImpression::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'this_month' => ProductImpression::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
        ];
    }

    /**
     * Get bounce rate (simplified)
     */
    private function getBounceRate(): float
    {
        // Simplified calculation - in real app, use proper analytics
        $totalSessions = User::count(); // Placeholder
        $singlePageSessions = User::whereDoesntHave('orders')->count(); // Placeholder

        return $totalSessions > 0 ? round(($singlePageSessions / $totalSessions) * 100, 2) : 0;
    }

    /**
     * Get session duration (simplified)
     */
    private function getSessionDuration(): array
    {
        // Placeholder - in real app, use proper session tracking
        return [
            'average' => 180, // 3 minutes in seconds
            'median' => 120, // 2 minutes in seconds
        ];
    }

    /**
     * Get traffic sources (simplified)
     */
    private function getTrafficSources(): array
    {
        // Placeholder - in real app, use proper analytics
        return [
            'direct' => 40,
            'organic' => 35,
            'social' => 15,
            'referral' => 10,
        ];
    }
}
