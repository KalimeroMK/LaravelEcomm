<?php

declare(strict_types=1);

namespace Modules\Attribute\Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Models\Attribute;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AttributeControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var User $user */
        $user = User::factory()->create();
        $this->adminUser = $user;
        $this->adminUser->givePermissionTo(['attribute-list', 'attribute-create', 'attribute-update', 'attribute-delete']);
    }

    #[Test]
    public function it_can_list_attributes(): void
    {
        Attribute::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.attributes.index'));

        $response->assertStatus(200);
        $response->assertViewHas('attributes');
    }

    #[Test]
    public function it_can_create_an_attribute(): void
    {
        $attributeData = [
            'name' => 'Size',
            'code' => 'size',
            'type' => 'text',
            'display' => 'select',
            'is_required' => true,
            'is_filterable' => true,
            'is_configurable' => true,
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.attributes.store'), $attributeData);

        $response->assertRedirect();
        $this->assertDatabaseHas('attributes', [
            'name' => 'Size',
            'code' => 'size',
        ]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.attributes.store'), []);

        $response->assertSessionHasErrors(['name', 'code', 'type']);
    }

    #[Test]
    public function it_validates_unique_code(): void
    {
        Attribute::factory()->create(['code' => 'color']);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.attributes.store'), [
                'name' => 'Color',
                'code' => 'color',
                'type' => 'text',
            ]);

        $response->assertSessionHasErrors(['code']);
    }

    #[Test]
    public function it_can_update_an_attribute(): void
    {
        $attribute = Attribute::factory()->create([
            'name' => 'Old Name',
            'code' => 'test',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.attributes.update', $attribute), [
                'name' => 'New Name',
                'code' => 'test',
                'type' => 'text',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attributes', [
            'id' => $attribute->id,
            'name' => 'New Name',
        ]);
    }

    #[Test]
    public function it_can_delete_an_attribute(): void
    {
        $attribute = Attribute::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.attributes.destroy', $attribute));

        $response->assertRedirect();
        $this->assertDatabaseMissing('attributes', [
            'id' => $attribute->id,
        ]);
    }

    #[Test]
    public function it_can_show_create_form(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.attributes.create'));

        $response->assertStatus(200);
        $response->assertViewIs('attribute::create');
    }

    #[Test]
    public function it_can_show_edit_form(): void
    {
        $attribute = Attribute::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.attributes.edit', $attribute));

        $response->assertStatus(200);
        $response->assertViewIs('attribute::edit');
        $response->assertViewHas('attribute', $attribute);
    }

    #[Test]
    public function guests_cannot_access_attributes(): void
    {
        $response = $this->get(route('admin.attributes.index'));

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_displays_attribute_types_in_form(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.attributes.create'));

        $response->assertSee('text');
        $response->assertSee('select');
        $response->assertSee('boolean');
        $response->assertSee('date');
    }

    #[Test]
    public function it_displays_display_types_in_form(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.attributes.create'));

        $response->assertSee('input');
        $response->assertSee('select');
        $response->assertSee('color');
        $response->assertSee('checkbox');
    }
}
