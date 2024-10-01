<?php
namespace DTO;

class AccountWithdrawDTO {
    public int $customerId;
    public float $funds;

    public function __construct(array $data) {
        $this->customerId = $data['customer_id'] ?? 0;
        $this->funds = isset($data['funds']) ? (float) $data['funds'] : 0.0;
    }

    public function isValid(): bool {
        return $this->customerId > 0 && $this->funds > 0;
    }
}