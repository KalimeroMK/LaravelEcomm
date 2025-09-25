<?php

declare(strict_types=1);

namespace Modules\ProductStats\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Modules\ProductStats\Actions\GetProductStatsAction;
use Modules\ProductStats\Repository\ProductStatsRepository;

class ProductStatsController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $filters = [
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'category_id' => $request->input('category_id'),
        ];
        $repo = new ProductStatsRepository;
        $statsListDto = $repo->getProductStats($filters);
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
        $product = Product::findOrFail($id);
        $from = $request->input('from');
        $to = $request->input('to');
        $impressionsQuery = $product->impressions();
        $clicksQuery = $product->clicks();
        if ($from) {
            $impressionsQuery->whereDate('created_at', '>=', $from);
            $clicksQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $impressionsQuery->whereDate('created_at', '<=', $to);
            $clicksQuery->whereDate('created_at', '<=', $to);
        }
        $impressions = $impressionsQuery->orderByDesc('created_at')->limit(30)->get();
        $clicks = $clicksQuery->orderByDesc('created_at')->limit(30)->get();
        $statsAction = new GetProductStatsAction;
        $stats = $statsAction->execute($product->id);

        return view('productstats::admin.detail', ['product' => $product, 'impressions' => $impressions, 'clicks' => $clicks, 'stats' => $stats]);
    }
}
