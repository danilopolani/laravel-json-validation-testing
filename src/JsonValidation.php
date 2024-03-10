<?php

namespace DaniloPolani\JsonValidation;

use DaniloPolani\JsonValidation\Contracts\HasRuleMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rules\Enum as EnumRule;

class JsonValidation
{
    /**
     * Get the error messages for an attribute and a validation rule.
     */
    public static function getRuleErrorMessage(string $attribute, string|HasRuleMessage|EnumRule $rule): array
    {
        return (new Validator(App::make('translator'), [], []))
            ->getErrorMessage(...func_get_args());
    }
}
