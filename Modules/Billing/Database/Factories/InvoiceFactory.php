<?php

declare(strict_types=1);

namespace Modules\Billing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Billing\Models\Invoice;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'invoice_number' => 'INV-'.mb_strtoupper($this->faker->unique()->bothify('##########')),
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['draft', 'sent', 'viewed', 'paid', 'overdue', 'cancelled']),
            'issue_date' => $this->faker->date(),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'paid_date' => $this->faker->optional()->date(),
            'subtotal' => $this->faker->randomFloat(2, 10, 1000),
            'tax_amount' => $this->faker->randomFloat(2, 0, 100),
            'discount_amount' => $this->faker->randomFloat(2, 0, 50),
            'total_amount' => $this->faker->randomFloat(2, 10, 1000),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
