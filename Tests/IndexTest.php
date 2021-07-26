<?php

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
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

    public function test_Get_Index_Not_Implemented()
    {
        $response = $this->client->get('/', [
            'http_errors' => false
        ]);

        $this->assertEquals(501, $response->getStatusCode());
    }
}