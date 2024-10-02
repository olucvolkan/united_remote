<?php

namespace Service;

use Repositories\CustomerRepository;

class TransferService
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }


    public function withdraw(int $customerId, int $amount)
    {
        $customer = $this->customerRepository->findById($customerId);
        if ($customer['balance'] < $amount) {
            throw new \Exception("Insufficient balance");
        }
        $newBalance = $customer['balance'] - $amount;
        $this->customerRepository->updateCustomer($customerId, $customer['name'], $customer['surname'], $newBalance);

        return $newBalance;
    }

    public function deposit(int $customerId, int $amount)
    {
        $customer = $this->customerRepository->findById($customerId);
        $newBalance = $customer['balance'] + $amount;
        $this->customerRepository->updateCustomer($customerId, $customer['name'], $customer['surname'], $newBalance);
        return $newBalance;
    }

}