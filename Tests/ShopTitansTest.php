<?php

use PHPUnit\Framework\TestCase;


class ShopTitansTest extends TestCase
{
    protected $client;

    public function setUp(): void
    {
        $this->client = new GuzzleHttp\Client(
            [
                'base_uri' => 'http://api'
            ]
        );
    }

    public function test_Put_Call_getAllUsers_Error()
    {
        $response = $this->client->put('/ShopTitans/getAllUsers', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoiZGlzY29yZF9ib3QiLCJsZXZlbCI6NH0'
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
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoiZGlzY29yZF9ib3QiLCJsZXZlbCI6NH0'
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
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoiZGlzY29yZF9ib3QiLCJsZXZlbCI6NH0'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertEquals($data['response'][0]['name'], "itsLittany");
    }
}
