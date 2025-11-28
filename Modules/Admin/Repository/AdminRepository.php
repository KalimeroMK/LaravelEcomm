<?php

declare(strict_types=1);

namespace Modules\Admin\Repository;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

class AdminRepository extends EloquentRepository
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    /**
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
     */
    /**
     * @return Collection<int, Order>
     */
    public function getPaidOrdersCountByMonth(): Collection
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return Order::select(
                DB::raw('CAST(strftime("%m", created_at) AS INTEGER) as month'),
                DB::raw('CAST(strftime("%Y", created_at) AS INTEGER) as year'),
                DB::raw('COUNT(*) as count')
            )
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', now()->subYear())
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
        }

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
