<?php

namespace Core\Validator;

abstract class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleSet) {
            $ruleList = explode('|', $ruleSet);
            foreach ($ruleList as $rule) {

                $ruleName = $rule;
                $ruleParams = [];

                if (strpos($rule, ':') !== false) {
                    [$ruleName, $ruleParamStr] = explode(':', $rule);
                    $ruleParams = explode(',', $ruleParamStr);
                }

                $value = $data[$field] ?? null;

                if (method_exists($this, $ruleName)) {
                    if (!$this->{$ruleName}($value, ...$ruleParams)) {
                        $this->addError($field, $ruleName, $ruleParams);
                    }
                }
            }
        }

        return empty($this->errors);
    }


    private function required($value): bool
    {
        return !empty($value);
    }


    private function minLength($value, $length): bool
    {
        return is_string($value) && strlen($value) >= (int)$length;
    }

    private function maxLength($value, $length): bool
    {
        return is_string($value) && strlen($value) <= (int)$length;
    }

    private function min($value, $minValue): bool
    {
        return is_numeric($value) && $value >= (float)$minValue;
    }

    private function max($value, $maxValue): bool
    {
        return is_numeric($value) && $value <= (float)$maxValue;
    }

    private function float($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    private function int($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function addError(string $field, string $rule, array $params = []): void
    {
        $this->errors[$field][] = "Validation failed for rule '{$rule}' with parameters: " . implode(', ', $params);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}