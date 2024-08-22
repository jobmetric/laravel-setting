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

# Setting for laravel

This is a package for a dynamic setting across different Laravel projects.

## Install via composer

Run the following command to pull in the latest version:
```bash
composer require jobmetric/laravel-setting
```

## Documentation

To use the services of this package, please follow the instructions below.

### Migrate the database

Run the following command to migrate the database:
```bash
php artisan migrate
```

### Usage

#### Dispatch setting into the database

The `dispatch` method will create a new setting if it does not exist, otherwise it will update the existing setting.

```php
use JobMetric\Setting\Facades\Setting as SettingFacade;

SettingFacade::dispatch('config', [
    'config_name' => 'job metric',
    'config_url' => 'jobmetric.net',
    'config_address' => [
        'city' => 'Mashhad',
        'street' => 'Pastor',
        'postal_code' => '1234567890',
    ],
]);
```

> The first parameter is the setting key, and the second parameter is an array of key-value pairs.
>
> Since event is an extraneous task, it is not useful in this method, this value is optionally placed in the third parameter, so that if your program needs it, it can be set.
>
> The data array keys must start with config_ which is the same code, otherwise the storage will not be done.
> 
> Key-value pairs are stored in the `settings` table on a record-by-record basis.
> 
> The value of the keys can be an array or string or boolean or integer or float.

> When the settings are updated, the settings caches are cleared, and if each user executes a new request on the server, the system cache is rebuilt.

#### Get setting

The `get` method will return the value of the setting key.

```php
use JobMetric\Setting\Facades\Setting as SettingFacade;

$config_name = SettingFacade::get('config_name');
```

> The first parameter is the setting key.
> 
> The second parameter is the default value of the setting key. If the setting key does not exist, the default value will be returned.
> 
> The `get` method will return the value of the setting key.

#### Get all settings

The `all` method will return all settings.

```php
use JobMetric\Setting\Facades\Setting as SettingFacade;

$settings = SettingFacade::all();
```

#### Forget setting

The `forget` method will delete the setting code.

```php
use JobMetric\Setting\Facades\Setting as SettingFacade;

SettingFacade::forget('config');
```

> The first parameter is the setting code.

#### Has setting

The `has` method will return true if the setting code exists, otherwise it will return false.

```php
use JobMetric\Setting\Facades\Setting as SettingFacade;

$has = SettingFacade::has('config_name');
```

> The first parameter is the setting key.
> 
> The `has` method will return true if the setting code exists, otherwise it will return false.

### Helper functions

#### Dispatch setting

The `setting` helper function will create a new setting if it does not exist, otherwise it will update the existing setting.

```php
dispatchSetting('config', [
    'config_name' => 'job metric',
    'config_url' => 'jobmetric.net',
    'config_address' => [
        'city' => 'Mashhad',
        'street' => 'Pastor',
        'postal_code' => '1234567890',
    ],
]);
```

#### Forget setting

The `forgetSetting` helper function will delete the setting code.

```php
forgetSetting('config');
```

#### Get setting

The `getSetting` helper function will return the value of the setting key.

```php
$config_name = getSetting('config_name');
```

#### Code settings

All the values set in the code form are returned.

```php
$settings = codeSettings('config');
```

#### Has setting

The `hasSetting` helper function will return true if the setting code exists, otherwise it will return false.

```php
$has = hasSetting('config_name');
```

## License

The MIT License (MIT). Please see [License File](https://github.com/jobmetric/laravel-setting/blob/master/LICENCE.md) for more information.
