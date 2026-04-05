<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\Models\Order;
use Modules\Front\Actions\ProcessCheckoutAction;
use Modules\Product\Models\Product;
use Modules\Cart\Models\Cart;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class ProcessCheckoutActionTest extends ActionTestCase
{
    public function test_execute_redirects_to_front_index_on_cod_order(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['status' => 'active', 'price' => 50.00]);
        Cart::factory()->create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'price'      => 50.00,
            'quantity'   => 1,
            'order_id'   => null,
        ]);

        $request = \Modules\Order\Http\Requests\Store::create('/checkout', 'POST', [
            'first_name'     => 'John',
            'last_name'      => 'Doe',
            'email'          => 'john@example.com',
            'phone'          => '555-1234',
            'country'        => 'US',
            'city'           => 'New York',
            'address1'       => '123 Main St',
            'post_code'      => '10001',
            'payment_method' => 'cod',
        ]);
        $request->setUserResolver(fn () => $user);

        $action = app(ProcessCheckoutAction::class);
        $response = $action->execute($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_execute_redirects_to_payment_for_paypal(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['status' => 'active', 'price' => 80.00]);
        Cart::factory()->create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'price'      => 80.00,
            'quantity'   => 1,
            'order_id'   => null,
        ]);

        $request = \Modules\Order\Http\Requests\Store::create('/checkout', 'POST', [
            'first_name'     => 'Jane',
            'last_name'      => 'Doe',
            'email'          => 'jane@example.com',
            'phone'          => '555-5678',
            'country'        => 'US',
            'city'           => 'LA',
            'address1'       => '456 Other St',
            'post_code'      => '90001',
            'payment_method' => 'paypal',
        ]);
        $request->setUserResolver(fn () => $user);

        $action = app(ProcessCheckoutAction::class);
        $response = $action->execute($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertStringContainsString('payment', $response->getTargetUrl());
    }

    public function test_execute_redirects_back_when_cart_is_empty(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = \Modules\Order\Http\Requests\Store::create('/checkout', 'POST', [
            'first_name'     => 'Empty',
            'last_name'      => 'Cart',
            'email'          => 'empty@example.com',
            'phone'          => '555-0000',
            'country'        => 'US',
            'city'           => 'NYC',
            'address1'       => '789 Void St',
            'post_code'      => '00000',
            'payment_method' => 'cod',
        ]);
        $request->setUserResolver(fn () => $user);

        $action = app(ProcessCheckoutAction::class);
        $response = $action->execute($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(session()->get('error'), 'Your cart is empty.');
    }

    public function test_execute_creates_order_in_database(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['status' => 'active', 'price' => 100.00]);
        Cart::factory()->create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'price'      => 100.00,
            'quantity'   => 1,
            'order_id'   => null,
        ]);

        $request = \Modules\Order\Http\Requests\Store::create('/checkout', 'POST', [
            'first_name'     => 'Test',
            'last_name'      => 'User',
            'email'          => 'test@example.com',
            'phone'          => '555-9999',
            'country'        => 'US',
            'city'           => 'Boston',
            'address1'       => '1 Test Ave',
            'post_code'      => '02101',
            'payment_method' => 'cod',
        ]);
        $request->setUserResolver(fn () => $user);

        $ordersBefore = Order::count();

        $action = app(ProcessCheckoutAction::class);
        $action->execute($request);

        $this->assertEquals($ordersBefore + 1, Order::count());
    }
}
