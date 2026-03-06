<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\User\Http\Resources\UserAddressResource;
use Modules\User\Models\UserAddress;

class UserAddressController extends CoreController
{
    /**
     * Display a listing of the user's addresses.
     */
    public function index(): ResourceCollection
    {
        $addresses = auth()->user()
            ->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return UserAddressResource::collection($addresses);
    }

    /**
     * Store a newly created address.
     */
    public function store(\Illuminate\Http\Request $request): JsonResponse
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

        $address = auth()->user()->addresses()->create([
            ...$validated,
            'is_default' => $request->boolean('is_default'),
        ]);

        if ($address->is_default) {
            $address->setAsDefault();
        }

        return $this
            ->setMessage('Address created successfully.')
            ->setStatusCode(201)
            ->respond(new UserAddressResource($address));
    }

    /**
     * Display the specified address.
     */
    public function show(UserAddress $address): JsonResponse
    {
        $this->authorize('view', $address);

        return $this
            ->setMessage('Address retrieved successfully.')
            ->respond(new UserAddressResource($address));
    }

    /**
     * Update the specified address.
     */
    public function update(\Illuminate\Http\Request $request, UserAddress $address): JsonResponse
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

        return $this
            ->setMessage('Address updated successfully.')
            ->respond(new UserAddressResource($address));
    }

    /**
     * Remove the specified address.
     */
    public function destroy(UserAddress $address): JsonResponse
    {
        $this->authorize('delete', $address);

        $address->delete();

        return $this
            ->setMessage('Address deleted successfully.')
            ->respond(null);
    }

    /**
     * Set address as default.
     */
    public function setDefault(UserAddress $address): JsonResponse
    {
        $this->authorize('update', $address);

        $address->setAsDefault();

        return $this
            ->setMessage('Default address updated.')
            ->respond(new UserAddressResource($address));
    }

    /**
     * Get default shipping address.
     */
    public function defaultShipping(): JsonResponse
    {
        $address = auth()->user()->defaultShippingAddress();

        if (!$address) {
            return $this
                ->setMessage('No default shipping address found.')
                ->setStatusCode(404)
                ->respond(null);
        }

        return $this
            ->setMessage('Default shipping address retrieved.')
            ->respond(new UserAddressResource($address));
    }

    /**
     * Get default billing address.
     */
    public function defaultBilling(): JsonResponse
    {
        $address = auth()->user()->defaultBillingAddress();

        if (!$address) {
            return $this
                ->setMessage('No default billing address found.')
                ->setStatusCode(404)
                ->respond(null);
        }

        return $this
            ->setMessage('Default billing address retrieved.')
            ->respond(new UserAddressResource($address));
    }
}
