<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\Admin\Service\AdminService;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Message\Models\Message;

class AdminController extends CoreController
{
    private AdminService $admin_service;

    public function __construct(AdminService $admin_service)
    {
        $this->admin_service = $admin_service;
    }

    public function index(): View|Factory|Application
    {
        $data = $this->admin_service->index();
        $paidOrdersByMonth = $this->admin_service->OrdersByMonth();

        return view('admin::index', ['paidOrdersByMonth' => $paidOrdersByMonth, 'data' => $data]);
    }

    public function messageFive(): JsonResponse
    {
        $message = Message::whereNull('read_at')->limit(5)->get();

        return response()->json($message);
    }
}
