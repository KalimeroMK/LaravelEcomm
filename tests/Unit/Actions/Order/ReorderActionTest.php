<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Modules\Cart\Models\Cart;
use Modules\Order\Actions\ReorderAction;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class ReorderActionTest extends ActionTestCase
{
    public function testExecuteSuccessfullyReordersItems(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'status' => 'active',
            'stock' => 10,
            'price' => 50.00,
        ]);

        // Create original order
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 2,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create cart item for the order
        \DB::table('carts')->insert([
            'id' => 1,
            'order_id' => 1,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 2,
            'price' => 50.00,
            'amount' => 100.00,
            'status' => 'progress',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $action = new ReorderAction();
        $result = $action->execute(1, $user->id);

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['added_items']);
        $this->assertSame(0, $result['skipped_items']);
        $this->assertStringContainsString('Added 1 item', $result['message']);

        // Verify cart was updated
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => null,
        ]);
    }

    public function testExecuteSkipsInactiveProducts(): void
    {
        $user = User::factory()->create();
        $inactiveProduct = Product::factory()->create([
            'status' => 'inactive',
            'stock' => 10,
            'price' => 50.00,
        ]);

        // Create original order
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 50,
            'total_amount' => 60,
            'quantity' => 1,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create cart item for the order
        \DB::table('carts')->insert([
            'id' => 1,
            'order_id' => 1,
            'product_id' => $inactiveProduct->id,
            'user_id' => $user->id,
            'quantity' => 1,
            'price' => 50.00,
            'amount' => 50.00,
            'status' => 'progress',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $action = new ReorderAction();
        $result = $action->execute(1, $user->id);

        $this->assertTrue($result['success']);
        $this->assertSame(0, $result['added_items']);
        $this->assertSame(1, $result['skipped_items']);
        $this->assertStringContainsString('No items could be added', $result['message']);
    }

    public function testExecuteSkipsOutOfStockProducts(): void
    {
        $user = User::factory()->create();
        $lowStockProduct = Product::factory()->create([
            'status' => 'active',
            'stock' => 1,
            'price' => 50.00,
        ]);

        // Create original order
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 2,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create cart item with quantity 2 but product only has stock 1
        \DB::table('carts')->insert([
            'id' => 1,
            'order_id' => 1,
            'product_id' => $lowStockProduct->id,
            'user_id' => $user->id,
            'quantity' => 2,
            'price' => 50.00,
            'amount' => 100.00,
            'status' => 'progress',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $action = new ReorderAction();
        $result = $action->execute(1, $user->id);

        $this->assertTrue($result['success']);
        $this->assertSame(0, $result['added_items']);
        $this->assertSame(1, $result['skipped_items']);
    }

    public function testExecuteUpdatesExistingCartItem(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'status' => 'active',
            'stock' => 100,
            'price' => 50.00,
        ]);

        // Create existing cart item
        \DB::table('carts')->insert([
            'id' => 100,
            'order_id' => null,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 1,
            'price' => 50.00,
            'amount' => 50.00,
            'status' => 'new',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create original order
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 2,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create cart item for the order
        \DB::table('carts')->insert([
            'id' => 101,
            'order_id' => 1,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 2,
            'price' => 50.00,
            'amount' => 100.00,
            'status' => 'progress',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $action = new ReorderAction();
        $result = $action->execute(1, $user->id);

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['added_items']);
        $this->assertSame(0, $result['skipped_items']);

        // Verify cart was updated with combined quantity (1 existing + 2 new = 3)
        $this->assertDatabaseHas('carts', [
            'id' => 100,
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => null,
            'quantity' => 3,
        ]);
    }

    public function testExecuteRejectsReorderForDifferentUser(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = Product::factory()->create(['status' => 'active']);

        // Create original order for user
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 1,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create cart item for the order
        \DB::table('carts')->insert([
            'id' => 1,
            'order_id' => 1,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'quantity' => 1,
            'price' => 100.00,
            'amount' => 100.00,
            'status' => 'progress',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $action = new ReorderAction();
        $result = $action->execute(1, $otherUser->id);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('do not have permission', $result['message']);
    }

    public function testExecuteHandlesMultipleItemsWithMixedAvailability(): void
    {
        $user = User::factory()->create();
        $activeProduct = Product::factory()->create([
            'status' => 'active',
            'stock' => 10,
            'price' => 50.00,
        ]);
        $inactiveProduct = Product::factory()->create([
            'status' => 'inactive',
            'stock' => 10,
            'price' => 30.00,
        ]);

        // Create original order
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 80,
            'total_amount' => 90,
            'quantity' => 2,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create cart items for the order
        \DB::table('carts')->insert([
            'id' => 1,
            'order_id' => 1,
            'product_id' => $activeProduct->id,
            'user_id' => $user->id,
            'quantity' => 1,
            'price' => 50.00,
            'amount' => 50.00,
            'status' => 'progress',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \DB::table('carts')->insert([
            'id' => 2,
            'order_id' => 1,
            'product_id' => $inactiveProduct->id,
            'user_id' => $user->id,
            'quantity' => 1,
            'price' => 30.00,
            'amount' => 30.00,
            'status' => 'progress',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $action = new ReorderAction();
        $result = $action->execute(1, $user->id);

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['added_items']);
        $this->assertSame(1, $result['skipped_items']);
    }
}
