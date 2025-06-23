<?php

use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
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

        $buildTicket = $this->db->prepare("CREATE TABLE `ticket` (`id` int NOT NULL, `name` varchar(80) NOT NULL, `email` varchar(200) NOT NULL, `message` text NOT NULL, `status` tinyint NOT NULL DEFAULT '0', `submitted_at` datetime NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");
        $buildTicket->execute();

        $insTicket = $this->db->prepare("INSERT INTO `ticket` (`id`, `name`, `email`, `message`, `status`, `submitted_at`) VALUES (1, 'Bob Belcher', 'bob@bobsburgers.com', 'I like burgers', 1, '2023-12-18 21:05:20')");
        $insTicket->execute();

        $idxTicket = $this->db->prepare("ALTER TABLE `ticket` ADD PRIMARY KEY (`id`), MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2");
        $idxTicket->execute();
    }

    public function tearDown(): void
    {
        $delTicket = $this->db->prepare("DROP TABLE `ticket`");
        $delTicket->execute();
    }

    public function test_list_tickets_not_empty()
    {
        $response = $this->client->get('/ticket/listTickets', [
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_add_ticket()
    {
        $response = $this->client->post('/ticket/createTicket',[
            'http_errors' => true,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'name'    => 'Bob Belcher',
                'email'   => 'bob@bobsburgers.com',
                'message' => 'I like burgers'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_view_ticket()
    {
        $response = $this->client->get('/Ticket/viewTicket/1', [
            'http_errors' => true,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ]
        ]);

        $clean = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('Data', $clean);
        $this->assertArrayHasKey('name', $clean['Data']);
        $this->assertEquals('Bob Belcher', $clean['Data']['name']);
        $this->assertArrayHasKey('email', $clean['Data']);
        $this->assertEquals('bob@bobsburgers.com', $clean['Data']['email']);
        $this->assertArrayHasKey('message', $clean['Data']);
        $this->assertEquals('I like burgers', $clean['Data']['message']);
    }

    public function test_mark_ticket_as_read()
    {
        $response = $this->client->patch('/Ticket/toggleStatus',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'id'     => 1,
                'status' => 1
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function test_mark_ticket_as_unread()
    {
        $response = $this->client->patch('/Ticket/toggleStatus',[
            'http_errors' => false,
            'headers' => [
                'user'  => 'discord_bot',
                'token' => $this->config['TOKEN']
            ],
            'json'    => [
                'id'     => 1,
                'status' => 0
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_ticket()
    {
        $response = $this->client->delete('/Ticket/deleteTicket/1',[
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