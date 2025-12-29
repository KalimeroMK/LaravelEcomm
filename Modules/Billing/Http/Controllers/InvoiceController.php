<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Modules\Billing\Actions\Invoice\CreateInvoiceAction;
use Modules\Billing\Actions\Invoice\GetAllInvoicesAction;
use Modules\Billing\Actions\Invoice\UpdateInvoiceAction;
use Modules\Billing\DTOs\InvoiceDTO;
use Modules\Billing\Http\Requests\Invoice\Store;
use Modules\Billing\Http\Requests\Invoice\Update;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Repository\InvoiceRepository;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly CreateInvoiceAction $createAction,
        private readonly UpdateInvoiceAction $updateAction,
        private readonly GetAllInvoicesAction $getAllAction,
        private readonly InvoiceRepository $repository
    ) {
        // Policy authorization is done per method
    }

    public function index(): Factory|View|Application
    {
        $this->authorize('viewAny', Invoice::class);

        $userId = auth()->user()->hasAnyRole(['admin', 'super-admin']) ? null : auth()->id();
        $invoices = $this->getAllAction->execute($userId);

        return view('billing::invoices.index', ['invoices' => $invoices]);
    }

    public function create(): View|Factory|Application
    {
        $this->authorize('create', Invoice::class);

        return view('billing::invoices.create', ['invoice' => new Invoice]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        $dto = InvoiceDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('invoices.index')->with('success', __('messages.created_successfully'));
    }

    public function show(Invoice $invoice): View|Factory|Application
    {
        $this->authorize('view', $invoice);

        $invoice->load(['user', 'order', 'payments']);

        return view('billing::invoices.show', ['invoice' => $invoice]);
    }

    public function edit(Invoice $invoice): View|Factory|Application
    {
        $this->authorize('update', $invoice);

        return view('billing::invoices.edit', ['invoice' => $invoice]);
    }

    public function update(Update $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        $dto = InvoiceDTO::fromRequest($request, $invoice->id, $invoice);
        $this->updateAction->execute($dto);

        return redirect()->route('invoices.edit', $invoice)->with('success', __('messages.updated_successfully'));
    }

    public function download(Invoice $invoice): Response
    {
        // TODO: Implement PDF generation
        // For now, return a simple response
        return response()->view('billing::invoices.pdf', ['invoice' => $invoice])
            ->header('Content-Type', 'application/pdf');
    }
}
