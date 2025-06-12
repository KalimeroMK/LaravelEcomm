<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;

class ProductComparisonController extends Controller
{
    public function addToCompare(int $productId, Request $request): RedirectResponse
    {
        $compare = session()->get('compare.products', []);
        if (! in_array($productId, $compare)) {
            $compare[] = $productId;
        }
        session(['compare.products' => array_slice($compare, -4)]); // keep last 4

        return back()->with('compare_added', $productId);
    }

    public function removeFromCompare(int $productId): RedirectResponse
    {
        $compare = session()->get('compare.products', []);
        $compare = array_diff($compare, [$productId]);
        session(['compare.products' => $compare]);

        return back()->with('compare_removed', $productId);
    }

    public function showComparison(): View|Factory|Application
    {
        $productIds = session('compare.products', []);
        $products = Product::with(['media', 'attributeValues.attribute'])
            ->whereIn('id', $productIds)
            ->get();
        $tooMany = count($productIds) > 4;

        return view('product::product.compare', [
            'products' => $products,
            'tooMany' => $tooMany,
        ]);
    }
}
