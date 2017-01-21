# mailboxlayer-laravel
Adds Laravel 5 validation rule for checking e-mail addresses using the mailboxlayer API

## Installation

```sh
composer require bagf/mailboxlayer-laravel
```

Add this service provider to your Laravel 5 app providers array in `config/app.php`

```php
        Bagf\Mailboxlayer\ServiceProvider::class,
```

Add these configuration values to `config/services.php`

```php
    'mailboxlayer' => [
        'https' => true,
        'access_key' => env('MAILBOXLAYER'),
    ],
```

You can then set your access key in the `.env` file

```sh
MAILBOXLAYER="KEY_GOES_HERE"
```

Add these two translations to your validation language file in `resources/lang/en/validation.php`

```php
    'mailboxlayer'         => 'The :attribute is not valid. :suggestion',
    'mailboxlayer_suggest'  => 'Did you mean :email?',
```
