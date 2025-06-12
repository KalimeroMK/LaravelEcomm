@extends('front::layouts.master')
@section('title', 'Product Comparison')
@section('content')
    <div class="container my-4">
        <h2>Product Comparison</h2>
        @if($tooMany)
            <div class="alert alert-warning">You can compare up to 4 products only. The oldest was removed.</div>
        @endif
        @if($products->isEmpty())
            <div class="alert alert-info">No products selected for comparison.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead>
                    <tr>
                        <th>Feature</th>
                        @foreach($products as $product)
                            <th>{{ $product->title }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Image</td>
                        @foreach($products as $product)
                            <td>
                                @if(method_exists($product, 'getFirstMediaUrl'))
                                    <img src="{{ $product->getFirstMediaUrl('images', 'thumb') ?? asset('img/no-image.png') }}"
                                         alt="{{ $product->title }}" style="max-width:100px;">
                                @else
                                    <span>No image</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>Price</td>
                        @foreach($products as $product)
                            <td>{{ number_format($product->price, 2) }} {{ config('app.currency', 'USD') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>Short Description</td>
                        @foreach($products as $product)
                            <td>{{ Str::limit($product->summary, 100) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>Attributes</td>
                        @foreach($products as $product)
                            <td>
                                @if($product->attributeValues && $product->attributeValues->count())
                                    <ul class="list-unstyled mb-0">
                                        @foreach($product->attributeValues as $attrVal)
                                            @if($attrVal->attribute)
                                                <li><strong>{{ $attrVal->attribute->name }}:</strong> {{ $attrVal->value ?? '-' }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif
        <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Back</a>
    </div>
@endsection
