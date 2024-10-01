<?php
namespace DTO;

use Core\Validator\Validator;

class CustomerDTO {
    public string $name;
    public string $surname;
    public float $balance;
    private Validator $validator;
    private  array $errors = [];

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->surname = $data['surname'] ?? '';
        $this->balance = isset($data['balance']) ? (float) $data['balance'] : 0.0;

        $this->validator = new Validator();
    }

    public function isValid(): bool
    {
        $rules = [
            'name' => 'required|minLength:3|maxLength:50',
            'surname' => 'required|minLength:3|maxLength:50',
            'balance' => 'required|min:0',
        ];

        $data = [
            'name' => $this->name,
            'surname' => $this->surname,
            'balance' => $this->balance,
        ];

        $isValid = $this->validator->validate($data, $rules);

        if (!$isValid) {
            $this->errors = $this->validator->getErrors();
        }

        return $isValid;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}