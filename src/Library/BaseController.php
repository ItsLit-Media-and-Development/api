<?php
/**
 * Base Controller
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 0.1
 * @filesource
 */

namespace API\Library;
use GuzzleHttp\Client;
use API\Library\Exceptions;

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

	/**
	 * Authenticate that the requestor has the right level of permission to access the resource.
	 * 
	 * @param int $level The level needed for access.
	 * 
	 * @return bool authorised or not authorised.
	 */
	protected function authenticate($level)
	{
		//Are we using Header based authentication?
		if(!isset($this->_headers['user']))
		{
			//Check to see if Params is set or user header and if not, check header for key/token
			if($this->_params === false && !isset($this->_headers['user']))
			{
				if(isset($this->_headers['token']) && strlen($this->_headers['token']) > 20)
				{
					$auth = explode('/', $this->_headers['token']);

					$this->_headers['user']  = $auth[0];
					$this->_headers['token'] = $auth[1];

					//Lets see if this is a valid token
					if($this->_auth->validate_token($this->_headers['token'], $this->_headers['user'])['auth_level'] < $level)
					{
						//They have a lower value token level
						try {
							$tmp = new Exceptions\WrongAuthLevelException("User {$this->_headers['user']} at level {$level} was insufficient.");
						} catch(\Exception $e) {
							echo "Default exception";
						}

						return false;
					} else {
						return true;
					}
				} else {
					try {
						//Unknown how they are authenticating
						$tmp = new Exceptions\InvalidTokenException('Unknown attempt at authentication', 2);
					} catch(\Exception $e) {
						echo "Default exception";
					}
					
					return false;
				}
			}
			else
			{
				//Looks like it could be QS based, lets see how big _params is and take the last value
				if($this->_params[sizeof($this->_params)-1][0] === '?')
				{
					$string = explode("&", ltrim($this->_params[sizeof($this->_params) - 1], $this->_params[sizeof($this->_params) - 1][0]));
					//Do we have 2 values in string?
					if(sizeof($string) == 2)
					{
						$this->_headers['user']  = explode("=", $string[0])[1];
						$this->_headers['token'] = explode("=", $string[1])[1];
						
						//Lets see if this is a valid token
						if($this->_auth->validate_token($this->_headers['token'], $this->_headers['user'])['auth_level'] < $level)
						{
							//They have a lower value token level
							return false;
						} else {
							return true;
						}
					} else {
						try {
							//Unknown how they are authenticating
							$tmp = new Exceptions\InvalidTokenException('Unknown attempt at authentication', 2);
						} catch(\Exception $e) {
							echo "Default exception";
						}
						
						return false;
					}
				} else {
					try {
						//Unknown how they are authenticating
						$tmp = new Exceptions\InvalidTokenException('Unknown attempt at authentication', 2);
					} catch(\Exception $e) {
						echo "Default exception";
					}
					
					return false;
				}
			}
		} else {
			//We are doing header based, lets check there is a token and authenticate
			if(!isset($this->_headers['token']))
			{
				return false;
			}
			
			return ($this->_auth->validate_token($this->_headers['token'], $this->_headers['user'])['auth_level'] >= $level) ? true : false;
		}
	}

	protected function expectedVerb($valid)
	{
		if($this->_requestType !== $valid)
        {
            //$this->_log->set_message("Request received with invalid HTTP request type", "ERROR");

            return false;
        } else {
			return true;
		}
	}

	protected function hasBody()
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