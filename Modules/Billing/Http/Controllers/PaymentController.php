<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\View\View;
use Modules\Billing\Actions\Payment\GetPaymentAnalyticsAction;
use Modules\Billing\Actions\Payment\GetUserPaymentsAction;
use Modules\Billing\Models\Payment;
use Modules\Billing\Repository\PaymentRepository;

class PaymentController extends Controller
{
    public function __construct(
        private readonly GetUserPaymentsAction $getUserPaymentsAction,
        private readonly GetPaymentAnalyticsAction $getAnalyticsAction,
        private readonly PaymentRepository $repository
    ) {}

    public function history(): Factory|View|Application
    {
        $userId = auth()->id();
        $payments = $this->getUserPaymentsAction->execute($userId);

        return view('billing::payments.history', ['payments' => $payments]);
    }

    public function analytics(): Factory|View|Application
    {
        $this->authorize('viewAny', Payment::class);

        $analytics = $this->getAnalyticsAction->execute();

        return view('billing::payments.analytics', ['analytics' => $analytics]);
    }
}
