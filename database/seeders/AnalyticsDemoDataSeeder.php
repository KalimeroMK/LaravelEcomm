<?php

declare(strict_types=1);

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Brand\Models\Brand;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

class AnalyticsDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating demo data for Analytics Dashboard...');

        // Create demo users
        $this->createDemoUsers();

        // Create demo products
        $this->createDemoProducts();

        // Create demo orders with different dates
        $this->createDemoOrders();

        // Create demo brands
        $this->createDemoCategoriesAndBrands();

        $this->command->info('Demo data created successfully!');
    }

    private function createDemoUsers()
    {
        $this->command->info('Creating demo users...');

        // Create users for different months
        $months = [
            '2024-01' => 15,
            '2024-02' => 22,
            '2024-03' => 18,
            '2024-04' => 25,
            '2024-05' => 30,
            '2024-06' => 28,
            '2024-07' => 35,
            '2024-08' => 32,
            '2024-09' => 40,
            '2024-10' => 45,
            '2024-11' => 38,
            '2024-12' => 50,
            '2025-01' => 42,
            '2025-02' => 48,
            '2025-03' => 55,
            '2025-04' => 60,
            '2025-05' => 65,
            '2025-06' => 70,
            '2025-07' => 75,
            '2025-08' => 80,
            '2025-09' => 85,
        ];

        foreach ($months as $month => $count) {
            for ($i = 0; $i < $count; $i++) {
                $user = new User;
                $user->name = fake()->name();
                $user->email = 'user'.microtime(true).rand(1000, 9999).'@demo.com';
                $user->email_verified_at = now();
                $user->password = bcrypt('password');
                $user->created_at = Carbon::parse($month.'-'.rand(1, 28));
                $user->updated_at = Carbon::parse($month.'-'.rand(1, 28));
                $user->save();
            }
        }
    }

    private function createDemoProducts()
    {
        $this->command->info('Creating demo products...');

        $brands = Brand::all();

        if ($brands->isEmpty()) {
            $this->command->warn('No brands found. Creating basic ones...');
            $this->createBasicCategoriesAndBrands();
            $brands = Brand::all();
        }

        for ($i = 0; $i < 50; $i++) {
            $product = new Product;
            $product->title = fake()->words(3, true);
            $product->slug = fake()->slug();
            $product->summary = fake()->paragraph();
            $product->description = fake()->paragraphs(3, true);
            $product->price = fake()->randomFloat(2, 10, 1000);
            $product->special_price = fake()->randomFloat(2, 5, 500);
            $product->sku = 'DEMO-'.microtime(true).rand(1000, 9999);
            $product->stock = fake()->numberBetween(0, 100);
            $product->status = fake()->randomElement(['active', 'inactive']);
            $product->is_featured = fake()->boolean(30);
            $product->brand_id = $brands->random()->id;
            $product->created_at = fake()->dateTimeBetween('-2 years', 'now');
            $product->updated_at = fake()->dateTimeBetween('-2 years', 'now');
            $product->save();
        }
    }

    private function createDemoOrders()
    {
        $this->command->info('Creating demo orders...');

        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No users or products found. Skipping orders...');

            return;
        }

        // Create orders for different months
        $months = [
            '2024-01' => 8,
            '2024-02' => 12,
            '2024-03' => 15,
            '2024-04' => 18,
            '2024-05' => 22,
            '2024-06' => 25,
            '2024-07' => 28,
            '2024-08' => 30,
            '2024-09' => 35,
            '2024-10' => 40,
            '2024-11' => 45,
            '2024-12' => 50,
            '2025-01' => 55,
            '2025-02' => 60,
            '2025-03' => 65,
            '2025-04' => 70,
            '2025-05' => 75,
            '2025-06' => 80,
            '2025-07' => 85,
            '2025-08' => 90,
            '2025-09' => 95,
        ];

        foreach ($months as $month => $count) {
            for ($i = 0; $i < $count; $i++) {
                $user = $users->random();
                $orderDate = Carbon::parse($month.'-'.rand(1, 28));

                $order = new Order;
                $order->user_id = $user->id;
                $order->order_number = 'ORD-'.microtime(true).rand(1000, 9999);
                $order->status = fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']);
                $order->payment_status = fake()->randomElement(['pending', 'paid', 'unpaid']);
                $order->payment_method = fake()->randomElement(['cod', 'paypal', 'stripe']);
                $order->sub_total = 0;
                $order->total_amount = 0;
                $order->quantity = 0;
                // $order->notes = fake()->optional()->sentence(); // Notes column doesn't exist
                $order->created_at = $orderDate;
                $order->updated_at = $orderDate;
                $order->save();

                // Generate random order totals
                $subtotal = fake()->randomFloat(2, 50, 1000);
                $totalAmount = $subtotal;
                $totalQuantity = fake()->numberBetween(1, 10);

                // Update order totals
                $order->update([
                    'sub_total' => $subtotal,
                    'total_amount' => $totalAmount,
                    'quantity' => $totalQuantity,
                ]);
            }
        }
    }

    private function createDemoCategoriesAndBrands()
    {
        $this->command->info('Creating demo brands...');

        // Create brands
        $brands = [
            'TechCorp',
            'FashionHub',
            'HomeStyle',
            'SportMax',
            'BookWorld',
            'BeautyPlus',
            'ToyLand',
            'AutoPro',
            'FoodFresh',
            'OfficeMax',
        ];

        foreach ($brands as $brand) {
            $br = new Brand;
            $br->title = $brand;
            $br->slug = mb_strtolower(str_replace(' ', '-', $brand));
            $br->status = 'active';
            $br->save();
        }
    }

    private function createBasicCategoriesAndBrands()
    {
        // Create at least one brand if none exist
        if (Brand::count() === 0) {
            $br = new Brand;
            $br->title = 'Generic';
            $br->slug = 'generic';
            $br->status = 'active';
            $br->save();
        }
    }
}
