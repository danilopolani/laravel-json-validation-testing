<?php

namespace DaniloPolani\JsonValidation;

use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Validation\ValidationRuleParser;

class Validator extends \Illuminate\Validation\Validator
{
    /**
     * Get the error messages for an attribute and a validation rule.
     *
     * @param  string  $attribute
     * @param  string|\Illuminate\Contracts\Validation\Rule  $rule
     * @return array
     */
    public function getErrorMessage(string $attribute, string|RuleContract $rule): array
    {
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

        if ($rule instanceof RuleContract) {
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
     *
     * @param  string $message
     * @param  string $attribute
     * @param  string $rule
     * @param  array $parameters
     * @return string
     */
    protected function buildMessage(string $message, string $attribute, string $rule, array $parameters): string
    {
        $result = $this->makeReplacements(...func_get_args());

        // Preserve original attribute name if nested (e.g. array.1.field)
        if (str_contains($attribute, '.')) {
            $result = str_replace($this->getDisplayableAttribute($attribute), $attribute, $result);
        }

        return $result;
    }
}
