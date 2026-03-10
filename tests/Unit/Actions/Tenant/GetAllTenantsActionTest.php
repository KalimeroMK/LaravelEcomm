<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Tenant;

use Modules\Tenant\Actions\GetAllTenantsAction;
use Modules\Tenant\Models\Tenant;
use Tests\Unit\Actions\ActionTestCase;

class GetAllTenantsActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllTenants(): void
    {
        Tenant::factory()->count(3)->create();

        $action = app(GetAllTenantsAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoTenants(): void
    {
        $action = app(GetAllTenantsAction::class);
        $result = $action->execute();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsCollectionOfTenants(): void
    {
        $tenant = Tenant::factory()->create();

        $action = app(GetAllTenantsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Tenant::class, $result->first());
        $this->assertEquals($tenant->id, $result->first()->id);
    }
}
