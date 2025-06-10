@php use Illuminate\Support\Arr; @endphp
@if(isset($banners) && count($banners))
    <div class="row">
        @foreach($banners as $banner)
            @if($banner->isActive())
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="single-banner">
                        <a href="{{ $banner->link ?? '#' }}" target="_blank" onclick="axios.post('{{ route('banner.impression', $banner->id) }}')">
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="img-fluid">
                        </a>
                        <div class="content">
                            <h3>{{ $banner->title }}</h3>
                            @if($banner->categories && $banner->categories->count())
                                <p class="small">
                                    @foreach($banner->categories as $cat)
                                        <span class="badge badge-info">{{ $cat->title }}</span>
                                    @endforeach
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
