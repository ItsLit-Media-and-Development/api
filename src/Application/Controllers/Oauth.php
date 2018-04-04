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
    private $_redirect_URI = 'https://api.itslit.uk/oauth/streamlabs/';
    private $_SL_URI = 'https://streamlabs.com/api/v1.0/';
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

        $res = $this->_guzzle->post($this->_SL_URI . 'token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                'client_id' => $this->_clientID,
                'client_secret' => $this->_clientSecret,
                    'redirect_uri' => $this->_redirect_URI,
                    'code' => $response
                ]]
        );

        if($res->getStatusCode() == 200)
        {
            $return = json_decode($res->getBody(), true);

            $user_get = $this->_guzzle->request('GET', $this->_SL_URI . 'user?access_token=' . $return['access_token']);
            $user_res = json_decode($user_get->getBody(), true);

            $user['streamlabs'] = $user_res['streamlabs']['display_name'];
            $user['twitch'] = $user_res['twitch'];

            $query = $this->_db->create_token($return['access_token'], $return['refresh_token'], $user);

            return (!is_bool($query)) ? $this->_output->output(400, $query, false) : ($query === true) ? $this->_output->output(201, "Streamlabs token is setup", false) : $this->_output->output(500, "Something went wrong, it is being looked into", false);
        }
        else
        {
            $this->_log->set_message("An error ocurred when calling Oauth::streamlabs()", "ERROR");

            return $this->_output->output(400, "Oops something went wrong", false);
        }

    }
}