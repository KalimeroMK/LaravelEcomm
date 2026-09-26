{{-- Layered navigation: one widget per filterable attribute.
     Options toggle ?{code}=v1,v2 in the query string (page resets). --}}
@if(!empty($layered_filters['attributes']) && $layered_filters['attributes']->isNotEmpty())
    @foreach($layered_filters['attributes'] as $filter)
        @continue($filter['options']->isEmpty())
        @php
            $selected = array_filter(explode(',', request()->query($filter['code'], '')));
        @endphp
        <div class="single-widget category attribute-filter" data-code="{{ $filter['code'] }}">
            <h3 class="title">{{ $filter['name'] }}</h3>
            @if($filter['type'] === 'swatch')
                <div class="swatch-list" style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach($filter['options'] as $option)
                        <a href="javascript:void(0);"
                           class="attr-option swatch{{ in_array($option['value'], $selected, true) ? ' active' : '' }}"
                           data-value="{{ $option['value'] }}"
                           title="{{ $option['label'] }} ({{ $option['count'] }})"
                           style="width:26px;height:26px;border-radius:50%;display:inline-block;border:2px solid {{ in_array($option['value'], $selected, true) ? '#F7941D' : '#e3e3e3' }};background: {{ $option['color_hex'] ?? '#ddd' }};"></a>
                    @endforeach
                </div>
            @else
                <ul class="categor-list">
                    @foreach($filter['options'] as $option)
                        <li>
                            <a href="javascript:void(0);"
                               class="attr-option{{ in_array($option['value'], $selected, true) ? ' active' : '' }}"
                               data-value="{{ $option['value'] }}"
                               @if(in_array($option['value'], $selected, true)) style="color:#F7941D;font-weight:600;" @endif>
                                {{ $option['label'] }}
                                <span class="text-muted">({{ $option['count'] }})</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.attribute-filter .attr-option').forEach(function (el) {
                el.addEventListener('click', function () {
                    var code = this.closest('.attribute-filter').dataset.code;
                    var value = this.dataset.value;
                    var params = new URLSearchParams(window.location.search);
                    var current = (params.get(code) || '').split(',').filter(Boolean);
                    var idx = current.indexOf(value);

                    if (idx === -1) {
                        current.push(value);
                    } else {
                        current.splice(idx, 1);
                    }

                    if (current.length) {
                        params.set(code, current.join(','));
                    } else {
                        params.delete(code);
                    }
                    params.delete('page');

                    window.location.search = params.toString();
                });
            });
        });
    </script>
@endif
