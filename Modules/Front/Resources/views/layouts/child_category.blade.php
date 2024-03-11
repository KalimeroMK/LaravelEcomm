<li>
    <a href="{{ route('front.product-cat', $child_category->slug) }}">{{ $child_category->title }}
        @if($child_category->childrenCategories->isNotEmpty())
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        @endif
    </a>
    @if ($child_category->childrenCategories->isNotEmpty())
        <ul class="sub-category">
            @foreach ($child_category->childrenCategories as $childCategory)
                @include('front::layouts.child_category', ['child_category' => $childCategory])
            @endforeach
        </ul>
    @endif
</li>
