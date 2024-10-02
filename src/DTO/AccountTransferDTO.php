<?php

namespace DTO;


use Core\Validator\Validator;

class AccountTransferDTO extends  Validator
{
    public int $from;
    public int $to;
    public float $funds;
    public function __construct(array $data)
    {
        $this->from = $data['from'] ?? 0;
        $this->to = $data['to'] ?? 0;
        $this->funds = isset($data['funds']) ? (float) $data['funds'] : 0.0;

    }

    public function isValid(): bool
    {
        $rules = [
            'from' => 'required|int|min:1|different:to',
            'to' => 'required|int|min:1',
            'funds' => 'required|float|min:0.01'
        ];

        $data = [
            'from' => $this->from,
            'to' => $this->to,
            'funds' => $this->funds
        ];

        $isValid = $this->validate($data, $rules);

        if (!$isValid) {
            $this->errors = $this->getErrors();
        }

        return $isValid;
    }

}