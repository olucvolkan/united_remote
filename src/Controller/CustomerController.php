<?php
namespace Controller;

use Core\BaseController\BaseController;
use Core\Http\Request\Request;
use Core\Http\Response\Response;
use DTO\CustomerDTO;
use Repositories\CustomerRepository;
use Utils\HttpStatusCode;

class CustomerController extends BaseController {

    private CustomerRepository $customerRepository;

    public function __construct( Request $request, Response $response, $database)
    {
        parent::__construct($request, $response, $database);
        $this->customerRepository = new CustomerRepository($database);
    }

    public function post() {
        $data = $this->request->getBodyParams();
        $customerDTO = new CustomerDTO($data);
        if (!$customerDTO->isValid()) {
            $this->response->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $customerDTO->getErrors()
            ], 400);
            return;
        }
        $customerId = $this->customerRepository->createCustomer($customerDTO->name, $customerDTO->surname, $customerDTO->balance);
        $this->response->json(['message' => 'Customer added', 'id' => $customerId], HttpStatusCode::CREATED);
    }

    public function get(int $id) {
        $customer = $this->customerRepository->findById($id);
        if ($customer) {
            $this->response->json($customer);
        } else {
            $this->response->error('Customer not found', HttpStatusCode::NOT_FOUND);
        }

    }

    public function getAll() {
        $customers = $this->customerRepository->findAll();
        if ($customers) {
            $this->response->json($customers);
        } else {
            $this->response->error('Customer not found', HttpStatusCode::NOT_FOUND);
        }

    }

    public function put(int $id) {
        $data = $this->request->getBodyParams();
        $customerDTO = new CustomerDTO($data);
        if (!$customerDTO->isValid()) {
            $this->response->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $customerDTO->getErrors()
            ], 400);
            return;
        }
        $updated = $this->customerRepository->updateCustomer($id, $customerDTO->name, $customerDTO->surname, $customerDTO->balance);
        if ($updated) {
            $this->response->json(['message' => 'Customer updated', 'name' => $customerDTO->name, 'surname' => $customerDTO->surname, 'balance' => $customerDTO->balance ]);
        } else {
            $this->response->error('Customer not found or not updated', HttpStatusCode::NOT_FOUND);
        }
    }

    public function delete(int $id) {
        $deleted = $this->customerRepository->deleteCustomer($id);
        if ($deleted) {
            $this->response->json(['message' => 'Customer deleted']);
        } else {
            $this->response->error('Customer not found or not deleted', HttpStatusCode::NOT_FOUND);
        }

    }
}