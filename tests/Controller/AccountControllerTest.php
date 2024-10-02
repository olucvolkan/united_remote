<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
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

    /**
     * Data provider for testTransferFundsWithDataProvider.
     *
     * @return array
     */
    public function transferFundsDataProvider(): array
    {
        return [
            'valid transfer' => [
                100.0,  // Initial balance of customer 1
                200.0,  // Initial balance of customer 2
                30.0,   // Transfer amount
                70.0,   // Expected balance of customer 1 after transfer
                230.0,  // Expected balance of customer 2 after transfer
                true,
            ],
            'full transfer' => [
                150.0,  // Initial balance of customer 1
                100.0,  // Initial balance of customer 2
                150.0,  // Transfer amount
                0.0,    // Expected balance of customer 1 after transfer
                250.0,  // Expected balance of customer 2 after transfer
                true,
            ],
            'no transfer (insufficient funds)' => [
                50.0,   // Initial balance of customer 1
                100.0,  // Initial balance of customer 2
                100.0,  // Transfer amount
                50.0,   // Expected balance of customer 1 after transfer (no change)
                100.0,  // Expected balance of customer 2 after transfer (no change)
                false   // Should the transfer be successful? (No)
            ],
        ];
    }

    /**
     * Test transfer funds functionality with data provider.
     *
     * @dataProvider transferFundsDataProvider
     */
    public function testTransferFundsWithDataProvider(
        float $initialBalanceCustomer1,
        float $initialBalanceCustomer2,
        float $transferAmount,
        float $expectedBalanceCustomer1,
        float $expectedBalanceCustomer2,
        bool $shouldSucceed
    ) {
        $customer1 = $this->createCustomer($initialBalanceCustomer1);
        $customer2 = $this->createCustomer($initialBalanceCustomer2);

        $transferData = [
            'from' => $customer1['id'],
            'to' => $customer2['id'],
            'funds' => $transferAmount,
        ];

        try {
            $response = $this->client->post("{$this->baseUrl}/api/accounts/transfer", [
                'json' => $transferData
            ]);

            $this->assertEquals(200, $response->getStatusCode());

        } catch (ClientException $e) {
            // If the transfer should fail due to insufficient funds, assert a 400 response code
            if (!$shouldSucceed) {
                $this->assertEquals(400, $e->getResponse()->getStatusCode());
                $errorResponse = json_decode($e->getResponse()->getBody(), true);
                $this->assertEquals('error', $errorResponse['status']);
                $this->assertEquals('Insufficient balance', $errorResponse['message']);
            } else {
                throw $e;
            }
        }

        $customer1Response = $this->client->get("{$this->baseUrl}/api/customers/{$customer1['id']}");
        $customer2Response = $this->client->get("{$this->baseUrl}/api/customers/{$customer2['id']}");

        $customer1Data = json_decode($customer1Response->getBody(), true);
        $customer2Data = json_decode($customer2Response->getBody(), true);

        $this->assertEquals($expectedBalanceCustomer1, $customer1Data['balance']);
        $this->assertEquals($expectedBalanceCustomer2, $customer2Data['balance']);

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