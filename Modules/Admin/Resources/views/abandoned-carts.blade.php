@extends('admin::layouts.master')
@section('title', 'Abandoned Carts')
@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Abandoned Carts</h1>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Abandoned</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_abandoned'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Converted</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['converted'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Recovery Rate</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['recovery_rate'] ?? 0 }}%</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-percentage fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abandoned Carts Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Abandoned Carts List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Total Items</th>
                                <th>Total Amount</th>
                                <th>Last Activity</th>
                                <th>Emails Sent</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($abandonedCarts as $cart)
                                <tr>
                                    <td>{{ $cart->id }}</td>
                                    <td>
                                        @if($cart->user)
                                            {{ $cart->user->name }}
                                        @else
                                            <span class="text-muted">Guest</span>
                                        @endif
                                    </td>
                                    <td>{{ $cart->email ?? 'N/A' }}</td>
                                    <td>{{ $cart->total_items }}</td>
                                    <td>${{ number_format($cart->total_amount, 2) }}</td>
                                    <td>{{ $cart->last_activity->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @php
                                            $emailsSent = 0;
                                            if ($cart->first_email_sent) $emailsSent++;
                                            if ($cart->second_email_sent) $emailsSent++;
                                            if ($cart->third_email_sent) $emailsSent++;
                                        @endphp
                                        {{ $emailsSent }}/3
                                    </td>
                                    <td>
                                        @if($cart->converted)
                                            <span class="badge badge-success">Converted</span>
                                        @else
                                            <span class="badge badge-warning">Abandoned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" onclick="viewCartDetails({{ $cart->id }})">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No abandoned carts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $abandonedCarts->links('pagination::admin-bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Details Modal -->
    <div class="modal fade" id="cartDetailsModal" tabindex="-1" role="dialog" aria-labelledby="cartDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartDetailsModalLabel">Cart Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="cartDetailsContent">
                    <!-- Cart details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function viewCartDetails(cartId) {
        // Load cart details via AJAX
        fetch(`/admin/analytics/abandoned-carts/${cartId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cart = data.data;
                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Cart Information</h6>
                                <p><strong>Total Items:</strong> ${cart.total_items}</p>
                                <p><strong>Total Amount:</strong> $${parseFloat(cart.total_amount).toFixed(2)}</p>
                                <p><strong>Last Activity:</strong> ${cart.last_activity}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Email Status</h6>
                                <p><strong>First Email:</strong> ${cart.first_email_sent || 'Not sent'}</p>
                                <p><strong>Second Email:</strong> ${cart.second_email_sent || 'Not sent'}</p>
                                <p><strong>Third Email:</strong> ${cart.third_email_sent || 'Not sent'}</p>
                            </div>
                        </div>
                        <hr>
                        <h6>Cart Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    if (cart.cart_data && Array.isArray(cart.cart_data)) {
                        cart.cart_data.forEach(item => {
                            html += `
                                <tr>
                                    <td>Product ID: ${item.product_id || 'N/A'}</td>
                                    <td>${item.quantity || 0}</td>
                                    <td>$${parseFloat(item.price || 0).toFixed(2)}</td>
                                    <td>$${parseFloat(item.amount || 0).toFixed(2)}</td>
                                </tr>
                            `;
                        });
                    }
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    document.getElementById('cartDetailsContent').innerHTML = html;
                    $('#cartDetailsModal').modal('show');
                }
            })
            .catch(error => {
                console.error('Error loading cart details:', error);
                alert('Error loading cart details');
            });
    }
</script>
@endpush

