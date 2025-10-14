# Laravel Translation Manager Guide (kalimeromk/laravel-translation-manager)

## What is this?

`kalimeromk/laravel-translation-manager` is a package for managing Laravel translations through a web interface. It provides:

-   Import existing translations from PHP files to database
-   Edit translations through web interface
-   Export translations back to PHP files
-   Automatic detection of translations in code
-   Detection of missing translations

## Access to Web Interface

Web interface is available at: **http://localhost/translations**

## Artisan Commands

### 1. Import translations from files to database

```bash
php artisan translations:import
```

-   Reads all translations from `resources/lang` directory
-   Saves them to `ltm_translations` table
-   By default, doesn't replace existing translations in database
-   To replace all: `php artisan translations:import --replace`

### 2. Finding translations in code

```bash
php artisan translations:find
```

-   Scans all PHP/Blade files
-   Finds all `__()`, `trans()`, `@lang` calls
-   Adds missing keys to database

### 3. Export translations from database to files

```bash
php artisan translations:export <group>
```

Examples:

-   `php artisan translations:export messages` - exports messages.php
-   `php artisan translations:export '*'` - exports everything
-   `php artisan translations:export --json` - exports JSON translations

### 4. Clean NULL translations

```bash
php artisan translations:clean
```

-   Deletes all translations that are NULL

### 5. Reset database

```bash
php artisan translations:reset
```

-   Deletes all translations from database

## How it works?

### Workflow:

1. **Import** - Import existing translations to database

    ```bash
    php artisan translations:import
    ```

2. **Find** - Find all translations in code

    ```bash
    php artisan translations:find
    ```

3. **Edit** - Edit translations through web interface

    - Open: http://localhost/translations
    - Click on translation to edit
    - Save

4. **Export** - Export translations back to files
    ```bash
    php artisan translations:export '*'
    ```

## Configuration

Configuration is in `config/translation-manager.php`:

```php
return [
    'route' => [
        'prefix' => 'translations',
        'middleware' => ['web', 'auth'], // Only authenticated users
    ],

    'delete_enabled' => true, // Allow deletion

    'exclude_groups' => [], // Groups that shouldn't be edited

    'exclude_langs' => [], // Languages that shouldn't be edited

    'template' => 'bootstrap5', // bootstrap3, bootstrap4, bootstrap5
];
```

## Database Schema

The package uses `ltm_translations` table:

```
- id
- status (0 = saved, 1 = changed)
- locale (en, fr, mk, etc.)
- group (messages, auth, etc.)
- key (home, name, etc.)
- value (translation)
- created_at
- updated_at
```

## Examples

### Adding new translation via Web Interface

1. Open http://localhost/translations
2. Select group (messages, auth, etc.)
3. Click "Add new keys"
4. Enter key (e.g. `welcome_message`)
5. Enter translations for each language
6. Click "Publish" to save to files

### Usage in Blade

```blade
{{ __('messages.home') }}
{{ __('messages.name') }}
{{ trans('auth.failed') }}
@lang('messages.welcome')
```

### Usage in PHP

```php
__('messages.home')
trans('messages.name')
trans_choice('messages.items', 5)
```

## Translation Status

-   ✅ **Imported**: 2512 translations
-   📁 **Groups**: messages, auth, pagination, passwords, sidebar, validation, apiResponse, partials
-   🌍 **Languages**: en, fr, mk, de, es, it, ar

## Next Steps

1. Open http://localhost/translations
2. Review translations
3. Add missing translations
4. Export translations: `php artisan translations:export '*'`
5. Commit and push changes

## Notes

-   Web interface is only available for authenticated users
-   Translations are saved in database but must be exported to files for production
-   For production, use PHP files, not database
-   Database is only for development and editing
