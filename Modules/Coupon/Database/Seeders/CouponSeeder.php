<?php

declare(strict_types=1);

namespace Modules\Coupon\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => 'abc123',
                'type' => 'fixed',
                'value' => '300',
                'status' => 'active',
            ],
            [
                'code' => '111111',
                'type' => 'percent',
                'value' => '10',
                'status' => 'active',
            ],
        ];

        DB::table('coupons')->insert($data);
    }
}
