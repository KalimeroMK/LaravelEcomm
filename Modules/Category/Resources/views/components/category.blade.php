@php
    $children = $category->childrenCategories()->with('childrenCategories')->get();
@endphp

<li class="dd-item" data-id="{{ $category->id }}">
    <div class="dd-handle">
        {{ $category->title }}
    </div>
    @if ($children->isNotEmpty())
        <ol class="dd-list">
            @foreach ($children as $childCategory)
                @include('category::components.single-category', ['category' => $childCategory])
            @endforeach
        </ol>
    @endif
</li>