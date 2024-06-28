@extends('admin::layouts.master')

@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">@lang('partials.list')</h6>
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               title="Add User">
                <i class="fas fa-plus"></i> @lang('partials.create')
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                @if(isset($products) && $products->count())
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.title')</th>
                            <th>@lang('sidebar.category')</th>
                            <th>@lang('partials.is_featured')</th>
                            <th>@lang('partials.price')</th>
                            <th>@lang('messages.discount')</th>
                            <th>@lang('partials.size')</th>
                            <th>@lang('partials.conditions')</th>
                            <th>@lang('sidebar.brands')</th>
                            <th>@lang('partials.quantity')</th>
                            <th>@lang('partials.image')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('sidebar.attributes')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $product->title }}</td>
                                <td>{{ $product->category->title ?? 'N/A' }}</td>
                                <td>{{ $product->is_featured ? 'Yes' : 'No' }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $product->discount }}</td>
                                <td>{{ $product->size->name ?? 'N/A' }}</td>
                                <td>{{ $product->condition->status ?? 'N/A' }}</td>
                                <td>{{ $product->brand->title ?? 'N/A' }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    <img src="{{ $product->photo_url }}" alt="{{ $product->title }}"
                                         style="max-width: 80px;">
                                </td>
                                <td>{{ ucfirst($product->status) }}</td>
                                <td>
                                    @foreach($product->attributeValues as $attributeValue)
                                        @if(!empty($attributeValue->value))
                                            <strong>{{ $attributeValue->attribute->name }}
                                                :</strong> {{ $attributeValue->value }}
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $product->id) }}"
                                          style="display: inline-block;">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">@lang('partials.no_records_found')</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
