<?php

namespace DaniloPolani\JsonValidation;

use DaniloPolani\JsonValidation\Contracts\HasRuleMessage;
use Illuminate\Validation\Rules\Enum as EnumRule;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationRuleParser;

class Validator extends \Illuminate\Validation\Validator
{
    protected array $dynamicRules = [
        'between',
        'gt',
        'gte',
        'lt',
        'lte',
        'max',
        'min',
        'password',
        'size',
    ];

    /**
     * Get the error messages for an attribute and a validation rule.
     */
    public function getErrorMessage(string $attribute, string|HasRuleMessage|EnumRule $rule): array
    {
        if ($rule instanceof HasRuleMessage) {
            $messages = $rule->message();

            $messages = $messages ? (array) $messages : [get_class($rule)];

            foreach ($messages as $message) {
                $result[] = $this->buildMessage(
                    $message,
                    $attribute,
                    get_class($rule),
                    []
                );
            }

            return $result;
        }

        if ($rule instanceof EnumRule) {
            return [
                str_replace(':attribute', $attribute, $this->getMessage($attribute, 'enum')),
            ];
        }

        [$rule, $parameters] = ValidationRuleParser::parse($rule);
        $result = [];

        // First we will get the correct keys for the given attribute in case the field is nested in
        // an array. Then we determine if the given rule accepts other field names as parameters.
        // If so, we will replace any asterisks found in the parameters with the correct keys.
        if ($this->dependsOnOtherFields($rule)) {
            $parameters = $this->replaceDotInParameters($parameters);

            if ($keys = $this->getExplicitKeys($attribute)) {
                $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
            }
        }

        return [
            $this->buildMessage(
                $this->getMessage($attribute, $rule),
                $attribute,
                $rule,
                $parameters
            ),
        ];
    }

    /**
     * Build the validation message.
     */
    protected function buildMessage(string $message, string $attribute, string $rule, array $parameters): string
    {
        // Convert dynamic rules such as "size.array" into their original shape ("size")
        if (str_contains($rule, '.')) {
            $originalRule = Str::of($rule)->before('.')->snake()->toString();

            // $originalRule !== $rule is needed because when no "." is present on the rule, it returns the whole string
            if ($originalRule !== $rule && in_array($originalRule, $this->dynamicRules)) {
                $rule = $originalRule;
            }
        }

        $result = $this->makeReplacements(...func_get_args());

        // Preserve original attribute name if nested (e.g. array.1.field)
        if (str_contains($attribute, '.')) {
            $result = str_replace($this->getDisplayableAttribute($attribute), $attribute, $result);
        }

        return $result;
    }
}
