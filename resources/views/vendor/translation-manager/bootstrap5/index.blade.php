@extends(config('translation-manager.layout'))
@php($controller = \Kalimero\TranslationManager\Controller::class)

@section('title', 'Translation Manager')

@section('content')
<div class="container-fluid">
    @include('translation-manager::bootstrap5._notifications')
    @include('translation-manager::bootstrap5.blocks._mainBlock')
    @if(!isset($selectedModel) || !$selectedModel)
        @include('translation-manager::bootstrap5.blocks._addEditGroupKeys')
    @endif
    @if(isset($group) && !empty($group))
        @include('translation-manager::bootstrap5.blocks._edit')
    @elseif(isset($selectedModel) && $selectedModel)
        @include('translation-manager::bootstrap5.blocks._editModel')
    @else
        @include('translation-manager::bootstrap5.blocks._supportedLocales')
        @include('translation-manager::bootstrap5.blocks._publishAll')
    @endif
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/x-editable-bs5/dist/bootstrap-editable.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/x-editable-bs5/dist/bootstrap-editable.min.js"></script>
    @include('translation-manager::jsScript')
@endpush
