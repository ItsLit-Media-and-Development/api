<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 21/11/2018
 * Time: 18:10
 */

namespace API\Library;
use GuzzleHttp\Client;

abstract class BaseController
{
	protected $_params;
	protected $_output;
	protected $_log;
	protected $_router;
	protected $_auth;
	protected $_headers;
	protected $_guzzle;
	protected $_config;
	protected $_requstType;
	protected $_twitch;
	protected $_input;

	public function __construct()
	{
		$this->_config      = new Config();
		$this->_router      = new Router();
		$this->_params      = $this->_router->getAllParameters();
		$this->_output      = new Output();
		$this->_log         = new Logger();
		$this->_auth        = new Authentication();
		$this->_headers     = $this->_router->getAllHeaders();
		$this->_guzzle      = new Client();
		$this->_requestType = $this->_router->getRequestType();
		$this->_twitch      = new Twitch();
	}

	public function __destruct()
	{
		$this->_log->saveMessage();
	}

	/**
	 * Covers the router's default method incase a part of the URL was missed
	 *
	 * @return array|string
	 * @throws \Exception
	 */
	public function main()
	{
		//$this->_log->set_message("main() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

		return $this->_output->output(501, "Function not implemented", false);
	}

	public function authenticate()
	{
		if(!isset($this->_headers['user']) || ($this->_auth->validate_token($this->_headers['token'], $this->_headers['user'])['level'] != 4))
        {
			//No header, could be QS based. Lets see how big _params is and take the last value
			if($this->_params[sizeof($this->_params)-1][0] === '?')
			{
				$string = explode("&",ltrim($this->_params[sizeof($this->_params)-1], $this->_params[sizeof($this->_params)-1][0]));

				$this->_headers['user']  = explode("=", $string[0])[1];
				$this->_headers['token'] = explode("=", $string[1])[1];

				if(!isset($this->_headers['token']) || ($this->_auth->validate_token($this->_headers['token'], $this->_headers['user'])['level'] != 4))
        		{
					return false;
				} else {
					return true;
				}
			}
            $this->_log->set_message("Authentication failed", "ERROR");

			return false;
        } else {
			return true;
		}
	}

	public function validRequest($valid)
	{
		if($this->_requestType !== $valid)
        {
            $this->_log->set_message("Request received with invalid HTTP request type", "ERROR");

            return false;
        } else {
			return true;
		}
	}

	public function hasBody()
	{
		if($this->_requestType == 'PUT' || $this->_requestType == 'POST')
		{
			$this->_input = json_decode(file_get_contents('php://input'), true);

			if(empty($this->_input) || is_null($this->_input))
			{
				//this just means the post/put wasnt sent in the body, lets check to see if it is a multi-part form
				if(empty($_POST))
				{
					return false;
				}

				$this->_input = $_POST;
			}

			return true;
		} else {
			return false;
		}
	}
}