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

    public function tearDown(): void
    {
        $response = $this->client->put('/Blog/updatePost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json' => [
                'id'             => 1,
                'title'          => 'Test Title',
                'slug'           => 'test-title',
                'summary'        => 'this is the summary of a test blog post',
                'content'        => 'this is the summary of a test blog post, there will be much more to handle in this including markdown to html conversion but that might be more for the end user account.',
                'featured_image' => null,
                'updated_date'   => '2023-12-17 22:05:22',
                'published'      => 1
            ]
        ]);
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

    public function test_update_post_incorrect_verb()
    {
        $response = $this->client->get('/Blog/updatePost', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(405, $response->getStatusCode());
    }

    public function test_update_post_no_data()
    {
        $response = $this->client->put('/Blog/updatePost', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_update_post_summary()
    {
        $response = $this->client->put('/Blog/updatePost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json' => [
                'id'             => 1,
                'title'          => 'Test Title',
                'slug'           => 'test-title',
                'summary'        => 'this is the summary of a test blog post, updated',
                'content'        => 'this is the summary of a test blog post, there will be much more to handle in this including markdown to html conversion but that might be more for the end user account.',
                'featured_image' => null,
                'updated_date'   => '2023-12-17 22:05:22',
                'published'      => 1
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_post_summary_missing_info()
    {
        $response = $this->client->put('/Blog/updatePost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json' => [
                'id'             => 1,
                'title'          => 'Test Title',
                'slug'           => 'test-title',
                'summary'        => 'this is the summary of a test blog post, updated',
                'content'        => 'this is the summary of a test blog post, there will be much more to handle in this including markdown to html conversion but that might be more for the end user account.',
                'featured_image' => null,
                'published'      => 1
            ]
        ]);

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_post_list_size()
    {
        $response = $this->client->get('/Blog/listPosts', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $clean = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(2, count($clean['Data']));
    }

    public function test_create_post()
    {

    }

    public function test_approve_post()
    {

    }

    public function test_unapprove_post()
    {

    }

    public function test_create_comment()
    {

    }

    public function test_approve_comment()
    {

    }

    public function test_unapprove_comment()
    {

    }

    public function test_delete_comment()
    {

    }

    public function test_delete_post()
    {

    }
}
