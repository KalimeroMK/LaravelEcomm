<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Carbon\Carbon;
use Modules\Order\Models\Order;

readonly class GetIncomeChartAction
{
    public function execute(?int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;

        $items = Order::with(['cart_info'])
            ->whereYear('created_at', $year)
            ->where('status', 'delivered')
            ->get()
            ->groupBy(fn ($d): string => Carbon::parse($d->created_at)->format('m'));

        $result = [];

        foreach ($items as $month => $orderGroup) {
            foreach ($orderGroup as $order) {
                $amount = $order->cart_info->sum('amount');
                $m = (int) $month;
                $result[$m] = ($result[$m] ?? 0) + $amount;
            }
        }

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $timestamp = mktime(0, 0, 0, $i, 1);
            $monthName = $timestamp === false ? 'Invalid' : date('F', $timestamp);
            $data[$monthName] = isset($result[$i]) ? (float) number_format($result[$i], 2, '.', '') : 0.0;
        }

        return $data;
    }
}
