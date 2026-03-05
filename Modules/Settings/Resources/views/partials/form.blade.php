{{--
    Settings Form - Single Record Approach
    Settings always exist and can only be updated, never deleted.
--}}
<form class="form-horizontal" method="POST" 
      action="{{ route('settings.update', $settings['id']) }}"
      enctype="multipart/form-data">
    @method('put')
    @csrf

    <div class="form-group">
        <label for="inputTitle" class="col-form-label">Short info <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="short_des" placeholder="Short description"
               value="{{ $settings['short_des'] ?? '' }}"
               class="form-control">
    </div>
    <div class="form-group">
        <label for="inputEmail" class="col-form-label">@lang('partials.email') <span class="text-danger">*</span></label>
        <input id="inputEmail" type="email" name="email" placeholder="Email address"
               value="{{ $settings['email'] ?? '' }}"
               class="form-control">
    </div>
    <div class="form-group">
        <label for="inputPhone" class="col-form-label">@lang('partials.phone') <span class="text-danger">*</span></label>
        <input id="inputPhone" type="text" name="phone" placeholder="Phone number"
               value="{{ $settings['phone'] ?? '' }}"
               class="form-control">
    </div>
    <div class="form-group">
        <label for="inputAddress" class="col-form-label">@lang('messages.address') <span class="text-danger">*</span></label>
        <input id="inputAddress" type="text" name="address" placeholder="Business address"
               value="{{ $settings['address'] ?? '' }}"
               class="form-control">
    </div>
    
    <!-- Template Selection -->
    <div class="form-group">
        <label for="active_template" class="col-form-label">Active Template <span class="text-danger">*</span></label>
        <select id="active_template" name="active_template" class="form-control">
            @php
                $availableThemes = get_available_themes();
                $activeTheme = $settings['active_template'] ?? 'default';
            @endphp
            @foreach($availableThemes as $theme)
                <option value="{{ $theme }}" {{ $activeTheme == $theme ? 'selected' : '' }}>
                    {{ ucfirst($theme) }} Theme
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">Choose the active theme for your website.</small>
    </div>
    
    <div class="form-group">
       <textarea class="form-control" id="description" name="description">
           {{ $settings['description'] ?? '' }}
       </textarea>
    </div>
    
    <div class="form-group">
        <label for="inputImage">@lang('partials.logo')</label>
        <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
        @if(!empty($settings['logo']))
            <small class="form-text text-muted">Current logo: {{ $settings['logo'] }}</small>
        @endif
    </div>

    <!-- Google Map Section -->
    <div class="form-group">
        <label for="map" class="col-form-label">Pin your address on the map</label>
        <div id="map" style="height: 400px; width: 100%;"></div>
        <input type="hidden" id="latitude" name="latitude" value="{{ $settings['latitude'] ?? null }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ $settings['longitude'] ?? null }}">
    </div>

    <div class="button-container">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
    </div>
</form>

@push('styles')
    <link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush

@push('scripts')
    <script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
    
    <script>
        $(document).ready(function () {
            $('#description').summernote({
                placeholder: "Write short description.....",
                tabsize: 2,
                height: 150
            });
        });
    </script>
    
    @if(!empty($settings['google_map_api_key']))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $settings['google_map_api_key'] }}"></script>
    <script>
        $(document).ready(function () {
            let map;
            let marker;
           
            const dbLat = {{ $settings['latitude'] ?? '50.8503' }};
            const dbLng = {{ $settings['longitude'] ?? '4.3517' }};
            const initialLocation = {lat: dbLat, lng: dbLng};

            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: initialLocation,
                    zoom: 5
                });

                marker = new google.maps.Marker({
                    position: initialLocation,
                    map: map,
                    draggable: true
                });

                google.maps.event.addListener(marker, 'dragend', function (event) {
                    document.getElementById('latitude').value = event.latLng.lat();
                    document.getElementById('longitude').value = event.latLng.lng();
                });
            }

            google.maps.event.addDomListener(window, 'load', function () {
                if (document.getElementById('map')) {
                    initMap();
                }
            });
        });
    </script>
    @endif
@endpush
