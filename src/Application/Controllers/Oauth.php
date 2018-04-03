<?php
/**
 * OAuth Endpoint
 *
 * @package        API
 * @author        Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright    Copyright (c) 2018 Marc Towler
 * @license        https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link        https://api.itslit.uk
 * @since        Version 1.0
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model\OauthModel;
use GuzzleHttp\Client;

class Oauth
{
    private $_params;
    private $_output;
    private $_log;
    private $_clientID = 'vKoCYYMK2vcHO5yA4YUHupwSSnlJwP6VqnClL5HA';
    private $_clientSecret = 'FwU80a22PJOySV73xYO6hZGzJEjQayOqdpWh4v4n';
    private $_redirect_URI = 'https://api.itslit.uk/oauth/';
    private $_db;
    private $_guzzle;

    public function __construct()
    {
        $tmp = new Library\Router();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
        $this->_log = new Library\Logger();
        $this->_db = new OauthModel();
        $this->_guzzle = new Client();
    }

    public function __destruct()
    {
        $this->_log->saveMessage();
    }

    public function streamlabs()
    {
        $response = $_GET['code'];

        $res = $this->_guzzle->request('POST', 'https://streamlabs.com/api/v1.0/token',
            [
                'grant_type' => 'refresh_token',
                'client_id' => $this->_clientID,
                'client_secret' => $this->_clientSecret,
                'redirect_uri' => $this->_redirect_URI
            ]
        );

        var_dump($res->getBody());
    }
}