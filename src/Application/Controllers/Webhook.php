<?php
/**
 * Webhook Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2019 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 1.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;
use GuzzleHttp\Client;


class Webhook extends Library\BaseController
{
    private $_db;
    private $_config;
    private $_guzzle;
    private $_twitch;

    public function __construct()
    {
		parent::__construct();

        $this->_config = new Library\Config();
        $this->_db     = new Model\UserModel();
        $this->_guzzle = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));
    }

    public function create_sub()
    {
        $this->_log->set_message("Webhook::create_sub() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $type  = $this->_params[0];
        $user  = $this->_params[1];
        $topic = $this->_params[2];
        $param = $this->_params[3];

        switch($topic)
        {
            case 'follows':
                $topic = 'https://api.twitch.tv/helix/users/follows';
                break;
            case 'streams':
                $topic = 'https://api.twitch.tv/helix/streams';
                break;
            case 'users':
                $topic = 'https://api.twitch.tv/helix/users';
                break;
            case 'extensions':
                $topic = 'https://api.twitch.tv/helix/extensions/transactions';
                break;
            default:
                return $this->_output->output(400, "Unknown topic $topic", false);
        }

        $body = [
            'hub.callback' => $this->_config->getSettings('BASE_URL') . "Webhook/receive/$type/$user",
            'hub.mode'     => 'subscribe',
            'hub.topic'    => $topic . "?$param",
            'hub.lease_seconds' => 864000,
            'hub.secret'        => $this->_config->getSettings('WEBHOOK_SECRET')
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Client-ID'    => $this->_config->getSettings('CLIENT_ID')
        ];
        
        $response = $this->_guzzle->request('POST', 'https://api.twitch.tv/helix/webhooks/hub', ['headers' => [ 'Client-ID' => $this->_config->getSettings('CLIENT_ID') ], 'json' => $body]);
    }

    public function receive()
    {
        //lets check to see if there is a get
        if(isset($_GET))
        {
	        $data = '';

            foreach ($_GET as $key => $val)
            {
                $data .= "$key = $val,";
            }

            file_put_contents("text.txt", $data);

            return $this->_output->output(200, $_GET['hub_challenge'], false);
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
        }

    }
}