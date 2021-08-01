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

    public function test_GetGcList()
    {
        $response = $this->client->get('/ShopTitans/getGcList', [
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

    public function test_AddToGcList_Wrong()
    {
        $response = $this->client->get('/ShopTitans/addToGcList', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ]
        ]);

        $this->assertEquals(405, $response->getStatusCode());
    }

    public function test_Add_To_Gc_List_No_Body()
    {
        $response = $this->client->post('/ShopTitans/addToGcList', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ],
            'form_params' => []
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_Add_To_Gc_List()
    {
        $response = $this->client->post('/ShopTitans/addToGcList', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ],
            'form_params' => [
                'building' => 'town hall',
                'user'     => 'ItsLittany'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_Mark_Complete()
    {
        $response = $this->client->put('/ShopTitans/markComplete', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ],
            'form_params' => []
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_get_event_info_no_event_specified()
    {
        $response = $this->client->get('/ShopTitans/geteventinfo', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_get_event_info_incorrect_event()
    {
        $response = $this->client->get('/ShopTitans/geteventinfo/fakeevent', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_get_event_info()
    {
        $response = $this->client->get('/ShopTitans/geteventinfo/caprice', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TEST_TOKEN']
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
