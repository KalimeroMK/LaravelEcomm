<?php

declare(strict_types=1);

namespace Modules\Attribute\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeFamily;
use Modules\Attribute\Models\AttributeGroup;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AttributeFamilyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_an_attribute_family(): void
    {
        $family = AttributeFamily::create([
            'name' => 'Clothing',
            'code' => 'clothing',
            'description' => 'Attributes for clothing items',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('attribute_families', [
            'name' => 'Clothing',
            'code' => 'clothing',
        ]);
    }

    #[Test]
    public function it_can_have_attributes(): void
    {
        $family = AttributeFamily::factory()->create();
        $attribute = Attribute::factory()->create();
        $group = AttributeGroup::factory()->create();

        $family->attributes()->attach($attribute->id, [
            'attribute_group_id' => $group->id,
            'position' => 10,
            'is_required' => true,
        ]);

        $this->assertCount(1, $family->fresh()->attributes);
    }

    #[Test]
    public function it_returns_attributes_ordered_by_position(): void
    {
        $family = AttributeFamily::factory()->create();
        $group = AttributeGroup::factory()->create();

        $attr1 = Attribute::factory()->create(['name' => 'First']);
        $attr2 = Attribute::factory()->create(['name' => 'Second']);

        $family->attributes()->attach($attr2->id, [
            'attribute_group_id' => $group->id,
            'position' => 20,
        ]);

        $family->attributes()->attach($attr1->id, [
            'attribute_group_id' => $group->id,
            'position' => 10,
        ]);

        $attributes = $family->fresh()->attributes;

        $this->assertEquals('First', $attributes->first()->name);
        $this->assertEquals('Second', $attributes->last()->name);
    }

    #[Test]
    public function it_can_get_attributes_by_group(): void
    {
        $family = AttributeFamily::factory()->create();
        $generalGroup = AttributeGroup::factory()->create(['name' => 'General']);
        $specsGroup = AttributeGroup::factory()->create(['name' => 'Specifications']);

        $attr1 = Attribute::factory()->create();
        $attr2 = Attribute::factory()->create();

        $family->attributes()->attach($attr1->id, [
            'attribute_group_id' => $generalGroup->id,
        ]);

        $family->attributes()->attach($attr2->id, [
            'attribute_group_id' => $specsGroup->id,
        ]);

        $generalAttributes = $family->attributesByGroup($generalGroup);

        $this->assertCount(1, $generalAttributes);
        $this->assertEquals($attr1->id, $generalAttributes->first()->id);
    }

    #[Test]
    public function it_can_check_if_attribute_is_required(): void
    {
        $family = AttributeFamily::factory()->create();
        $group = AttributeGroup::factory()->create();
        $attribute = Attribute::factory()->create();

        $family->attributes()->attach($attribute->id, [
            'attribute_group_id' => $group->id,
            'is_required' => true,
        ]);

        $this->assertTrue($family->isAttributeRequired($attribute));
    }

    #[Test]
    public function it_has_scope_for_active_families(): void
    {
        AttributeFamily::factory()->create([
            'code' => 'active',
            'is_active' => true,
        ]);

        AttributeFamily::factory()->create([
            'code' => 'inactive',
            'is_active' => false,
        ]);

        $activeFamilies = AttributeFamily::active()->get();

        $this->assertCount(1, $activeFamilies);
        $firstFamily = $activeFamilies->first();
        $this->assertNotNull($firstFamily);
        $this->assertEquals('active', $firstFamily->code);
    }

    #[Test]
    public function it_returns_default_family_when_no_active(): void
    {
        // Clear any existing families
        AttributeFamily::query()->delete();

        $default = AttributeFamily::getDefault();

        $this->assertInstanceOf(AttributeFamily::class, $default);
        $this->assertEquals('default', $default->code);
        $this->assertDatabaseHas('attribute_families', ['code' => 'default']);
    }

    #[Test]
    public function it_returns_first_active_as_default(): void
    {
        AttributeFamily::factory()->create([
            'code' => 'first',
            'is_active' => true,
        ]);

        AttributeFamily::factory()->create([
            'code' => 'second',
            'is_active' => true,
        ]);

        $default = AttributeFamily::getDefault();

        $this->assertEquals('first', $default->code);
    }

    #[Test]
    public function it_can_get_groups_with_attributes(): void
    {
        $family = AttributeFamily::factory()->create();
        $group = AttributeGroup::factory()->create(['name' => 'General']);
        $attribute = Attribute::factory()->create();

        $family->attributes()->attach($attribute->id, [
            'attribute_group_id' => $group->id,
        ]);

        $groupsWithAttrs = $family->groupsWithAttributes();

        $this->assertCount(1, $groupsWithAttrs);
        $this->assertEquals('General', $groupsWithAttrs->first()->name);
        $this->assertCount(1, $groupsWithAttrs->first()->attributes);
    }
}
