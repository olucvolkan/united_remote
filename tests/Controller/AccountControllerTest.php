<?php

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class AccountControllerTest extends TestCase
{
    private $client;
    private $baseUrl;

    protected function setUp(): void
    {
        $this->client = new Client();

        $this->baseUrl = getenv('APP_BASE_URL');
    }

    public function testGetAccountBalance()
    {
        $customer = $this->createCustomer(100.50);
        $response = $this->client->get("{$this->baseUrl}/api/accounts/{$customer['id']}");
        $this->assertEquals(200, $response->getStatusCode());

        $result = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('balance', $result);
        $this->deleteCustomer($customer['id']);
    }

    public function testDepositFunds()
    {
        $customer = $this->createCustomer(100);

        $depositData = [
            'funds' => 50,
        ];

        $response = $this->client->post("{$this->baseUrl}/api/accounts/{$customer['id']}/deposit", [
            'json' => $depositData
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $result = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('status', $result);
        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('new_balance', $result);
        $this->assertEquals(150, $result['new_balance']);
        $this->deleteCustomer($customer['id']);

    }

    public function testWithdrawFunds()
    {
        $customer = $this->createCustomer(100);

        $withdrawData = [
            'funds' => 20.00,
        ];

        $response = $this->client->post("{$this->baseUrl}/api/accounts/{$customer['id']}/withdraw", [
            'json' => $withdrawData
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $result = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('status', $result);
        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('new_balance', $result);
        $this->assertEquals(80, $result['new_balance']);
        $this->deleteCustomer($customer['id']);

    }

    public function testTransferFunds()
    {
        $customer1 = $this->createCustomer(100);
        $customer2 = $this->createCustomer(200);

        $transferData = [
            'from' => $customer1['id'],
            'to' => $customer2['id'],
            'funds' => 30.00,
        ];

        $response = $this->client->post("{$this->baseUrl}/api/accounts/transfer", [
            'json' => $transferData
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $customer1 = $this->client->get("{$this->baseUrl}/api/customers/{$customer1['id']}");
        $this->assertEquals(200, $customer1->getStatusCode());
        $customer2 = $this->client->get("{$this->baseUrl}/api/customers/{$customer2['id']}");
        $this->assertEquals(200, $customer1->getStatusCode());
        $customer1 = json_decode($customer1->getBody(), true);
        $customer2 = json_decode($customer2->getBody(), true);

        $result = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('status', $result);
        $this->assertEquals('success', $result['status']);

        $this->assertEquals(230, $customer2['balance']);
        $this->assertEquals(70, $customer1['balance']);

        $this->deleteCustomer($customer1['id']);
        $this->deleteCustomer($customer2['id']);
    }

    private function createCustomer($amount){
        $customerData = [
            'name' => 'John',
            'surname' => 'Doe',
            'balance' => $amount,
        ];
        $response = $this->client->post("{$this->baseUrl}/api/customers", ['json' => $customerData]);
        $this->assertEquals(201, $response->getStatusCode());
        return json_decode($response->getBody(), true);
    }

    private function deleteCustomer(int $id){
        $response = $this->client->delete("{$this->baseUrl}/api/customers/{$id}", );
        $this->assertEquals(200, $response->getStatusCode());
    }
}