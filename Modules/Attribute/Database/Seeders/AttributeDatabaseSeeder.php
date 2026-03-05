<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class AttributeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds in order:
     * 1. Attribute Groups - base grouping structure
     * 2. Attributes - actual attributes (color, size, etc.)
     * 3. Attribute Options - specific values for select-type attributes
     * 4. Attribute Families - grouping attributes for categories
     * 5. Attribute Values - actual values on products/bundles/categories
     * 6. System Seeder - any system-specific setup
     */
    public function run(): void
    {
        Model::unguard();

        $this->command->info('Starting Attribute module seeding...');

        $this->call([
            AttributeGroupSeeder::class,
            AttributeSeeder::class,
            AttributeOptionSeeder::class,
            AttributeFamilySeeder::class,
            AttributeValueSeeder::class,
            AttributeSystemSeeder::class,
        ]);

        Model::reguard();

        $this->command->info('Attribute module seeding completed successfully!');
    }
}
