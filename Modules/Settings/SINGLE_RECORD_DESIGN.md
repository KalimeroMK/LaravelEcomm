# Settings Module - Single Record Design

## Концепт

Овој модул користи **SINGLE RECORD** архитектура. Тоа значи:

- ✅ Секогаш постои точно **ЕДЕН** settings запис
- ✅ Клиентот може да ги уредува (edit) settings
- ❌ Клиентот **НЕ МОЖЕ** да ги избрише settings
- ❌ Не може да се креираат нови settings записи

## Зошто?

Settings се критични за функционирање на апликацијата:
- Контакт информации (email, телефон, адреса)
- Тема на веб страната
- Плаќање и shipping подесувања
- SEO подесувања

Ако ги избрише - апликацијата ќе "пукне" (errors, missing data).

## Како работи?

### 1. Автоматско креирање
```php
// Ако нема settings - се креираат автоматски со default вредности
EnsureSettingsExist::class middleware
```

### 2. Заштита од бришење
```php
// SettingsPolicy::delete() секогаш враќа FALSE
// Никој не може да избрише settings
```

### 3. Само UPDATE дозволен
```php
// Routes: само index и update
Route::resource('settings', SettingsController::class)->only('index', 'update');
```

## Default вредности

При првото креирање, settings се пополнуваат со:
- `short_des`: "Quality products, fast delivery, best prices"
- `email`: "info@example.com"
- `phone`: "+1 (555) 123-4567"
- `address`: "123 Main Street, City, Country"
- `active_template`: "default"

## Технички детали

### Middleware
- `EnsureSettingsExist` - проверува и креира settings ако ги нема

### Actions
- `CreateDefaultSettingsAction` - креира default settings
- `GetSettingsAction` - ги враќа тековните settings
- `UpdateSettingsAction` - ги ажурира settings

### Policy
- `create()`: ❌ false (не може да се креираат нови)
- `update()`: ✅ true (админите можат да уредуваат)
- `delete()`: ❌ false (никој не може да брише)

## За програмери

Ако треба да додадете ново поле во settings:

1. Додадете го во `CreateDefaultSettingsAction` (default вредност)
2. Додадете го во `Update` Request (валидација)
3. Додадете го во формата (blade)
4. Додадете го во `GetSettingsAction` (ako користи селект)

Не заборавајте: секогаш има само ЕДЕН запис во `settings` табелата!
