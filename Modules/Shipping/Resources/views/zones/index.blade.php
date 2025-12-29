@extends('admin::layouts.master')

@section('title', 'Shipping Zones')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Shipping Zones</h3>
                        <a href="{{ route('shipping.zones.create') }}" class="btn btn-primary float-right">Create Zone</a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Countries</th>
                                    <th>Methods</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($zones as $zone)
                                    <tr>
                                        <td>{{ $zone->id }}</td>
                                        <td>{{ $zone->name }}</td>
                                        <td>
                                            @if($zone->countries)
                                                {{ implode(', ', $zone->countries) }}
                                            @else
                                                All
                                            @endif
                                        </td>
                                        <td>{{ $zone->methods->count() }}</td>
                                        <td>{{ $zone->priority }}</td>
                                        <td>
                                            <span class="badge badge-{{ $zone->is_active ? 'success' : 'danger' }}">
                                                {{ $zone->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('shipping.zones.edit', $zone) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('shipping.zones.destroy', $zone) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No shipping zones found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

