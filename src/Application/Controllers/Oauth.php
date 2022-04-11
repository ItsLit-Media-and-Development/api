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

class Oauth extends Library\BaseController
{
  	private $_SLclientID;
	private $_SLclientSecret;
	private $_redirect_URI = 'https://api.itslit.uk/oauth/streamlabs/';
	private $_twitch_redirect = 'https://api.itslit.uk/Oauth/twitch/';
	private $_SL_URI = 'https://streamlabs.com/api/v1.0/';
    private $_db;
    protected $_guzzle;

    public function __construct()
    {
		parent::__construct();

		$this->_db = new OauthModel();
        $this->_guzzle = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));

		$this->_SLclientID = $this->_config->getSettings('SL_CLIENT_ID');
        $this->_SLclientSecret = $this->_config->getSettings('SL_SECRET');
    }

    public function streamlabs()
    {
        $response = $_GET['code'];

        $res = $this->_guzzle->post($this->_SL_URI . 'token', [
                'form_params' => [
					'grant_type'    => 'authorization_code',
					'client_id'     => $this->_SLclientID,
					'client_secret' => $this->_SLclientSecret,
					'redirect_uri'  => $this->_redirect_URI,
					'code'          => $response
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

	public function twitch()
	{
		if(isset($this->_params[0])) {
			//We know it is an internal request!
			$query = $this->_db->authorize($this->_params[0], 'twitch');

			return ($query != false) ? $query : $this->_guzzle->get('https://api.twitch.tv/oauth/twitch');
		}

		$parameters = [
			'client_id'     => $this->_config->getSettings('CLIENT_ID'),
			'client_secret' => $this->_config->getSettings('TWITCH_SECRET'),
			'redirect_uri'  => $this->_twitch_redirect,
			'code'          => $_GET['code'],
			'grant_type'    => 'authorization_code'
		];

		$response = $this->_guzzle->request('POST', 'https://id.twitch.tv/oauth2/token', ['form_params' => $parameters]);

		$response = json_decode($response->getBody(), true);

		return $response['access_token'];
	}

	public function Github()
	{
		//echo($this->_config->getSettings('GITHUB_CLIENT_ID') . "<br />" .$this->_config->getSettings('GITHUB_CLIENT_SECRET'));die;

		if(isset($this->_params[0]) && substr($this->_params[0], 0, 6) == '?code=')
		{
			$code = str_replace('?code=', '', $this->_params[0]);

			$parameters = [
				'client_id'		 => $this->_config->getSettings('GITHUB_CLIENT_ID'),
				'client_secret' => $this->_config->getSettings('GITHUB_CLIENT_SECRET'),
				'code'			 => $code,
				'response_uri'	 => $this->_config->getSettings('GITHUB_CALLBACK')
			];

			//$response = $this->_guzzle->request('POST', 'https://github.com/login/oauth/access_token', ['headers' => ['Accept' => 'application/json'], 'body' => $parameters]);
			$response = $this->_guzzle->post('https://github.com/login/oauth/access_token', [
				'headers' => [
					'Accept' => 'application/json',
					'Content-Type' => 'application/x-www-form-urlencoded'
				], 
				'form_params' => $parameters
			]);

			$response = json_decode($response->getBody(), true);

			var_dump($response);die;
		} else {
			var_dump($this->_params[0]);die;
		}
	}
}