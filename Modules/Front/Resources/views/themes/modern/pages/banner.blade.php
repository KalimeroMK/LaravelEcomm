@extends($themePath . '.layouts.master')
@section('content')
<section class="main-container">
    <div class="container">
        <div class="row">
            @if($banners)
            @foreach($banners as $banner)
            <div class="col-md-12 mb-30">
                <div class="banner-item">
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="img-responsive">
                    <div class="banner-caption">
                        <h2>{{ $banner->title }}</h2>
                        <p>{!! html_entity_decode($banner->description) !!}</p>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</section>
@endsection
