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

        $createdAttributes = [];
        foreach ($attributes as $attr) {
            // attribute_group_id is not in the array structure, so we don't check for it
            $attribute = Attribute::firstOrCreate([
                'code' => $attr['code'],
            ], array_merge($attr, [
                'is_required' => false,
                'is_filterable' => true,
            ]));
            $createdAttributes[] = $attribute;
        }
        // Note: Attributes are not automatically attached to groups in this seeder
        // They can be attached manually or via another seeder if needed
    }
}
