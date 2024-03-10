<?php

use DaniloPolani\JsonValidation\Contracts\HasRuleMessage;
use DaniloPolani\JsonValidation\JsonValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;

$rules = require './tests/validation.php';
$rulesToLoad = Collection::make($rules)->mapWithKeys(fn (mixed $value, string $key) => [
    'validation.' . $key => $value,
])->toArray();

it('returns the error message for built-in rules', function () use ($rulesToLoad) {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines($rulesToLoad, 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    expect(JsonValidation::getRuleErrorMessage('foo', 'required'))->toBe([
        'The foo field is required.',
    ]);
    expect(JsonValidation::getRuleErrorMessage('foo', 'required_array_keys:bar,baz'))->toBe([
        'The foo field must contain entries for: bar, baz.',
    ]);
    expect(JsonValidation::getRuleErrorMessage('foo', 'required_without:bar'))->toBe([
        'The foo field is required when bar is not present.',
    ]);
    expect(JsonValidation::getRuleErrorMessage('foo.1.bar', 'required'))->toBe([
        'The foo.1.bar field is required.',
    ]);
});

it('extracts the error message from a custom rule', function () {
    $rule = new class () implements ValidationRule, HasRuleMessage {
        public function validate(string $attribute, mixed $value, \Closure $fail): void
        {
            return;
        }

        public function message(): string
        {
            return ':attribute must be baz';
        }
    };

    $trans = new Translator(new ArrayLoader(), 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    expect(JsonValidation::getRuleErrorMessage('foo', $rule))->toBe(['foo must be baz']);
});

it('handles dynamic "between" rule', function (string $shape) use ($rules, $rulesToLoad) {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines($rulesToLoad, 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    $expected = str_replace(
        [':min', ':max', ':attribute'],
        [3, 5, 'foo'],
        $rules['between'][$shape],
    );

    expect(JsonValidation::getRuleErrorMessage('foo', sprintf('between.%s:3,5', $shape)))->toBe([$expected]);
})->with([
    'array',
    'file',
    'numeric',
    'string',
]);

it('handles dynamic "gt" rule', function (string $shape) use ($rules, $rulesToLoad) {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines($rulesToLoad, 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    $expected = str_replace(
        [':value', ':attribute'],
        [3, 'foo'],
        $rules['gt'][$shape],
    );

    expect(JsonValidation::getRuleErrorMessage('foo', sprintf('gt.%s:3', $shape)))->toBe([$expected]);
})->with([
    'array',
    'file',
    'numeric',
    'string',
]);

it('handles dynamic "gte" rule', function (string $shape) use ($rules, $rulesToLoad) {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines($rulesToLoad, 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    $expected = str_replace(
        [':value', ':attribute'],
        [3, 'foo'],
        $rules['gte'][$shape],
    );

    expect(JsonValidation::getRuleErrorMessage('foo', sprintf('gte.%s:3', $shape)))->toBe([$expected]);
})->with([
    'array',
    'file',
    'numeric',
    'string',
]);

it('handles dynamic "lt" rule', function (string $shape) use ($rules, $rulesToLoad) {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines($rulesToLoad, 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    $expected = str_replace(
        [':value', ':attribute'],
        [3, 'foo'],
        $rules['lt'][$shape],
    );

    expect(JsonValidation::getRuleErrorMessage('foo', sprintf('lt.%s:3', $shape)))->toBe([$expected]);
})->with([
    'array',
    'file',
    'numeric',
    'string',
]);

it('handles dynamic "lte" rule', function (string $shape) use ($rules, $rulesToLoad) {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines($rulesToLoad, 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    $expected = str_replace(
        [':value', ':attribute'],
        [3, 'foo'],
        $rules['lte'][$shape],
    );

    expect(JsonValidation::getRuleErrorMessage('foo', sprintf('lte.%s:3', $shape)))->toBe([$expected]);
})->with([
    'array',
    'file',
    'numeric',
    'string',
]);

it('handles dynamic "max" rule', function (string $shape) use ($rules, $rulesToLoad) {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines($rulesToLoad, 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    $expected = str_replace(
        [':max', ':attribute'],
        [3, 'foo'],
        $rules['max'][$shape],
    );

    expect(JsonValidation::getRuleErrorMessage('foo', sprintf('max.%s:3', $shape)))->toBe([$expected]);
})->with([
    'array',
    'file',
    'numeric',
    'string',
]);

it('handles dynamic "min" rule', function (string $shape) use ($rules, $rulesToLoad) {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines($rulesToLoad, 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    $expected = str_replace(
        [':min', ':attribute'],
        [3, 'foo'],
        $rules['min'][$shape],
    );

    expect(JsonValidation::getRuleErrorMessage('foo', sprintf('min.%s:3', $shape)))->toBe([$expected]);
})->with([
    'array',
    'file',
    'numeric',
    'string',
]);

it('handles dynamic "password" rule', function (string $shape) use ($rules, $rulesToLoad) {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines($rulesToLoad, 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    $expected = str_replace(
        ':attribute',
        'foo',
        $rules['password'][$shape],
    );

    expect(JsonValidation::getRuleErrorMessage('foo', sprintf('password.%s', $shape)))->toBe([$expected]);
})->with([
    'letters',
    'mixed',
    'numbers',
    'symbols',
    'uncompromised',
]);

it('handles dynamic "size" rule', function (string $shape) use ($rules, $rulesToLoad) {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines($rulesToLoad, 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    $expected = str_replace(
        [':size', ':attribute'],
        [3, 'foo'],
        $rules['size'][$shape],
    );

    expect(JsonValidation::getRuleErrorMessage('foo', sprintf('size.%s:3', $shape)))->toBe([$expected]);
})->with([
    'array',
    'file',
    'numeric',
    'string',
]);
