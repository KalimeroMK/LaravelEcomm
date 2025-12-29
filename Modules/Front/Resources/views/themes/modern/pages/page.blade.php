@extends($themePath . '.layouts.master')
@section('title', $page->title ?? 'Page')
@section('content')
<section class="page-header page-header-dark bg-secondary">
    <div class="container"><div class="row"><div class="col-md-12">
        <h1>{{ $page->title }}</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('front.index') }}">Home</a></li>
            <li class="active">{{ $page->title }}</li>
        </ol>
    </div></div></div>
</section>

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
