<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Tenant;

use Modules\Tenant\Actions\CreateTenantAction;
use Modules\Tenant\DTOs\TenantDTO;
use Modules\Tenant\Models\Tenant;
use Tests\Unit\Actions\ActionTestCase;

class CreateTenantActionTest extends ActionTestCase
{
    public function testExecuteCreatesTenantSuccessfully(): void
    {
        $dto = new TenantDTO(
            id: null,
            name: 'Test Tenant',
            domain: 'test.example.com',
            database: 'tenant_test_db'
        );

        $action = app(CreateTenantAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Tenant::class, $result);
        $this->assertEquals('Test Tenant', $result->name);
        $this->assertEquals('test.example.com', $result->domain);
        $this->assertEquals('tenant_test_db', $result->database);
        $this->assertDatabaseHas('tenants', ['name' => 'Test Tenant']);
    }

    public function testExecuteCreatesTenantWithMinimumData(): void
    {
        $dto = new TenantDTO(
            id: null,
            name: 'Minimal Tenant',
            domain: 'minimal.example.com',
            database: 'tenant_minimal'
        );

        $action = app(CreateTenantAction::class);
        $result = $action->execute($dto);

        $this->assertNotNull($result->id);
        $this->assertEquals('Minimal Tenant', $result->name);
    }
}
