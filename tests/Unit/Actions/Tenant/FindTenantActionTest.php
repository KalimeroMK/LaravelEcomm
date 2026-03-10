<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Tenant;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Tenant\Actions\FindTenantAction;
use Modules\Tenant\Models\Tenant;
use Tests\Unit\Actions\ActionTestCase;

class FindTenantActionTest extends ActionTestCase
{
    public function testExecuteFindsTenantById(): void
    {
        $tenant = Tenant::factory()->create();

        $action = app(FindTenantAction::class);
        $result = $action->execute($tenant->id);

        $this->assertInstanceOf(Tenant::class, $result);
        $this->assertEquals($tenant->id, $result->id);
        $this->assertEquals($tenant->name, $result->name);
    }

    public function testExecuteThrowsExceptionForNonExistentTenant(): void
    {
        $action = app(FindTenantAction::class);

        $this->expectException(ModelNotFoundException::class);
        $action->execute(99999);
    }
}
