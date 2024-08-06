<form class="form-horizontal" method="POST" action="{{ route('settings.update', $settings->id) }}"
      enctype="multipart/form-data">
    @method('put')
    @csrf

    <!-- Existing form fields -->

    <div class="form-group">
        <label for="inputTitle" class="col-form-label">Short info <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="short_des" placeholder="Short description"
               value="{{ $settings->short_des ?? null }}"
               class="form-control">
    </div>
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.email') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="email" placeholder="Short description"
               value="{{ $settings->email ?? null }}"
               class="form-control">
    </div>
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('partials.phone') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="phone" placeholder="Short description"
               value="{{ $settings->phone ?? null }}"
               class="form-control">
    </div>
    <div class="form-group">
        <label for="inputTitle" class="col-form-label">@lang('messages.address') <span
                    class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="address" placeholder="address"
               value="{{ $settings->address ?? null }}"
               class="form-control">
    </div>
    <div class="form-group">
       <textarea class="form-control" id="description"
                 name="description">{{ $settings->description ?? null }}</textarea>
    </div>
    <div class="form-group">
        <label for="inputImage">@lang('partials.logo')</label>
        <input type="file" class="form-control" id="inputImage" name="images[]" multiple>
    </div>

    <!-- Google Map Section -->
    <div class="form-group">
        <label for="map" class="col-form-label">Pin your address on the map</label>
        <div id="map" style="height: 400px; width: 100%;"></div>
        <input type="hidden" id="latitude" name="latitude" value="{{ $settings->latitude ?? null }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ $settings->longitude ?? null }}">
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
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $settings->google_map_api_key ?? 'AIzaSyDhiON8B3SmouSxloPhI3AtdNl2Sovmi_8'}}"></script>
    <script>
        $(document).ready(function () {
            $('#description').summernote({
                placeholder: "Write short description.....",
                tabsize: 2,
                height: 150
            });

            // Initialize Google Map
            let map;
            let marker;
           
            const dbLat = {{ $settings->latitude ?? '50.8503' }};
            const dbLng = {{ $settings->longitude ?? '4.3517' }};
            const initialLocation = {lat: dbLat, lng: dbLng};

            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    center: initialLocation,
                    zoom: 5 // Adjust zoom level for a wider view of Europe
                });

                marker = new google.maps.Marker({
                    position: initialLocation,
                    map: map,
                    draggable: true
                });

                // Update hidden inputs with marker's position
                google.maps.event.addListener(marker, 'dragend', function (event) {
                    document.getElementById('latitude').value = event.latLng.lat();
                    document.getElementById('longitude').value = event.latLng.lng();
                });
            }

            // Ensure the map container is rendered before initializing the map
            google.maps.event.addDomListener(window, 'load', function () {
                if (document.getElementById('map')) {
                    initMap();
                } else {
                    console.error('Map container not found.');
                }
            });
        });
    </script>

@endpush