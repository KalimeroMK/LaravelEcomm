<?php

declare(strict_types=1);

namespace Modules\ProductStats\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Category\Models\Category;
use Modules\ProductStats\Actions\GetAllProductStatsAction;
use Modules\ProductStats\Actions\GetProductDetailStatsAction;

class ProductStatsController extends Controller
{
    public function __construct(
        private readonly GetAllProductStatsAction $getAllAction,
        private readonly GetProductDetailStatsAction $getDetailAction
    ) {}

    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $filters = [
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'category_id' => $request->input('category_id'),
        ];

        $statsListDto = $this->getAllAction->execute($filters);
        $categories = Category::all();

        return view('productstats::admin.index', [
            'statsListDto' => $statsListDto,
            'categories' => $categories,
            'from' => $filters['from'],
            'to' => $filters['to'],
            'categoryId' => $filters['category_id'],
        ]);
    }

    public function detail(Request $request, $id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $data = $this->getDetailAction->execute($id, $from, $to);

        return view('productstats::admin.detail', [
            'product' => $data['product'],
            'impressions' => $data['impressions'],
            'clicks' => $data['clicks'],
            'stats' => $data['stats'],
        ]);
    }
}
