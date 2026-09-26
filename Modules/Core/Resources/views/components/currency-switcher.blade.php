@php
    $currencies = \Modules\Core\Models\Currency::activeList();
    $current = active_currency();
@endphp

@if($current && $currencies->count() > 1)
    <div class="language-switcher currency-switcher">
        <div class="language-dropdown">
            <button class="language-toggle" type="button" onclick="toggleCurrencyDropdown()">
                <span class="language-name">{{ $current->code }} ({{ $current->symbol }})</span>
                <span class="caret">▼</span>
            </button>
            <ul class="language-menu" id="currencyMenu" style="display: none;">
                @foreach($currencies as $currency)
                    @continue($currency->code === $current->code)
                    <li>
                        <a class="language-item" href="{{ route('currency.switch', $currency->code) }}">
                            <span class="language-name">{{ $currency->code }} ({{ $currency->symbol }})</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <script>
        function toggleCurrencyDropdown() {
            const menu = document.getElementById('currencyMenu');
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.currency-switcher')) {
                const menu = document.getElementById('currencyMenu');
                if (menu) menu.style.display = 'none';
            }
        });
    </script>
@endif
