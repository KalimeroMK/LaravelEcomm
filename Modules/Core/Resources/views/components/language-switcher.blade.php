@props(['locales', 'currentLocale'])

<div class="language-switcher">
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="flag">{{ $locales[$currentLocale]['flag'] ?? '🌐' }}</span>
            <span class="language-name">{{ $locales[$currentLocale]['native'] ?? $currentLocale }}</span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="languageDropdown">
            @foreach($locales as $locale => $config)
                @if($locale !== $currentLocale)
                    <li>
                        <a class="dropdown-item" href="{{ route('language.switch', $locale) }}">
                            <span class="flag">{{ $config['flag'] }}</span>
                            <span class="language-name">{{ $config['native'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>

<style>
.language-switcher .dropdown-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: white;
    color: #495057;
    text-decoration: none;
    transition: all 0.2s ease;
}

.language-switcher .dropdown-toggle:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
}

.language-switcher .flag {
    font-size: 18px;
    line-height: 1;
}

.language-switcher .language-name {
    font-size: 14px;
    font-weight: 500;
}

.language-switcher .dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    color: #495057;
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.language-switcher .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #212529;
}

.language-switcher .dropdown-item .flag {
    font-size: 16px;
}

.language-switcher .dropdown-item .language-name {
    font-size: 14px;
}
</style>
