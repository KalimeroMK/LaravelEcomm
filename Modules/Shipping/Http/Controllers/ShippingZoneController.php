<?php

declare(strict_types=1);

namespace Modules\Shipping\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Models\ShippingZone;

class ShippingZoneController extends Controller
{
    public function __construct()
    {
        // Authorization is handled per method
    }

    public function index(): View|Factory|Application
    {
        $this->authorize('viewAny', ShippingZone::class);

        $zones = ShippingZone::with('methods.shipping')
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->get();

        return view('shipping::zones.index', ['zones' => $zones]);
    }

    public function create(): View|Factory|Application
    {
        $this->authorize('create', ShippingZone::class);

        $shippingMethods = Shipping::where('status', 'active')->get();

        return view('shipping::zones.create', ['shippingMethods' => $shippingMethods]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', ShippingZone::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'countries' => 'nullable|array',
            'countries.*' => 'string|max:2',
            'regions' => 'nullable|array',
            'regions.*' => 'string|max:255',
            'postal_codes' => 'nullable|array',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'methods' => 'nullable|array',
            'methods.*.shipping_id' => 'required|exists:shipping,id',
            'methods.*.price' => 'required|numeric|min:0',
            'methods.*.free_shipping_threshold' => 'nullable|numeric|min:0',
            'methods.*.estimated_days' => 'nullable|integer|min:1',
            'methods.*.is_active' => 'boolean',
            'methods.*.priority' => 'integer|min:0',
        ]);

        $zone = ShippingZone::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'countries' => $validated['countries'] ?? null,
            'regions' => $validated['regions'] ?? null,
            'postal_codes' => $validated['postal_codes'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'priority' => $validated['priority'] ?? 0,
        ]);

        if (isset($validated['methods'])) {
            foreach ($validated['methods'] as $methodData) {
                $zone->methods()->create([
                    'shipping_id' => $methodData['shipping_id'],
                    'price' => $methodData['price'],
                    'free_shipping_threshold' => $methodData['free_shipping_threshold'] ?? null,
                    'estimated_days' => $methodData['estimated_days'] ?? null,
                    'is_active' => $methodData['is_active'] ?? true,
                    'priority' => $methodData['priority'] ?? 0,
                ]);
            }
        }

        return redirect()->route('shipping.zones.index')->with('success', 'Shipping zone created successfully');
    }

    public function edit(ShippingZone $zone): View|Factory|Application
    {
        $this->authorize('update', $zone);

        $zone->load('methods.shipping');
        $shippingMethods = Shipping::where('status', 'active')->get();

        return view('shipping::zones.edit', [
            'zone' => $zone,
            'shippingMethods' => $shippingMethods,
        ]);
    }

    public function update(Request $request, ShippingZone $zone): RedirectResponse
    {
        $this->authorize('update', $zone);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'countries' => 'nullable|string',
            'regions' => 'nullable|array',
            'regions.*' => 'string|max:255',
            'postal_codes' => 'nullable|array',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'methods' => 'nullable|array',
            'methods.*.id' => 'nullable|exists:shipping_zone_methods,id',
            'methods.*.shipping_id' => 'required|exists:shipping,id',
            'methods.*.price' => 'required|numeric|min:0',
            'methods.*.free_shipping_threshold' => 'nullable|numeric|min:0',
            'methods.*.estimated_days' => 'nullable|integer|min:1',
            'methods.*.is_active' => 'boolean',
            'methods.*.priority' => 'integer|min:0',
        ]);

        // Parse countries string to array
        $countries = null;
        if (! empty($validated['countries'])) {
            $countries = array_map('trim', explode(',', $validated['countries']));
        }

        $zone->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'countries' => $countries,
            'regions' => $validated['regions'] ?? null,
            'postal_codes' => $validated['postal_codes'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'priority' => $validated['priority'] ?? 0,
        ]);

        if (isset($validated['methods'])) {
            $existingIds = collect($validated['methods'])->pluck('id')->filter();
            $zone->methods()->whereNotIn('id', $existingIds)->delete();

            foreach ($validated['methods'] as $methodData) {
                if (isset($methodData['id'])) {
                    $zone->methods()->where('id', $methodData['id'])->update([
                        'shipping_id' => $methodData['shipping_id'],
                        'price' => $methodData['price'],
                        'free_shipping_threshold' => $methodData['free_shipping_threshold'] ?? null,
                        'estimated_days' => $methodData['estimated_days'] ?? null,
                        'is_active' => $methodData['is_active'] ?? true,
                        'priority' => $methodData['priority'] ?? 0,
                    ]);
                } else {
                    $zone->methods()->create([
                        'shipping_id' => $methodData['shipping_id'],
                        'price' => $methodData['price'],
                        'free_shipping_threshold' => $methodData['free_shipping_threshold'] ?? null,
                        'estimated_days' => $methodData['estimated_days'] ?? null,
                        'is_active' => $methodData['is_active'] ?? true,
                        'priority' => $methodData['priority'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('shipping.zones.index')->with('success', 'Shipping zone updated successfully');
    }

    public function destroy(ShippingZone $zone): RedirectResponse
    {
        $this->authorize('delete', $zone);
        $zone->delete();

        return redirect()->route('shipping.zones.index')->with('success', 'Shipping zone deleted successfully');
    }
}
