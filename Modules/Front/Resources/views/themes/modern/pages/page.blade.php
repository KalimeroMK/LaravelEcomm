@extends($themePath . '.layouts.master')
@section('title', $page->title ?? 'Page')
@section('content')
@include($themePath . '.layouts.breadcrumbs', ['title' => $page->title])

<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                {!! html_entity_decode($page->description) !!}
            </div>
        </div>
    </div>
</section>
@endsection
