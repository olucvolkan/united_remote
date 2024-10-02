<?php

namespace DTO;


use Core\Validator\Validator;

class AccountDepositDTO extends Validator
{
    public float $funds;

    public function __construct(array $data)
    {
        $this->funds = isset($data['funds']) ? (float) $data['funds'] : 0.0;

    }

    public function isValid(): bool
    {
        $rules = [
            'funds' => 'required|float|min:0.01'
        ];

        $data = [
            'funds' => $this->funds
        ];

        $isValid = $this->validate($data, $rules);

        if (!$isValid) {
            $this->errors = $this->getErrors();
        }

        return $isValid;
    }
}