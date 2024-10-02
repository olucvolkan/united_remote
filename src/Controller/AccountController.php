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

    public function __construct(
        Request $request,
        Response $response,
        CustomerRepository $customerRepository,
        TransferService $transferService
    ) {
        parent::__construct($request, $response);
        $this->customerRepository = $customerRepository;
        $this->transferService = $transferService;
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
            ], HttpStatusCode::BAD_REQUEST);
        }
        $this->customerRepository->getPdo()->beginTransaction();
        try {
            $newBalance = $this->transferService->deposit($id,$dto->funds);
            $this->customerRepository->getPdo()->commit();
            return $this->response->json([
                'status' => 'success',
                'message' => 'Deposit successful',
                'new_balance' => $newBalance
            ], HttpStatusCode::OK);
        } catch (\Throwable $e) {
            $this->customerRepository->getPdo()->rollBack();
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
        $this->customerRepository->getPdo()->beginTransaction();
        $newBalance = $this->transferService->withdraw($id, $dto->funds);
        try {
            $this->logTransaction($id, 'withdraw', $dto->funds);
            $this->customerRepository->getPdo()->commit();
            return $this->response->json([
                'status' => 'success',
                'message' => 'Withdraw successful',
                'new_balance' => $newBalance
            ], HttpStatusCode::OK);
        } catch (\Throwable $e) {
            $this->customerRepository->getPdo()->rollBack();
            return $this->response->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], HttpStatusCode::BAD_REQUEST);
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
            ], HttpStatusCode::BAD_REQUEST);
        }
        $this->customerRepository->getPdo()->beginTransaction();
        try {
            $this->transferService->withdraw($dto->from, $dto->funds);
            $this->transferService->deposit($dto->to, $dto->funds);
            $this->logTransaction($dto->from, 'transfer', -$dto->funds);
            $this->logTransaction($dto->to, 'transfer', $dto->funds);
            $this->customerRepository->getPdo()->commit();
            return $this->response->json([
                'status' => 'success',
                'message' => 'Transfer Successful',
            ], HttpStatusCode::OK);
        } catch (\Throwable $e) {
            $this->customerRepository->getPdo()->rollBack();
            return $this->response->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], HttpStatusCode::BAD_REQUEST);
        }
    }

    private function logTransaction(int $customerId, string $type, float $amount) {
        $stmt = $this->customerRepository->getPdo()->prepare("INSERT INTO transactions (customer_id, type, amount) VALUES (?, ?, ?)");
        $stmt->execute([$customerId, $type, $amount]);
    }
}