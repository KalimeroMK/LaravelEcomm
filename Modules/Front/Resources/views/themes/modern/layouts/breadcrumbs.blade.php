{{-- Shared page breadcrumb band, styled by the base all_front CSS.
     Usage: @include($themePath . '.layouts.breadcrumbs', ['title' => 'About Us']) --}}
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <ul class="bread-list">
                        <li><a href="{{ route('front.index') }}">Home<i class="ti-arrow-right"></i></a></li>
                        <li class="active"><a href="javascript:void(0);">{{ $title ?? '' }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
