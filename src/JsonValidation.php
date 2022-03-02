<?php

namespace DaniloPolani\JsonValidation;

use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Support\Facades\App;

class JsonValidation
{
    /**
     * Get the error messages for an attribute and a validation rule.
     *
     * @param  string  $attribute
     * @param  string|\Illuminate\Contracts\Validation\Rule  $rule
     * @return array
     */
    public static function getRuleErrorMessage(string $attribute, string|RuleContract $rule): array
    {
        return (new Validator(App::make('translator'), [], []))
            ->getErrorMessage(...func_get_args());
    }
}
