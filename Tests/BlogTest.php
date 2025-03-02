<?php

use PHPUnit\Framework\TestCase;

class BlogTest extends TestCase
{
    protected $client;
    protected $config;

    public function setUp(): void
    {
        $this->config = parse_ini_file("src/Config/site.ini");

        $this->client = new GuzzleHttp\Client(
            [
                'base_uri' => 'http://api.local'
            ]
        );
    }

    public function test_get_post_by_ID_auth_failure()
    {
        $response = $this->client->get('/Blog/getPostByID/1', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'test',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_get_post_by_ID()
    {
        $response = $this->client->get('/Blog/getPostByID/1', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $clean = json_decode($response->getBody()->getContents(), true);

        $this->assertArrayHasKey('Data', $clean);
        $this->assertArrayHasKey('post', $clean['Data']);
        $this->assertEquals('Test Title', $clean['Data']['post']['title']);
        $this->assertArrayHasKey('display_name', $clean['Data']['post']['comments'][0]);
        $this->assertEquals('Test User', $clean['Data']['post']['comments'][0]['display_name']);
        $this->assertArrayHasKey('tag_name', $clean['Data']['post']['tags'][0]);
        $this->assertEquals('tes', $clean['Data']['post']['tags'][0]['tag_name']);
    }

    public function test_get_post_by_ID_with_string()
    {
        $response = $this->client->get('/Blog/getPostByID/test', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_get_post_by_Slug()
    {
        $response = $this->client->get('/Blog/getPostBySlug/test-title', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $clean = json_decode($response->getBody()->getContents(), true);

        $this->assertArrayHasKey('Data', $clean);
        $this->assertArrayHasKey('post', $clean['Data']);
        $this->assertEquals('Test Title', $clean['Data']['post']['title']);
        $this->assertArrayHasKey('display_name', $clean['Data']['post']['comments'][0]);
        $this->assertEquals('Test User', $clean['Data']['post']['comments'][0]['display_name']);
        $this->assertArrayHasKey('tag_name', $clean['Data']['post']['tags'][0]);
        $this->assertEquals('tes', $clean['Data']['post']['tags'][0]['tag_name']);
    }

    public function test_get_post_by_slug_invalid()
    {
        $response = $this->client->get('/Blog/getPostBySlug/invalid-title', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
