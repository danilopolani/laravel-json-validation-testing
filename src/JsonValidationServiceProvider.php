<?php

namespace DaniloPolani\JsonValidation;

use DaniloPolani\JsonValidation\Contracts\HasRuleMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class JsonValidationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('json-validation-testing');
    }

    public function packageBooted(): void
    {
        TestResponse::macro(
            'assertJsonValidationErrorRule',
            /**
             * Assert that the response has the given JSON validation errors.
             *
             * @param  string|array  $attribute
             * @param  string|\Illuminate\Contracts\Validation\Rule|null  $rule
             * @param  string  $responseKey
             * @return self
             */
            function (string|array $attribute, string|HasRuleMessage|null $rule = null, string $responseKey = 'errors') {
                $validationRules = $attribute;

                if (is_string($attribute)) {
                    PHPUnit::assertNotNull($rule, 'No validation rule was provided.');

                    $validationRules = [$attribute => $rule];
                }

                $validator = new Validator(App::make('translator'), [], []);

                foreach ($validationRules as $attribute => $rule) {
                    /** @var \Illuminate\Testing\TestResponse $this */
                    $this->assertJsonValidationErrors([
                        $attribute => $validator->getErrorMessage($attribute, $rule),
                    ], $responseKey);
                }

                return $this;
            }
        );
    }
}
