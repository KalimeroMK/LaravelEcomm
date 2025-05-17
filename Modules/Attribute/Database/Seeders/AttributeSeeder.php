<?php

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $group = AttributeGroup::firstOrCreate(['name' => 'General']);

        $attributes = [
            ['name' => 'Color', 'code' => 'color', 'type' => 'text', 'display' => 'input'],
            ['name' => 'Size', 'code' => 'size', 'type' => 'select', 'display' => 'select'],
            ['name' => 'Material', 'code' => 'material', 'type' => 'text', 'display' => 'input'],
            ['name' => 'Brand', 'code' => 'brand', 'type' => 'text', 'display' => 'input'],
            ['name' => 'Weight', 'code' => 'weight', 'type' => 'float', 'display' => 'input'],
            ['name' => 'Length', 'code' => 'length', 'type' => 'float', 'display' => 'input'],
            ['name' => 'Width', 'code' => 'width', 'type' => 'float', 'display' => 'input'],
            ['name' => 'Height', 'code' => 'height', 'type' => 'float', 'display' => 'input'],
            ['name' => 'Is New', 'code' => 'is_new', 'type' => 'boolean', 'display' => 'checkbox'],
            ['name' => 'Release Date', 'code' => 'release_date', 'type' => 'date', 'display' => 'input'],
        ];

        foreach ($attributes as $attr) {
            Attribute::firstOrCreate([
                'code' => $attr['code'],
            ], array_merge($attr, [
                'attribute_group_id' => $group->id,
                'is_required' => false,
                'is_filterable' => true,
            ]));
        }
    }
}
