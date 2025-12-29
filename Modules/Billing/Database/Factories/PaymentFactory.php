<?php

declare(strict_types=1);

namespace Modules\Billing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Billing\Models\Payment;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'invoice_id' => null,
            'user_id' => User::factory(),
            'payment_method' => $this->faker->randomElement(['cod', 'paypal', 'stripe', 'bank_transfer']),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'failed', 'refunded']),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'USD',
            'transaction_id' => $this->faker->optional()->uuid(),
            'transaction_reference' => $this->faker->optional()->bothify('REF-##########'),
            'notes' => $this->faker->optional()->sentence(),
            'metadata' => null,
            'processed_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
