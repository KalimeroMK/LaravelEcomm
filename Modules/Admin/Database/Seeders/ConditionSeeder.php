<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('conditions')->insert([
            0 => [
                'id' => 1,
                'status' => 'New',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            1 => [
                'id' => 2,
                'status' => 'Used',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
