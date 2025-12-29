@extends('admin::layouts.master')

@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">@lang('partials.list')</h6>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               title="Add User">
                <i class="fas fa-plus"></i> @lang('partials.create')
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(isset($products) && count($products))
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.sku')</th>
                            <th>@lang('partials.title')</th>
                            <th>@lang('sidebar.category')</th>
                            <th>@lang('partials.is_featured')</th>
                            <th>@lang('partials.price')</th>
                            <th>@lang('messages.discount')</th>
                            <th>@lang('partials.tags')</th>
                            <th>@lang('sidebar.brands')</th>
                            <th>@lang('partials.quantity')</th>
                            <th>@lang('partials.image')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('sidebar.attributes')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $product['sku'] ?? 'N/A' }}</td>
                                <td>{{ $product['title'] ?? '' }}</td>
                                <td>{{ isset($product['categories']) ? implode(', ', array_column($product['categories'], 'title')) : 'N/A' }}</td>
                                <td>{{ !empty($product['is_featured']) ? 'Yes' : 'No' }}</td>
                                <td>{{ $product['price'] ?? '' }}</td>
                                <td>{{ $product['discount'] ?? '' }}</td>
                                <td>
                                    @if(isset($product['tags']) && is_array($product['tags']))
                                        {{ implode(', ', array_column($product['tags'], 'title')) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $product['brand']['title'] ?? 'N/A' }}</td>
                                <td>{{ $product['stock'] ?? '' }}</td>
                                <td>
                                    <img src="{{ $product['photo'] ?? asset('backend/img/thumbnail-default.jpg') }}"
                                         alt="{{ $product['title'] ?? '' }}"
                                         style="max-width: 80px;">
                                </td>
                                <td>{{ ucfirst($product['status'] ?? '') }}</td>
                                <td>
                                    @if(!empty($product['attributes']))
                                        <ul class="list-unstyled mb-0">
                                            @foreach($product['attributes'] as $attributeValue)
                                                @php
                                                    $attr = $attributeValue['attribute'] ?? null;
                                                    $column = $attr['type'] ?? 'text';
                                                    $value = $attributeValue['value'] ?? null;
                                                @endphp
                                                @if($attr && $value)
                                                    <li>
                                                        <strong>{{ $attr['name'] ?? $attr['label'] ?? '' }}:</strong> {{ $value }}
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product['id']) }}"
                                       class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit"><i
                                                class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.products.destroy', $product['id']) }}" method="POST"
                                          style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-confirm"
                                                data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13">No products found!</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">No products found!</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
