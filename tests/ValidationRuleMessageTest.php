<?php

use DaniloPolani\JsonValidation\JsonValidation;
use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Support\Facades\App;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;

it('returns the error message for built-in rules', function () {
    $trans = new Translator(new ArrayLoader(), 'en');
    $trans->addLines([
        'validation.required_array_keys' => 'The :attribute field must contain entries for :values',
        'validation.required' => 'The :attribute field is required.',
        'validation.required_without' => 'The :attribute field is required when :values is not present.',
    ], 'en');

    /** @var \DaniloPolani\JsonValidation\Tests\TestCase $this */
    App::getFacadeApplication()->instance('translator', $trans);

    expect(JsonValidation::getRuleErrorMessage('foo', 'required'))->toBe([
        'The foo field is required.',
    ]);
    expect(JsonValidation::getRuleErrorMessage('foo', 'required_array_keys:bar,baz'))->toBe([
        'The foo field must contain entries for bar, baz',
    ]);
    expect(JsonValidation::getRuleErrorMessage('foo', 'required_without:bar'))->toBe([
        'The foo field is required when bar is not present.',
    ]);
});

it('extracts the error message from a custom rule', function () {
    $rule = new class () implements RuleContract {
        public function passes($attribute, $value): bool
        {
            return true;
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
