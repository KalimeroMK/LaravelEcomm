<li><a href="{{ route('product-cat',$child_category->slug) }}">{{ $child_category->title }}<i class="fa fa-angle-right"
                                                                                              aria-hidden="true"></i></a>
    @if ($child_category->categories)
        <ul class="main-category">
            @foreach ($child_category->categories as $childCategory)
                @include('front::layouts.child_category', ['child_category' => $childCategory])
            @endforeach
        </ul>
@endif