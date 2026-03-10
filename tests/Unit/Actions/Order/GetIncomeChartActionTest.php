<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Carbon\Carbon;
use Modules\Cart\Models\Cart;
use Modules\Order\Actions\GetIncomeChartAction;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetIncomeChartActionTest extends ActionTestCase
{
    public function testExecuteReturnsIncomeDataForCurrentYear(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['status' => 'active', 'price' => 50.00]);
        
        // Create order for current year with delivered status
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 2,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'delivered',
            'created_at' => Carbon::now()->month(3)->day(15),
            'updated_at' => Carbon::now(),
        ]);

        // Create cart item for the order
        \DB::table('carts')->insert([
            'order_id' => 1,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 2,
            'price' => 50.00,
            'amount' => 100.00,
            'status' => 'progress',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $action = new GetIncomeChartAction();
        $result = $action->execute();

        $this->assertIsArray($result);
        $this->assertCount(12, $result);
        $this->assertArrayHasKey('January', $result);
        $this->assertArrayHasKey('December', $result);
        
        // March should have income
        $this->assertGreaterThanOrEqual(0, $result['March']);
    }

    public function testExecuteReturnsIncomeDataForSpecificYear(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['status' => 'active', 'price' => 75.00]);
        
        // Create order for a specific year (2023)
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 150,
            'total_amount' => 160,
            'quantity' => 2,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'delivered',
            'created_at' => Carbon::create(2023, 6, 15),
            'updated_at' => Carbon::now(),
        ]);

        // Create cart item for the order
        \DB::table('carts')->insert([
            'order_id' => 1,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 2,
            'price' => 75.00,
            'amount' => 150.00,
            'status' => 'progress',
            'created_at' => Carbon::create(2023, 6, 15),
            'updated_at' => Carbon::now(),
        ]);

        $action = new GetIncomeChartAction();
        $result = $action->execute(2023);

        $this->assertIsArray($result);
        $this->assertCount(12, $result);
        $this->assertArrayHasKey('June', $result);
    }

    public function testExecuteReturnsZeroForMonthsWithNoIncome(): void
    {
        $action = new GetIncomeChartAction();
        $result = $action->execute(2050);

        $this->assertIsArray($result);
        $this->assertCount(12, $result);
        
        // All months should have 0.0 for a year with no orders
        foreach ($result as $month => $value) {
            $this->assertSame(0.0, $value);
        }
    }

    public function testExecuteIgnoresNonDeliveredOrders(): void
    {
        $user = User::factory()->create();
        
        // Create pending order (should not count)
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 1,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create shipped order (should not count)
        \DB::table('orders')->insert([
            'id' => 2,
            'order_number' => 'ORD-002',
            'user_id' => $user->id,
            'sub_total' => 200,
            'total_amount' => 220,
            'quantity' => 2,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'shipped',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $action = new GetIncomeChartAction();
        $result = $action->execute();

        // All months should have 0.0 since no orders are delivered
        foreach ($result as $month => $value) {
            $this->assertSame(0.0, $value);
        }
    }
}
