@extends($themePath . '.layouts.master')
@section('title','E-SHOP || Bundles')
@section('content')
@include($themePath . '.layouts.breadcrumbs', ['title' => 'Bundles'])

<section class="main-container">
    <div class="container">
        <div class="row isotope-container">
            @foreach($bundles as $bundle)
            <div class="col-md-4 col-sm-6 isotope-item">
                <div class="product-item">
                    <div class="product-item-img">
                        <a href="{{ route('front.bundle-detail', $bundle->slug) }}">
                            <img src="{{ $bundle->imageUrl }}" alt="{{ $bundle->title }}" class="img-responsive">
                        </a>
                        @if($bundle->discount)
                        <span class="badge badge-danger">Save {{ $bundle->discount }}%</span>
                        @endif
                    </div>
                    <div class="product-item-title">
                        <a href="{{ route('front.bundle-detail', $bundle->slug) }}">{{ $bundle->title }}</a>
                    </div>
                    <div class="product-item-price">
                        @php $after_discount = ($bundle->price - ($bundle->price * $bundle->discount) / 100); @endphp
                        @if($bundle->discount)<del class="text-muted">{{ currency($bundle->price) }}</del>@endif
                        <span class="text-default">{{ currency($after_discount) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center">
            {{ $bundles->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
</section>
@endsection
