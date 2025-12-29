{{-- Modern Theme Child Category Recursive Component --}}
<li>
    <a href="{{ route('front.product-cat', $child_category->slug) }}">{{ $child_category->title }}</a>
    @if($child_category->childrenCategories->count() > 0)
        <ul class="list-unstyled">
            @foreach($child_category->childrenCategories as $subCategory)
                @include($themePath . '.layouts.child_category', ['child_category' => $subCategory])
            @endforeach
        </ul>
    @endif
</li>
