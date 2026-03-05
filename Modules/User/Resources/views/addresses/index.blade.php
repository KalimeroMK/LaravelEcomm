@extends('admin::layouts.master')

@section('title','My Addresses')

@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h4 class="font-weight-bold m-0">My Addresses</h4>
        <a href="{{ route('user.addresses.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Address
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($addresses->count() > 0)
            <div class="row">
                @foreach($addresses as $address)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 {{ $address->is_default ? 'border-primary' : '' }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="badge badge-{{ $address->type === 'shipping' ? 'info' : ($address->type === 'billing' ? 'warning' : 'success') }}">
                                    {{ ucfirst($address->type) }}
                                </span>
                                @if($address->is_default)
                                    <span class="badge badge-primary">Default</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $address->full_name }}</h5>
                                <p class="card-text">
                                    {{ $address->address1 }}<br>
                                    @if($address->address2)
                                        {{ $address->address2 }}<br>
                                    @endif
                                    {{ $address->city }}, {{ $address->state }} {{ $address->post_code }}<br>
                                    {{ $address->country }}
                                </p>
                                @if($address->phone)
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> {{ $address->phone }}
                                        </small>
                                    </p>
                                @endif
                                @if($address->email)
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-envelope"></i> {{ $address->email }}
                                        </small>
                                    </p>
                                @endif
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('user.addresses.edit', $address) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('user.addresses.destroy', $address) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                                @if(!$address->is_default)
                                    <form action="{{ route('user.addresses.default', $address) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            Set as Default
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                <h5>No addresses found</h5>
                <p class="text-muted">You haven't added any addresses yet.</p>
                <a href="{{ route('user.addresses.create') }}" class="btn btn-primary">
                    Add Your First Address
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
