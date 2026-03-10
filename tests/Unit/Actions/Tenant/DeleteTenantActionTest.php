<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Tenant;

use Modules\Tenant\Actions\DeleteTenantAction;
use Modules\Tenant\Models\Tenant;
use Tests\Unit\Actions\ActionTestCase;

class DeleteTenantActionTest extends ActionTestCase
{
    public function testExecuteDeletesTenantSuccessfully(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);

        $action = app(DeleteTenantAction::class);
        $result = $action->execute($tenant->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }

    public function testExecuteThrowsExceptionForNonExistentTenant(): void
    {
        $action = app(DeleteTenantAction::class);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute(99999);
    }
}
