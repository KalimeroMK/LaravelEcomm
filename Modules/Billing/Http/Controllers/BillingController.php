<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\View\View;
use Modules\Billing\Actions\Invoice\GetAllInvoicesAction;
use Modules\Billing\Actions\Payment\GetUserPaymentsAction;
use Modules\Billing\Repository\InvoiceRepository;
use Modules\Billing\Repository\PaymentRepository;

class BillingController extends Controller
{
    public function __construct(
        private readonly GetAllInvoicesAction $getAllInvoicesAction,
        private readonly GetUserPaymentsAction $getUserPaymentsAction,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly PaymentRepository $paymentRepository
    ) {}

    public function history(): Factory|View|Application
    {
        $userId = auth()->id();
        $invoices = $this->getAllInvoicesAction->execute($userId);
        $payments = $this->getUserPaymentsAction->execute($userId);

        return view('billing::history', [
            'invoices' => $invoices,
            'payments' => $payments,
        ]);
    }
}
