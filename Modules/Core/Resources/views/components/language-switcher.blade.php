@php
$locales = config('app.locales', []);
$currentLocale = app()->getLocale();
@endphp

<div class="language-switcher">
    <div class="language-dropdown">
        <button class="language-toggle" type="button" onclick="toggleLanguageDropdown()">
            <span class="flag">{{ $locales[$currentLocale]['flag'] ?? '🌐' }}</span>
            <span class="language-name">{{ $locales[$currentLocale]['native'] ?? $currentLocale }}</span>
            <span class="caret">▼</span>
        </button>
        <ul class="language-menu" id="languageMenu" style="display: none;">
            @foreach($locales as $locale => $config)
                @if($locale !== $currentLocale)
                    <li>
                        <a class="language-item" href="{{ route('language.switch', $locale) }}">
                            <span class="flag">{{ $config['flag'] }}</span>
                            <span class="language-name">{{ $config['native'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>

<script>
function toggleLanguageDropdown() {
    const menu = document.getElementById('languageMenu');
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
    } else {
        menu.style.display = 'none';
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.language-dropdown');
    const menu = document.getElementById('languageMenu');
    if (dropdown && !dropdown.contains(event.target)) {
        menu.style.display = 'none';
    }
});
</script>

<style>
.language-switcher {
    display: inline-block;
    position: relative;
}

.language-dropdown {
    position: relative;
}

.language-toggle {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 4px;
    color: inherit;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.language-toggle:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.5);
}

.language-toggle .flag {
    font-size: 16px;
    line-height: 1;
}

.language-toggle .language-name {
    font-weight: 500;
}

.language-toggle .caret {
    font-size: 10px;
    margin-left: 4px;
    opacity: 0.7;
}

.language-menu {
    position: absolute;
    top: 100%;
    right: 0;
    z-index: 1000;
    min-width: 160px;
    padding: 5px 0;
    margin: 2px 0 0;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    list-style: none;
}

.language-menu li {
    display: block;
    width: 100%;
}

.language-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 15px;
    color: #333;
    text-decoration: none;
    transition: background 0.2s;
}

.language-item:hover {
    background: #f5f5f5;
    color: #333;
}

.language-item .flag {
    font-size: 16px;
}

.language-item .language-name {
    font-size: 14px;
}

/* Dark theme variant for topbar */
.topbar .language-switcher .language-toggle {
    color: #fff;
    border-color: rgba(255,255,255,0.3);
}

.topbar .language-switcher .language-toggle:hover {
    background: rgba(255,255,255,0.1);
}

/* Light theme variant for middle header */
.middle-inner .language-switcher .language-toggle {
    color: #333;
    border-color: #ddd;
}

.middle-inner .language-switcher .language-toggle:hover {
    background: #f5f5f5;
}
</style>
