<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;

class AttributeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        foreach (Attribute::TYPES as $type) {
            // Randomly pick a display type from the DISPLAYS array
            $randomDisplay = Attribute::DISPLAYS[array_rand(Attribute::DISPLAYS)];

            Attribute::create([
                'name' => ucfirst($type), // Capitalize the first letter for the name
                'code' => strtolower($type), // Use lowercase for the code
                'type' => $type,
                'display' => $randomDisplay, // Use the randomly selected display type
                'filterable' => true,
                'configurable' => true,
            ]);
        }
    }
}
