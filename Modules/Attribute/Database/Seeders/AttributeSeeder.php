<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            1 => AttributeGroup::firstOrCreate(['id' => 1], ['name' => 'General']),
            2 => AttributeGroup::firstOrCreate(['id' => 2], ['name' => 'Dimensions']),
            3 => AttributeGroup::firstOrCreate(['id' => 3], ['name' => 'Specifications']),
            4 => AttributeGroup::firstOrCreate(['id' => 4], ['name' => 'Marketing Info']),
        ];

        $attributes = [
            [
                'name' => 'Color',
                'code' => 'color',
                'type' => 'select',
                'display' => 'color',  // Visual color swatches
                'is_filterable' => true,
                'is_configurable' => true,
            ],
            [
                'name' => 'Size',
                'code' => 'size',
                'type' => 'select',
                'display' => 'button',  // Button swatches
                'is_filterable' => true,
                'is_configurable' => true,
            ],
            [
                'name' => 'Material',
                'code' => 'material',
                'type' => 'select',
                'display' => 'select',
                'is_filterable' => true,
                'is_configurable' => false,
            ],
            [
                'name' => 'Brand',
                'code' => 'brand',
                'type' => 'string',
                'display' => 'input',
                'is_filterable' => true,
                'is_configurable' => false,
            ],
            [
                'name' => 'Weight',
                'code' => 'weight',
                'type' => 'decimal',
                'display' => 'input',
                'is_filterable' => true,
                'is_configurable' => false,
            ],
            [
                'name' => 'Length',
                'code' => 'length',
                'type' => 'decimal',
                'display' => 'input',
                'is_filterable' => false,
                'is_configurable' => false,
            ],
            [
                'name' => 'Width',
                'code' => 'width',
                'type' => 'decimal',
                'display' => 'input',
                'is_filterable' => false,
                'is_configurable' => false,
            ],
            [
                'name' => 'Height',
                'code' => 'height',
                'type' => 'decimal',
                'display' => 'input',
                'is_filterable' => false,
                'is_configurable' => false,
            ],
            [
                'name' => 'Is New',
                'code' => 'is_new',
                'type' => 'boolean',
                'display' => 'checkbox',
                'is_filterable' => true,
                'is_configurable' => false,
            ],
            [
                'name' => 'Release Date',
                'code' => 'release_date',
                'type' => 'date',
                'display' => 'input',
                'is_filterable' => false,
                'is_configurable' => false,
            ],
        ];

        foreach ($attributes as $attr) {
            Attribute::firstOrCreate([
                'code' => $attr['code'],
            ], array_merge($attr, [
                'is_required' => false,
            ]));
        }

        $this->command->info('Attributes seeded successfully!');
    }
}
