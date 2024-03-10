<?php

namespace DaniloPolani\JsonValidation;

use DaniloPolani\JsonValidation\Contracts\HasRuleMessage;
use Illuminate\Support\Facades\App;

class JsonValidation
{
    /**
     * Get the error messages for an attribute and a validation rule.
     */
    public static function getRuleErrorMessage(string $attribute, string|HasRuleMessage $rule): array
    {
        return (new Validator(App::make('translator'), [], []))
            ->getErrorMessage(...func_get_args());
    }
}
