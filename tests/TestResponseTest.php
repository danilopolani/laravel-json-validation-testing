<?php

use DaniloPolani\JsonValidation\Contracts\HasRuleMessage;
use DaniloPolani\JsonValidation\Tests\TestEnumRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\Rules\Enum;
use PHPUnit\Framework\ExpectationFailedException;

it('accepts a string as field name', function () {
    $data = [
        'response' => 'error',
        'errors' => ['key' => 'The key field is required.'],
    ];

    $testResponse = TestResponse::fromBaseResponse(
        (new Response())->setContent(json_encode($data))
    );

    $testResponse->assertJsonValidationErrorRule('key', 'required');
});

it('accepts an array of field => rule', function () {
    $data = [
        'response' => 'error',
        'errors' => ['key' => 'The key field is required.'],
    ];

    $testResponse = TestResponse::fromBaseResponse(
        (new Response())->setContent(json_encode($data))
    );

    $testResponse->assertJsonValidationErrorRule(['key' => 'required']);
});

it('accepts a custom rule', function () {
    $rule = new class() implements ValidationRule, HasRuleMessage
    {
        public function validate(string $attribute, mixed $value, \Closure $fail): void
        {
            return;
        }

        public function message(): string
        {
            return ':attribute must be baz';
        }
    };

    $data = [
        'response' => 'error',
        'errors' => ['key' => 'key must be baz'],
    ];

    $testResponse = TestResponse::fromBaseResponse(
        (new Response())->setContent(json_encode($data))
    );

    $testResponse->assertJsonValidationErrorRule('key', $rule);
});

it('accepts "Enum" rule', function () {
    $data = [
        'response' => 'error',
        'errors' => ['key' => 'The selected key is invalid.'],
    ];

    $testResponse = TestResponse::fromBaseResponse(
        (new Response())->setContent(json_encode($data))
    );

    $testResponse->assertJsonValidationErrorRule('key', new Enum(TestEnumRole::class));
});

it('throws error when field is a string and rule is not provided', function () {
    $response = TestResponse::fromBaseResponse(new Response());
    $response->assertJsonValidationErrorRule('foo');
})->throws(ExpectationFailedException::class, 'No validation rule was provided.');
