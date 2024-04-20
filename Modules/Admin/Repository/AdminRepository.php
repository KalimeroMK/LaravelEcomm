<?php

namespace Modules\Admin\Repository;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

class AdminRepository
{
    /**
     *
     * @return array<string, int> Array of paths.
     */
    public function usersLastSevenDays(): array
    {
        $usersCount = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', $date)->count();
            $usersCount[$date] = $count;
        }
        return $usersCount;
    }

    /**
     * Get the count of paid orders for each of the last 12 months.
     *
     * @return Collection
     */
    public function getPaidOrdersCountByMonth(): Collection
    {
        return Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

}
