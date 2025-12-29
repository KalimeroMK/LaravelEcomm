<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\View\View;
use Modules\Newsletter\Services\NewsletterService;

class NewsletterAnalyticsController extends Controller
{
    public function __construct(
        private readonly NewsletterService $newsletterService
    ) {}

    public function index(): View|Factory|Application
    {
        $analytics = $this->newsletterService->getNewsletterAnalytics('30_days');
        $subscriberStats = $this->newsletterService->getSubscriberStats();

        return view('newsletter::analytics.index', [
            'analytics' => $analytics,
            'subscriberStats' => $subscriberStats,
        ]);
    }
}
