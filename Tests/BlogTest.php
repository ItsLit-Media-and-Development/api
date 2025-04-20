<?php

use PHPUnit\Framework\TestCase;

class BlogTest extends TestCase
{
    protected $client;
    protected $config;
    protected $db;

    public function setUp(): void
    {
        $this->config = parse_ini_file("src/Config/site.ini");

        $this->client = new GuzzleHttp\Client(
            [
                'base_uri' => 'http://api.local'
            ]
        );

        $this->database();

        $buildPost = $this->db->prepare("CREATE TABLE `blog_post` (`id` int NOT NULL,`title` varchar(45) NOT NULL,`slug` varchar(45) NOT NULL,`summary` varchar(100) NOT NULL,`content` longtext NOT NULL,`featured_image_url` varchar(100) DEFAULT NULL,`published_date` datetime DEFAULT CURRENT_TIMESTAMP,`updated_date` datetime DEFAULT CURRENT_TIMESTAMP,`published` tinyint DEFAULT '0') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");
        $buildPost->execute();

        $insPost = $this->db->prepare("INSERT INTO `blog_post` (`id`, `title`, `slug`, `summary`, `content`, `featured_image_url`, `published_date`, `updated_date`, `published`) VALUES (1, 'Test Title', 'test-title', 'this is the summary of a test blog post', 'this is the summary of a test blog post, there will be much more to handle in this including markdown to html conversion but that might be more for the end user account.', NULL, '2023-12-17 22:05:22', '2023-12-17 22:05:22', 1), (2, 'Second Test', 'second-test', 'this is just a second test post', 'this is the body of the second test post', NULL, '2023-12-28 23:37:03', '2023-12-28 23:37:03', 0)");
        $insPost->execute();

        $idxPost = $this->db->prepare("ALTER TABLE `blog_post` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `title_UNIQUE` (`title`), ADD UNIQUE KEY `slug_UNIQUE` (`slug`), MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3");
        $idxPost->execute();

        $buildTag = $this->db->prepare("CREATE TABLE `blog_tags` (`post_id` int NOT NULL, `tag_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");
        $buildTag->execute();

        $insTag = $this->db->prepare("INSERT INTO `blog_tags` (`post_id`, `tag_name`) VALUES (1, 'tes'), (1, 'testing'), (1, 'totally_testing'), (2, 'testing')");
        $insTag->execute();

        $idxTag = $this->db->prepare("ALTER TABLE `blog_tags` ADD KEY `post_id` (`post_id`)");
        $idxTag->execute();

        $buildComments = $this->db->prepare("CREATE TABLE `blog_comments` (`id` int NOT NULL, `bid` int NOT NULL, `response_id` int NOT NULL DEFAULT '0', `display_name` varchar(45) NOT NULL, `email` varchar(100) NOT NULL, `comment` mediumtext, `posted_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `approved` tinyint NOT NULL DEFAULT '0', `deleted` tinyint NOT NULL DEFAULT '0') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");
        $buildComments->execute();

        $insComments = $this->db->prepare("INSERT INTO `blog_comments` (`id`, `bid`, `response_id`, `display_name`, `email`, `comment`, `posted_on`, `approved`, `deleted`) VALUES (1, 1, 0, 'Test User', 'test@user.com', 'this is a test comment', '2023-12-18 21:05:20', 0, 0)");
        $insComments->execute();

        $idxComments = $this->db->prepare("ALTER TABLE `blog_comments` ADD PRIMARY KEY (`id`), MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2");
        $idxComments->execute();
    }

    public function tearDown(): void
    {
        $delPost = $this->db->prepare("DROP TABLE `blog_post`");
        $delPost->execute();

        $delTag = $this->db->prepare("DROP TABLE `blog_tags`");
        $delTag->execute();

        $delComments = $this->db->prepare("DROP TABLE `blog_comments`");
        $delComments->execute();
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
        $response = $this->client->post('/Blog/createPost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'title'          => 'Temp Title',
                'slug'           => 'temp-title',
                'summary'        => 'this is the summary of a temp blog post',
                'content'        => 'this is the summary of a temp blog post, there will be much more to handle in this including markdown to html conversion but that might be more for the end user account.',
                'featured_image' => null,
                'published'      => 0
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_create_post_no_data()
    {
        $response = $this->client->post('/Blog/createPost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_approve_post()
    {
        $response = $this->client->patch('/Blog/approvePost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'id' => 3
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_approve_post_no_id()
    {
        $response = $this->client->patch('/Blog/approvePost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_approve_post_string_id()
    {
        $response = $this->client->patch('/Blog/approvePost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'id' => "three"
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_unapprove_post()
    {
        $response = $this->client->patch('/Blog/unapprovePost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'id' => 3
            ]
        ]);
        
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_unapprove_post_no_id()
    {
        $response = $this->client->patch('/Blog/unapprovePost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_unapprove_post_string_id()
    {
        $response = $this->client->patch('/Blog/unapprovePost',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'id' => "three"
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_get_list_of_approved_posts()
    {
        $response = $this->client->get('/Blog/listPosts/1', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $clean = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(1, count($clean['Data']));
    }

    public function test_get_approved_posts_by_tag()
    {
        $response = $this->client->get('/Blog/listPostsByTag/1/testing', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $clean = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(9, count($clean['Data'])); //9 items returned for 1 post
    }

    public function test_create_comment()
    {
        $response = $this->client->post('/Blog/createComment',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'post_ID'        => 2,
                'display_name'   => 'temp name',
                'email'          => 'test@api.local',
                'comment'        => 'This is a test comment',
                'approved'       => 0
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_create_comment_no_data()
    {
        $response = $this->client->post('/Blog/createComment',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_approve_Comment()
    {
        $response = $this->client->patch('/Blog/approveComment',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'id' => 2
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_approve_Comment_no_id()
    {
        $response = $this->client->patch('/Blog/approveComment',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_approve_Comment_string_id()
    {
        $response = $this->client->patch('/Blog/approveComment',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'id' => "three"
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_unapprove_comment()
    {
        $response = $this->client->patch('/Blog/unapproveComment',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'id' => 2
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_comment()
    {
        $response = $this->client->delete('/Blog/deleteComment/2',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function test_delete_post_invalid_id()
    {
        $response = $this->client->delete('/Blog/deletePost/4',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_delete_post()
    {
        $response = $this->client->delete('/Blog/deletePost/2',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(204, $response->getStatusCode());
    } 

    /**
     * Connects to the database
     *
     * @param string $override Optional allows temporary settings change
     * @return object|\PDO The database connection object
     * @throws \Exception on missing settings
     */
    public function database($override = '')
    {
        if(!is_object($this->db))
        {
            if(!isset($this->config['DBHOST']))
            {
                throw new \Exception("config::database needs settings, check your config");
            } else {
                $this->db = new \PDO("mysql:host=" . $this->config['DBHOST'] . ";port=" . $this->config['PORT'] .
                    ";dbname=" . $this->config['DBNAME'], $this->config['DBUSER'], $this->config['DBPASS']);
                $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
        } else {
            $this->db = new \PDO("mysql:host=" . $override['DBHOST'] . ";port=" . $override['PORT'] .
                ";dbname=" . $override['DBNAME'], $override['DBUSER'], $override['DBPASS']);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return $this->db;
    }
}
