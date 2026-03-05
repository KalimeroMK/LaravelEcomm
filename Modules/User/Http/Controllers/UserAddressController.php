<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\User\Models\UserAddress;

class UserAddressController extends CoreController
{
    /**
     * Display user's addresses.
     */
    public function index(): View
    {
        $user = auth()->user();
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        
        return view('user::addresses.index', compact('addresses'));
    }

    /**
     * Show form to create new address.
     */
    public function create(): View
    {
        return view('user::addresses.create');
    }

    /**
     * Store a new address.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:shipping,billing,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'post_code' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'is_default' => 'boolean',
        ]);

        $user = auth()->user();
        
        $address = $user->addresses()->create([
            ...$validated,
            'is_default' => $request->boolean('is_default'),
        ]);

        if ($address->is_default) {
            $address->setAsDefault();
        }

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address added successfully.');
    }

    /**
     * Show form to edit address.
     */
    public function edit(UserAddress $address): View
    {
        $this->authorize('update', $address);
        
        return view('user::addresses.edit', compact('address'));
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, UserAddress $address): RedirectResponse
    {
        $this->authorize('update', $address);
        
        $validated = $request->validate([
            'type' => 'required|in:shipping,billing,both',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'post_code' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'is_default' => 'boolean',
        ]);

        $address->update([
            ...$validated,
            'is_default' => $request->boolean('is_default'),
        ]);

        if ($address->is_default) {
            $address->setAsDefault();
        }

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address updated successfully.');
    }

    /**
     * Remove the specified address.
     */
    public function destroy(UserAddress $address): RedirectResponse
    {
        $this->authorize('delete', $address);
        
        $address->delete();

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address deleted successfully.');
    }

    /**
     * Set address as default.
     */
    public function setDefault(UserAddress $address): RedirectResponse
    {
        $this->authorize('update', $address);
        
        $address->setAsDefault();

        return redirect()->route('user.addresses.index')
            ->with('success', 'Default address updated.');
    }
}
