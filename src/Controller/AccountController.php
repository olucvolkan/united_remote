<?php
namespace Controller;

use Core\BaseController\BaseController;
use DTO\AccountDepositDTO;
use DTO\AccountWithdrawDTO;
use DTO\AccountTransferDTO;
use PDO;
use Repository\CustomerRepository;

class AccountController extends  BaseController {

    public function getAccountBalance(int $id) {
        $customer = $this->customerRepository->getCustomerById($id);
        return isset($customer['balance']) ? $customer['balance'] : null;
    }

    public function deposit(array $data) {
        $dto = new AccountDepositDTO($data);
        if (!$dto->isValid()) {
            throw new \Exception("Invalid deposit data");
        }
        $this->pdo->beginTransaction();
        try {
            $customer = $this->customerRepository->getCustomerById($dto->customerId);
            $newBalance = $customer['balance'] + $dto->funds;
            $this->customerRepository->updateCustomer($dto->customerId, $customer['name'], $customer['surname'], $newBalance);
            $this->logTransaction($dto->customerId, 'deposit', $dto->funds);
            $this->pdo->commit();
            return $newBalance;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function withdraw(array $data) {
        $dto = new AccountWithdrawDTO($data);
        if (!$dto->isValid()) {
            throw new \Exception("Invalid withdraw data");
        }
        $this->pdo->beginTransaction();
        try {
            $customer = $this->customerRepository->getCustomerById($dto->customerId);
            if ($customer['balance'] < $dto->funds) {
                throw new \Exception("Insufficient balance");
            }
            $newBalance = $customer['balance'] - $dto->funds;
            $this->customerRepository->updateCustomer($dto->customerId, $customer['name'], $customer['surname'], $newBalance);
            $this->logTransaction($dto->customerId, 'withdraw', $dto->funds);
            $this->pdo->commit();
            return $newBalance;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function transfer(array $data) {
        $dto = new AccountTransferDTO($data);
        if (!$dto->isValid()) {
            throw new \Exception("Invalid transfer data");
        }
        $this->pdo->beginTransaction();
        try {
            $this->withdraw(['customer_id' => $dto->from, 'funds' => $dto->funds]);
            $this->deposit(['customer_id' => $dto->to, 'funds' => $dto->funds]);
            $this->logTransaction($dto->from, 'transfer', -$dto->funds);
            $this->logTransaction($dto->to, 'transfer', $dto->funds);
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function logTransaction(int $customerId, string $type, float $amount) {
        $stmt = $this->pdo->prepare("INSERT INTO transactions (customer_id, type, amount) VALUES (?, ?, ?)");
        $stmt->execute([$customerId, $type, $amount]);
    }
}