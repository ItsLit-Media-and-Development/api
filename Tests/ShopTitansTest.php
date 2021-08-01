<?php

use PHPUnit\Framework\TestCase;


class ShopTitansTest extends TestCase
{
    protected $client;
    protected $config;

    public function setUp(): void
    {
        $this->config = parse_ini_file("src/Config/site.ini");

        $this->client = new GuzzleHttp\Client(
            [
                'base_uri' => $this->config['BASE_URL']
            ]
        );
    }

    public function test_Put_Call_getAllUsers_Error()
    {
        $response = $this->client->put('/ShopTitans/getAllUsers', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ]
        ]);

        $this->assertEquals(405, $response->getStatusCode());
    }

    public function test_getAllUsers()
    {
        $response = $this->client->get('/ShopTitans/getAllUsers', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertIsArray($data);
    }
    
    public function test_failAuthentication()
    {
        $response = $this->client->get('/ShopTitans/getAllUsers', [
            'http_errors' => false
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_GetUserItsLittany()
    {
        $response = $this->client->get('/ShopTitans/getUser/ItsLittany', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertEquals($data[0]['name'], "itslittany");
    }

    public function test_GetInvestmentItsLittany()
    {
        $response = $this->client->get('/ShopTitans/getInvestment/ItsLittany', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertEquals($data['worth'], "1");
        $this->assertEquals($data['investment'], "1");
    }
}
