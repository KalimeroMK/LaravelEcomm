<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Tenant;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Tenant\Actions\UpdateTenantAction;
use Modules\Tenant\DTOs\TenantDTO;
use Modules\Tenant\Models\Tenant;
use Tests\Unit\Actions\ActionTestCase;

class UpdateTenantActionTest extends ActionTestCase
{
    public function testExecuteUpdatesTenantSuccessfully(): void
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Old Name',
            'domain' => 'old.example.com',
        ]);

        $dto = new TenantDTO(
            id: $tenant->id,
            name: 'New Name',
            domain: 'new.example.com',
            database: $tenant->database
        );

        $action = app(UpdateTenantAction::class);
        $result = $action->execute($tenant->id, $dto);

        $this->assertEquals('New Name', $result->name);
        $this->assertEquals('new.example.com', $result->domain);
        $this->assertDatabaseHas('tenants', ['name' => 'New Name']);
    }

    public function testExecuteThrowsExceptionForNonExistentTenant(): void
    {
        $dto = new TenantDTO(
            id: 99999,
            name: 'Test',
            domain: 'test.example.com',
            database: 'tenant_test'
        );

        $action = app(UpdateTenantAction::class);

        $this->expectException(ModelNotFoundException::class);
        $action->execute(99999, $dto);
    }
}
