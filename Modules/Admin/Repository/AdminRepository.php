<?php

namespace Modules\Admin\Repository;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\User\Models\User;

class AdminRepository
{
    /**
     * @return array
     */
    public function index(): array
    {
        $data = User::query()
            ->selectRaw('COUNT(*) as count, DAYNAME(created_at) as day_name, DAY(created_at) as day')
            ->where('created_at', '>', Carbon::today()->subDays(6))
            ->groupBy('day_name', 'day')
            ->orderBy('day')
            ->get();

        $array = [['Day', 'Count']];

        foreach ($data as $row) {
            $array[] = [$row->day_name, $row->count];
        }

        return $array;
    }

}
