<?php

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    protected $client;
    protected $config;

    public function setUp(): void
    {
        $this->config = parse_ini_file("src/Config/site.ini");

        $this->client = new GuzzleHttp\Client(
            [
                'base_uri' => 'http://api'
            ]
        );
    }

    public function test_Get_Index_Not_Implemented()
    {
        $response = $this->client->get('/', [
            'http_errors' => false
        ]);

        $this->assertEquals(501, $response->getStatusCode());
    }

    public function test_Config_Is_Loadable()
    {
        $this->assertFileExists('src/Config/site.ini');
        $this->assertIsArray($this->config);
    }

    public function test_Config_Has_Token_Key()
    {
        $this->assertArrayHasKey('TEST_TOKEN', $this->config);
    }
}