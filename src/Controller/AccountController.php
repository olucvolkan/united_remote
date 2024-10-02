<?php
namespace Controller;

use Core\BaseController\BaseController;
use Core\Http\Request\Request;
use Core\Http\Response\Response;
use DTO\AccountDepositDTO;
use DTO\AccountWithdrawDTO;
use DTO\AccountTransferDTO;
use Repositories\CustomerRepository;
use Service\TransferService;
use Utils\HttpStatusCode;

class AccountController extends  BaseController {

    private CustomerRepository $customerRepository;
    private TransferService $transferService;

    public function __construct( Request $request, Response $response, $database)
    {
        parent::__construct($request, $response, $database);
        $this->customerRepository = new CustomerRepository($database);
        $this->transferService = new TransferService($this->customerRepository);
    }
    public function getAccountBalance(int $id) {
        $customer = $this->customerRepository->findById($id);
        if ($customer) {
            $this->response->json(['balance' => $customer['balance']]);
        } else {
            $this->response->error('Customer account not found', HttpStatusCode::NOT_FOUND);
        }
    }

    public function deposit(int $id) {
        $data = $this->request->getBodyParams();
        $dto = new AccountDepositDTO($data);
        if (!$dto->isValid()) {
            return $this->response->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $dto->getErrors()
            ], 400);
        }
        $this->database->beginTransaction();
        try {
            $newBalance = $this->transferService->deposit($id,$dto->funds);
            $this->database->commit();
            return $this->response->json([
                'status' => 'success',
                'message' => 'Withdraw successful',
                'new_balance' => $newBalance
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
    }

    public function withdraw(int $id) {
        $data = $this->request->getBodyParams();
        $dto = new AccountWithdrawDTO($data);
        if (!$dto->isValid()) {
            return $this->response->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $dto->getErrors()
            ], 400);
        }
        $this->database->beginTransaction();
        $newBalance = $this->transferService->withdraw($id, $dto->funds);
        try {
            $this->logTransaction($id, 'withdraw', $dto->funds);
            $this->database->commit();
            return $this->response->json([
                'status' => 'success',
                'message' => 'Withdraw successful',
                'new_balance' => $newBalance
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
    }

    public function transfer() {
        $data = $this->request->getBodyParams();
        $dto = new AccountTransferDTO($data);
        if (!$dto->isValid()) {
            return $this->response->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $dto->getErrors()
            ], 400);
        }
        $this->database->beginTransaction();
        try {
            $this->transferService->withdraw($dto->from, $dto->funds);
            $this->transferService->deposit($dto->to, $dto->funds);
            $this->logTransaction($dto->from, 'transfer', -$dto->funds);
            $this->logTransaction($dto->to, 'transfer', $dto->funds);
            $this->database->commit();
            return $this->response->json([
                'status' => 'success',
                'message' => 'Transfer Successfull',
            ], HttpStatusCode::OK);
        } catch (\Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
    }

    private function logTransaction(int $customerId, string $type, float $amount) {
        $stmt = $this->database->prepare("INSERT INTO transactions (customer_id, type, amount) VALUES (?, ?, ?)");
        $stmt->execute([$customerId, $type, $amount]);
    }
}