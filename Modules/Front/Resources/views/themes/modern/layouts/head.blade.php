{{-- Modern Theme Head Meta/Links --}}
{{-- This file contains metadata and common links --}}
@yield('meta')

{{-- Feed Links --}}
@include('feed::links')

{{-- Additional Styles --}}
@stack('styles')
