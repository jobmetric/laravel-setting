[contributors-shield]: https://img.shields.io/github/contributors/jobmetric/laravel-setting.svg?style=for-the-badge
[contributors-url]: https://github.com/jobmetric/laravel-setting/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/jobmetric/laravel-setting.svg?style=for-the-badge&label=Fork
[forks-url]: https://github.com/jobmetric/laravel-setting/network/members
[stars-shield]: https://img.shields.io/github/stars/jobmetric/laravel-setting.svg?style=for-the-badge
[stars-url]: https://github.com/jobmetric/laravel-setting/stargazers
[license-shield]: https://img.shields.io/github/license/jobmetric/laravel-setting.svg?style=for-the-badge
[license-url]: https://github.com/jobmetric/laravel-setting/blob/master/LICENCE.md
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-blue.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/majidmohammadian

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

# Laravel Setting

**Build Dynamic Settings. Manage Them Safely.**

Laravel Setting lets you define application settings as form-driven classes and store/retrieve them from a database-backed cache. Stop scattering config values across controllers and configs. Use a unified settings layer with validation, caching, discovery, and clear storage keys.

## Why Laravel Setting?

### Form-Driven Settings
Define each setting form by extending `JobMetric\Setting\Contracts\AbstractSetting` and returning a `JobMetric\Form\FormBuilder` that describes fields.

### Application-Key Storage
Each setting form produces a stable `formName()` (`application_key`). Values are stored per field under that form.

### Fast Reads with Cache
Reads are cached under `config('setting.cache_key')` (default: `SETTING`). Cache is invalidated whenever settings are stored or forgotten.

### Events and Lifecycle Control
Optionally fire `StoreSettingEvent` and `ForgetSettingEvent` when storing or deleting settings.

### Namespaced Discovery
Register one or more namespaces in `SettingNamespaceRegistry`. `SettingRegistry` can discover and validate all `AbstractSetting` subclasses inside them.

## What is a Setting Form?

A setting form is a class that extends `AbstractSetting` and implements:
- `application()`
- `key()`
- `title()`
- `description()`
- `form()` (returns `FormBuilder`)

From these, `formName()` is built as `application() . '_' . key()` and used as the main storage identifier (`form` column in the `settings` table).

## What Awaits You?

By adopting Laravel Setting, you can:
- Create setting form classes with `setting:make`
- Store values safely with `Setting::dispatch()` (class-based) or `Setting::dispatchByForm()` (raw)
- Retrieve values with `Setting::get()`, `Setting::form()` and class helpers like `Setting::getFromClass()`
- Invalidate cache via `setting:clear`
- Discover all setting forms through `SettingNamespaceRegistry` and `SettingRegistry`
- Listen to store/forget events with optional dispatching control

## Quick Start

Install Laravel Setting via Composer:

```bash
composer require jobmetric/laravel-setting
```

Run migrations:

```bash
php artisan migrate
```

Create a setting form class:

```bash
php artisan setting:make ConfigSetting --application=app --title="Config" --description="Application configuration"
```

Edit the generated class and implement `form()` by adding the desired fields using `FormBuilder`.

## Usage

### Store settings by form name (raw)
Use `dispatchByForm()` (or the `dispatchSetting()` helper). Keys in the object must start with the `form` prefix (`{application_key}_...`).

```php
use JobMetric\Setting\Facades\Setting;

Setting::dispatchByForm('app_config', [
    'app_config_site_name' => 'My Site',
    'app_config_site_url'  => 'https://example.com',
]);
```

Or via helper:

```php
dispatchSetting('app_config', [
    'app_config_site_name' => 'My Site',
]);
```

### Store settings by setting class (validated)
Use `dispatch()` (or `dispatchSettingFromClass()`) for class-based dispatch with DTO validation via `FormBuilderRequest`.

```php
use JobMetric\Setting\Facades\Setting;
use App\Settings\ConfigSetting;

Setting::dispatch(ConfigSetting::class, [
    'site_name' => 'My Site',
]);
```

### Read settings

```php
use JobMetric\Setting\Facades\Setting;

$siteName = Setting::get('app_config_site_name');
$form = Setting::form('app_config');
```

### Read from a setting class

```php
use JobMetric\Setting\Facades\Setting;
use App\Settings\ConfigSetting;

$siteName = Setting::getFromClass(ConfigSetting::class, 'site_name');
$form = Setting::getFromClass(ConfigSetting::class, null);
```

### Forget settings (delete + cache invalidation)

```php
use JobMetric\Setting\Facades\Setting;

Setting::forget('app_config');
```

### Clear cache

```bash
php artisan setting:clear
```

## Documentation

Ready to transform your Laravel applications? Our comprehensive documentation is your gateway to mastering Laravel Setting:

**[📚 Read Full Documentation ->](https://doc.jobmetric.net/package/laravel-setting)**

The documentation includes:
- **Getting Started** - installation and migration steps
- **Setting Forms** - `AbstractSetting`, `formName()`, and `FormBuilder`
- **Setting Service** - storing, forgetting, cache invalidation, and retrieval APIs
- **Registries** - `SettingNamespaceRegistry` and `SettingRegistry` discovery rules
- **Commands** - `setting:make`, `setting:clear`
- **Events** - `StoreSettingEvent` and `ForgetSettingEvent`
- **Helpers** - `dispatchSetting`, `dispatchSettingFromClass`, and read/exists utilities
- **Testing** - package tests and common verification flows

## Contributing

Thank you for participating in `laravel-setting`. A contribution guide can be found [here](CONTRIBUTING.md).

## License

The `laravel-setting` is open-sourced software licensed under the MIT license. See [License File](LICENCE.md) for more information.
