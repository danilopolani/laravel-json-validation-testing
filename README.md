# JSON Validation errors testing helper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/danilopolani/laravel-json-validation-testing.svg?style=flat-square)](https://packagist.org/packages/danilopolani/laravel-json-validation-testing)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/danilopolani/laravel-json-validation-testing/run-tests?label=tests)](https://github.com/danilopolani/laravel-json-validation-testing/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/danilopolani/laravel-json-validation-testing/Check%20&%20fix%20styling?label=code%20style)](https://github.com/danilopolani/laravel-json-validation-testing/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/danilopolani/laravel-json-validation-testing.svg?style=flat-square)](https://packagist.org/packages/danilopolani/laravel-json-validation-testing)

A simple library to help testing JSON validation errors by rules.  

The current way to test HTTP errors is **broken**: it tests that the validation fails or you have to manually specify the error message. With this package you'll just need to specify the validation rule that fails and it builds the error message to be sure at 100% that it fails and **what fails**. (Further reading [here](https://github.com/laravel/framework/pull/41239) in the old merge request for Laravel).

## Installation

You can install the package via composer:

```bash
composer require --dev danilopolani/laravel-json-validation-testing
```

## Usage

The package provides an helper to retrieve a compiled error message:

```php
use DaniloPolani\JsonValidation\JsonValidation;

JsonValidation::getRuleErrorMessage('foo', 'required');

// => ["The foo field is required."]
```

However, if you need to test that your HTTP APIs, the package ships with a brand new `assertJsonValidationErrorRule` assertion to make your life easier:

```php
it('throws validation error', function () {
    $this->postJson('/')
        ->assertJsonValidationErrorRule('foo', 'required');
});
```

Of course you can provide your own custom validation Rules:

```php
it('throws validation error', function () {
    $this->postJson('/')
        ->assertJsonValidationErrorRule('foo', new MyCustomRule());
});
```

It supports as well dynamic rules, such as `between`, `size`, `max` etc. You just need to specify the type of rule you want to apply:

```php
it('throws validation error', function () {
    $this->postJson('/')
        ->assertJsonValidationErrorRule('foo', 'between.string:1,5') // The foo must be between 1 and 5 characters.
        ->assertJsonValidationErrorRule('foo', 'size.array:3'); // The foo must contain 3 items.
});
```

You can even test multiple validation errors at once by providing an array of `field => rule` as argument:

```php
use DaniloPolani\JsonValidation\JsonValidation;

it('throws validation error', function () {
    $this->postJson('/')
        ->assertJsonValidationErrorRule([
            'foo' => 'required',
            'bar' => 'required_array_keys:foo,baz',
        ]);
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Danilo Polani](https://github.com/danilopolani)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
