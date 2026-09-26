<?php

declare(strict_types=1);

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'symbol_position' => 'left', 'exchange_rate' => 1.0, 'decimals' => 2, 'is_default' => true, 'is_active' => true, 'sort_order' => 1],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'symbol_position' => 'left', 'exchange_rate' => 0.92, 'decimals' => 2, 'is_default' => false, 'is_active' => true, 'sort_order' => 2],
            ['code' => 'MKD', 'name' => 'Македонски денар', 'symbol' => 'ден', 'symbol_position' => 'right', 'exchange_rate' => 56.60, 'decimals' => 0, 'is_default' => false, 'is_active' => true, 'sort_order' => 3],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'symbol_position' => 'left', 'exchange_rate' => 0.79, 'decimals' => 2, 'is_default' => false, 'is_active' => true, 'sort_order' => 4],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(['code' => $currency['code']], $currency);
        }
    }
}
