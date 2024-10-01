<?php
namespace DTO;

class AccountTransferDTO {
    public int $from;
    public int $to;
    public float $funds;

    public function __construct(array $data) {
        $this->from = $data['from'] ?? 0;
        $this->to = $data['to'] ?? 0;
        $this->funds = isset($data['funds']) ? (float) $data['funds'] : 0.0;
    }

    public function isValid(): bool {
        return $this->from > 0 && $this->to > 0 && $this->from !== $this->to && $this->funds > 0;
    }
}