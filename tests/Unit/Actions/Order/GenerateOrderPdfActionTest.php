<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Order;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PDFClass;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Order\Actions\GenerateOrderPdfAction;
use Modules\Order\Models\Order;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GenerateOrderPdfActionTest extends ActionTestCase
{
    public function testExecuteGeneratesPdfForExistingOrder(): void
    {
        $user = User::factory()->create();
        
        // Create order using direct DB insert to avoid FK constraints
        \DB::table('orders')->insert([
            'id' => 1,
            'order_number' => 'ORD-001',
            'user_id' => $user->id,
            'sub_total' => 100,
            'total_amount' => 110,
            'quantity' => 1,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mock PDF facade
        $pdfMock = $this->createMock(PDFClass::class);
        Pdf::shouldReceive('loadView')
            ->once()
            ->with('order::pdf', \Mockery::on(function ($data) {
                return $data['order'] instanceof Order;
            }))
            ->andReturn($pdfMock);

        $action = new GenerateOrderPdfAction();
        $result = $action->execute(1);

        $this->assertSame($pdfMock, $result);
    }

    public function testExecuteThrowsExceptionForNonExistentOrder(): void
    {
        $action = new GenerateOrderPdfAction();

        $this->expectException(ModelNotFoundException::class);
        $action->execute(99999);
    }
}
