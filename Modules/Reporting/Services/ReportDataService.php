<?php

declare(strict_types=1);

namespace Modules\Reporting\Services;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\Reporting\Models\Report;
use Modules\User\Models\User;

readonly class ReportDataService
{
    /**
     * Generate report data based on report type and filters
     *
     * @return array{data: Collection, summary: array}
     */
    public function generate(Report $report, array $parameters = []): array
    {
        $filters = array_merge($report->filters ?? [], $parameters);
        
        return match ($report->type) {
            Report::TYPE_SALES => $this->generateSalesReport($filters),
            Report::TYPE_PRODUCTS => $this->generateProductsReport($filters),
            Report::TYPE_CUSTOMERS => $this->generateCustomersReport($filters),
            Report::TYPE_ORDERS => $this->generateOrdersReport($filters),
            Report::TYPE_REVENUE => $this->generateRevenueReport($filters),
            Report::TYPE_INVENTORY => $this->generateInventoryReport($filters),
            Report::TYPE_COUPONS => $this->generateCouponsReport($filters),
            Report::TYPE_TAX => $this->generateTaxReport($filters),
            default => ['data' => collect(), 'summary' => []],
        };
    }

    /**
     * Generate sales report
     *
     * @return array{data: Collection, summary: array}
     */
    private function generateSalesReport(array $filters): array
    {
        $query = Order::query()
            ->whereNotNull('completed_at')
            ->with(['orderItems.product', 'user']);

        $this->applyDateRange($query, $filters);
        $this->applyStatusFilter($query, $filters);

        $orders = $query->get();

        $data = collect();
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $data->push([
                    'date' => $order->completed_at->format('Y-m-d'),
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer' => $order->user?->name ?? 'Guest',
                    'customer_email' => $order->user?->email ?? $order->guest_email,
                    'product' => $item->product?->title ?? $item->product_name,
                    'sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total' => $item->total,
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                ]);
            }
        }

        $summary = [
            'total_orders' => $orders->count(),
            'total_items' => $data->sum('quantity'),
            'total_revenue' => $data->sum('total'),
            'average_order_value' => $orders->count() > 0 ? $data->sum('total') / $orders->count() : 0,
        ];

        return ['data' => $data, 'summary' => $summary];
    }

    /**
     * Generate products report
     *
     * @return array{data: Collection, summary: array}
     */
    private function generateProductsReport(array $filters): array
    {
        $query = Product::query()->with(['categories', 'brand']);

        $this->applyCategoryFilter($query, $filters);
        $this->applyStockStatusFilter($query, $filters);

        $products = $query->get();

        // Get sales data for each product
        $salesData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotNull('orders.completed_at')
            ->select(
                'order_items.product_id',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.total) as total_revenue')
            )
            ->groupBy('order_items.product_id')
            ->get()
            ->keyBy('product_id');

        $data = $products->map(fn ($product) => [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->title,
            'category' => $product->categories->first()?->title ?? 'Uncategorized',
            'brand' => $product->brand?->title ?? '-',
            'stock' => $product->stock,
            'price' => $product->price,
            'status' => $product->status,
            'sold' => $salesData[$product->id]->total_sold ?? 0,
            'revenue' => $salesData[$product->id]->total_revenue ?? 0,
        ]);

        $summary = [
            'total_products' => $data->count(),
            'total_sold' => $data->sum('sold'),
            'total_revenue' => $data->sum('revenue'),
            'low_stock_count' => $data->where('stock', '<', 10)->count(),
        ];

        return ['data' => $data, 'summary' => $summary];
    }

    /**
     * Generate customers report
     *
     * @return array{data: Collection, summary: array}
     */
    private function generateCustomersReport(array $filters): array
    {
        $query = User::query()->withCount('orders')->withSum('orders as total_spent', 'total');

        if (! empty($filters['date_from'])) {
            $query->whereHas('orders', function ($q) use ($filters): void {
                $q->whereDate('created_at', '>=', $filters['date_from']);
            });
        }

        if (! empty($filters['min_orders'])) {
            $query->having('orders_count', '>=', $filters['min_orders']);
        }

        $users = $query->get();

        $data = $users->map(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'orders' => $user->orders_count,
            'spent' => $user->total_spent ?? 0,
            'last_order' => $user->orders->first()?->created_at?->format('Y-m-d') ?? '-',
            'registered' => $user->created_at->format('Y-m-d'),
            'status' => $user->status ?? 'active',
        ]);

        $summary = [
            'total_customers' => $data->count(),
            'total_orders' => $data->sum('orders'),
            'total_spent' => $data->sum('spent'),
            'average_orders_per_customer' => $data->count() > 0 ? $data->sum('orders') / $data->count() : 0,
            'average_spent_per_customer' => $data->count() > 0 ? $data->sum('spent') / $data->count() : 0,
        ];

        return ['data' => $data, 'summary' => $summary];
    }

    /**
     * Generate orders report
     *
     * @return array{data: Collection, summary: array}
     */
    private function generateOrdersReport(array $filters): array
    {
        $query = Order::query()->with(['user', 'orderItems']);

        $this->applyDateRange($query, $filters);
        $this->applyStatusFilter($query, $filters);

        $orders = $query->get();

        $data = $orders->map(fn ($order) => [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'date' => $order->created_at->format('Y-m-d H:i'),
            'customer' => $order->user?->name ?? $order->guest_email ?? 'Guest',
            'items' => $order->orderItems->sum('quantity'),
            'subtotal' => $order->subtotal,
            'discount' => $order->discount ?? 0,
            'shipping' => $order->shipping_cost ?? 0,
            'tax' => $order->tax ?? 0,
            'total' => $order->total,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
        ]);

        $summary = [
            'total_orders' => $data->count(),
            'total_items' => $data->sum('items'),
            'subtotal' => $data->sum('subtotal'),
            'total_discount' => $data->sum('discount'),
            'total_shipping' => $data->sum('shipping'),
            'total_tax' => $data->sum('tax'),
            'total_revenue' => $data->sum('total'),
        ];

        return ['data' => $data, 'summary' => $summary];
    }

    /**
     * Generate revenue report
     *
     * @return array{data: Collection, summary: array}
     */
    private function generateRevenueReport(array $filters): array
    {
        $dateFrom = $filters['date_from'] ?? now()->subDays(30);
        $dateTo = $filters['date_to'] ?? now();

        $orders = Order::query()
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$dateFrom, $dateTo])
            ->get();

        // Group by date
        $grouped = $orders->groupBy(fn ($order) => $order->completed_at->format('Y-m-d'));

        $data = $grouped->map(fn ($dayOrders, $date) => [
            'date' => $date,
            'orders' => $dayOrders->count(),
            'subtotal' => $dayOrders->sum('subtotal'),
            'discount' => $dayOrders->sum('discount'),
            'shipping' => $dayOrders->sum('shipping_cost'),
            'tax' => $dayOrders->sum('tax'),
            'total' => $dayOrders->sum('total'),
        ])->sortBy('date')->values();

        $summary = [
            'total_days' => $data->count(),
            'total_orders' => $data->sum('orders'),
            'total_revenue' => $data->sum('total'),
            'average_daily_revenue' => $data->count() > 0 ? $data->sum('total') / $data->count() : 0,
            'average_order_value' => $data->sum('orders') > 0 ? $data->sum('total') / $data->sum('orders') : 0,
        ];

        return ['data' => $data, 'summary' => $summary];
    }

    /**
     * Generate inventory report
     *
     * @return array{data: Collection, summary: array}
     */
    private function generateInventoryReport(array $filters): array
    {
        $query = Product::query()->with(['categories', 'brand']);

        $this->applyCategoryFilter($query, $filters);
        $this->applyStockStatusFilter($query, $filters);

        $products = $query->get();

        $data = $products->map(fn ($product) => [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->title,
            'category' => $product->categories->first()?->title ?? 'Uncategorized',
            'stock' => $product->stock,
            'stock_value' => $product->stock * $product->price,
            'status' => $this->getStockStatus($product->stock),
            'price' => $product->price,
        ]);

        $summary = [
            'total_products' => $data->count(),
            'total_stock' => $data->sum('stock'),
            'total_stock_value' => $data->sum('stock_value'),
            'in_stock' => $data->where('status', 'in_stock')->count(),
            'low_stock' => $data->where('status', 'low_stock')->count(),
            'out_of_stock' => $data->where('status', 'out_of_stock')->count(),
        ];

        return ['data' => $data, 'summary' => $summary];
    }

    /**
     * Generate coupons report
     *
     * @return array{data: Collection, summary: array}
     */
    private function generateCouponsReport(array $filters): array
    {
        $coupons = \Modules\Coupon\Models\Coupon::query()
            ->withCount('usages')
            ->get();

        $data = $coupons->map(fn ($coupon) => [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'times_used' => $coupon->usages_count,
            'usage_limit' => $coupon->usage_limit,
            'is_active' => $coupon->is_active,
            'starts_at' => $coupon->starts_at?->format('Y-m-d'),
            'expires_at' => $coupon->expires_at?->format('Y-m-d'),
        ]);

        $summary = [
            'total_coupons' => $data->count(),
            'active_coupons' => $data->where('is_active', true)->count(),
            'total_usages' => $data->sum('times_used'),
        ];

        return ['data' => $data, 'summary' => $summary];
    }

    /**
     * Generate tax report
     *
     * @return array{data: Collection, summary: array}
     */
    private function generateTaxReport(array $filters): array
    {
        $query = Order::query()
            ->whereNotNull('completed_at')
            ->where('tax', '>', 0);

        $this->applyDateRange($query, $filters);

        $orders = $query->get();

        $data = $orders->map(fn ($order) => [
            'date' => $order->completed_at->format('Y-m-d'),
            'order_number' => $order->order_number,
            'subtotal' => $order->subtotal,
            'tax_rate' => $order->tax_rate ?? 0,
            'tax_amount' => $order->tax,
            'total' => $order->total,
        ]);

        $summary = [
            'total_orders' => $data->count(),
            'total_subtotal' => $data->sum('subtotal'),
            'total_tax' => $data->sum('tax_amount'),
            'total_with_tax' => $data->sum('total'),
            'average_tax_rate' => $data->count() > 0 ? $data->avg('tax_rate') : 0,
        ];

        return ['data' => $data, 'summary' => $summary];
    }

    private function applyDateRange($query, array $filters): void
    {
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
    }

    private function applyStatusFilter($query, array $filters): void
    {
        if (! empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
    }

    private function applyCategoryFilter($query, array $filters): void
    {
        if (! empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters): void {
                $q->whereIn('categories.id', (array) $filters['category_id']);
            });
        }
    }

    private function applyStockStatusFilter($query, array $filters): void
    {
        if (! empty($filters['stock_status']) && $filters['stock_status'] !== 'all') {
            match ($filters['stock_status']) {
                'in_stock' => $query->where('stock', '>', 10),
                'low_stock' => $query->whereBetween('stock', [1, 10]),
                'out_of_stock' => $query->where('stock', '<=', 0),
                default => null,
            };
        }
    }

    private function getStockStatus(int $stock): string
    {
        return match (true) {
            $stock <= 0 => 'out_of_stock',
            $stock <= 10 => 'low_stock',
            default => 'in_stock',
        };
    }
}
