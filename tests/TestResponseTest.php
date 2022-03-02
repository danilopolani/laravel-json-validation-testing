<?php

use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
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

    $data = [
        'response' => 'error',
        'errors' => ['key' => 'key must be baz'],
    ];

    $testResponse = TestResponse::fromBaseResponse(
        (new Response())->setContent(json_encode($data))
    );

    $testResponse->assertJsonValidationErrorRule('key', $rule);
});

it('throws error when field is a string and rule is not provided', function () {
    $response = TestResponse::fromBaseResponse(new Response());
    $response->assertJsonValidationErrorRule('foo');
})->throws(ExpectationFailedException::class, 'No validation rule was provided.');
