<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers;

use Closure;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Actions\AddProductToComparisonAction;
use Modules\Product\Actions\GetProductComparisonAction;
use Modules\Product\Actions\RemoveProductFromComparisonAction;

class ProductComparisonController extends Controller
{
    public function addToCompare(int $productId, Request $request): RedirectResponse
    {
        $action = new AddProductToComparisonAction(
            $this->getStorageClosure(),
            $this->putStorageClosure()
        );

        $result = $action->execute($productId);

        return back()->with('compare_added', $productId);
    }

    public function removeFromCompare(int $productId): RedirectResponse
    {
        $action = new RemoveProductFromComparisonAction(
            $this->getStorageClosure(),
            $this->putStorageClosure()
        );

        $action->execute($productId);

        return back()->with('compare_removed', $productId);
    }

    public function showComparison(): View|Factory|Application
    {
        $action = new GetProductComparisonAction($this->getStorageClosure());
        $products = $action->execute();

        $productIds = session($this->getStorageKey(), []);
        $tooMany = count($productIds) > 4;

        return view('product::product.compare', [
            'products' => $products,
            'tooMany' => $tooMany,
        ]);
    }

    private function getStorageKey(): string
    {
        return 'compare.products';
    }

    private function getStorageClosure(): Closure
    {
        return fn () => session()->get($this->getStorageKey(), []);
    }

    private function putStorageClosure(): Closure
    {
        return function (array $compare): void {
            session([$this->getStorageKey() => $compare]);
        };
    }

    private function clearStorageClosure(): Closure
    {
        return fn () => session()->forget($this->getStorageKey());
    }
}
