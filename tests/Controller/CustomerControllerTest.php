<?php

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class CustomerControllerTest extends TestCase
{
    private $client;
    private $baseUrl;

    protected function setUp(): void
    {
        $this->client = new Client();
        $this->baseUrl = getenv('APP_BASE_URL');
    }

    public function testGetCustomer()
    {
        $response = $this->client->get("{$this->baseUrl}/api/customers");
        $this->assertEquals(200, $response->getStatusCode());
        $customers = json_decode($response->getBody(), true);
        $this->assertIsArray($customers);
        $this->assertNotEmpty($customers);
    }

    public function testCreateCustomer()
    {
        $customerData = [
            'name' => 'John',
            'surname' => 'Doe',
            'balance' => 100.50,
        ];

        $response = $this->client->post("{$this->baseUrl}/api/customers", ['json' => $customerData]);
        $this->assertEquals(201, $response->getStatusCode());
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('id', $result);
    }

    public function testUpdateCustomer()
    {
        $customerData = [
            'name' => 'Jane',
            'surname' => 'Doe',
            'balance' => 200.75,
        ];

        $response = $this->client->put("{$this->baseUrl}/api/customers/1", ['json' => $customerData]);
        $this->assertEquals(200, $response->getStatusCode());
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('surname', $result);
        $this->assertArrayHasKey('balance', $result);
        $this->assertEquals($result['balance'], 200.75);
    }

    public function testDeleteCustomer()
    {
        $response = $this->client->delete("{$this->baseUrl}/api/customers/1");
        $this->assertEquals(200, $response->getStatusCode());
        $result = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $result);
    }
}