<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    public function run()
    {
        DB::table('sizes')->insert([
            0 =>
                [
                    'id'         => 1,
                    'name'       => 'XS',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            1 =>
                [
                    'id'         => 2,
                    'name'       => 'S',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            2 =>
                [
                    'id'         => 3,
                    'name'       => 'M',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            3 =>
                [
                    'id'         => 4,
                    'name'       => 'L',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            4 =>
                [
                    'id'         => 5,
                    'name'       => 'XL',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            5 =>
                [
                    'id'         => 6,
                    'name'       => 'XXL',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
        ]);
    }
}
