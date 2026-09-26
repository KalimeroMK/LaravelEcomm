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

        // Signups over the last 21 months (relative to today so the
        // analytics charts always have data), gently trending upward.
        $months = $this->monthlyCounts(21, 15, 85);

        foreach ($months as $month => $count) {
            for ($i = 0; $i < $count; $i++) {
                $user = new User;
                $user->name = fake()->name();
                $user->email = 'user'.microtime(true).rand(1000, 9999).'@demo.com';
                $user->email_verified_at = now();
                $user->password = bcrypt('password');
                $user->created_at = $this->randomDayInMonth($month);
                $user->updated_at = $user->created_at;
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

        // Orders over the last 21 months (relative to today), trending upward.
        $months = $this->monthlyCounts(21, 8, 95);

        foreach ($months as $month => $count) {
            for ($i = 0; $i < $count; $i++) {
                $user = $users->random();
                $orderDate = $this->randomDayInMonth($month);

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

    /**
     * Month-keyed counts for the last N months (oldest first), linearly
     * growing from $from to $to with a little jitter - always relative to
     * today so charts show a living trend regardless of when you seed.
     *
     * @return array<string, int>
     */
    private function monthlyCounts(int $monthsBack, int $from, int $to): array
    {
        $counts = [];

        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $progress = ($monthsBack - 1 - $i) / max(1, $monthsBack - 1);
            $base = (int) round($from + ($to - $from) * $progress);
            $counts[$month] = max(1, $base + rand(-3, 3));
        }

        return $counts;
    }

    /**
     * A random moment in the given Y-m month, never in the future.
     */
    private function randomDayInMonth(string $month): Carbon
    {
        $maxDay = 28;

        if ($month === now()->format('Y-m')) {
            $maxDay = min(28, (int) now()->format('d'));
        }

        return Carbon::parse($month.'-'.rand(1, $maxDay))
            ->setTime(rand(8, 21), rand(0, 59), rand(0, 59));
    }
}
