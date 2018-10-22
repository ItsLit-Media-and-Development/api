<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 18/06/2018
 * Time: 19:38
 */

namespace API\Controllers;

use API\Library;
<<<<<<< HEAD
=======
use API\Model\OauthModel;
>>>>>>> origin/Nightly

class Clips
{
	private $_params;
	private $_output;
	private $_log;
	private $_twitch;

	public function __construct()
	{
<<<<<<< HEAD
		$tmp = new Library\Router();
		$this->_params = $tmp->getAllParameters();
		$this->_output = new Library\Output();
		$this->_log = new Library\Logger();
=======
		$tmp           = new Library\Router();
		$this->_params = $tmp->getAllParameters();
		$this->_output = new Library\Output();
		$this->_log    = new Library\Logger();
>>>>>>> origin/Nightly
		$this->_twitch = new Library\Twitch();
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
		$this->_log->set_message("Clips::main() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

		return $this->_output->output(501, "Function not implemented", false);
	}

	public function get_clips()
	{
		$this->_log->set_message("Clips::get_clips() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$username = $this->_params[0];
<<<<<<< HEAD
		$limit = isset($this->_params[1]) ? $this->_params[1] : 100;
		$period = isset($this->_params[2]) ? $this->_params[2] : 'all';
		$cursor = isset($this->_params[3]) ? $this->_params[3] : NULL;

		$url = is_null($cursor) ? "clips/top?channel=$username&limit=$limit&period=$period" :
			"clips/top?channel=$username&limit=$limit&period=$period&cursor=$cursor";
=======
		$limit    = isset($this->_params[1]) ? $this->_params[1] : 100;
		$period   = isset($this->_params[2]) ? $this->_params[2] : 'all';
		$cursor   = isset($this->_params[3]) ? $this->_params[3] : NULL;

		$url    = is_null($cursor) ? "clips/top?channel=$username&limit=$limit&period=$period" : "clips/top?channel=$username&limit=$limit&period=$period&cursor=$cursor";
>>>>>>> origin/Nightly

		$output = $this->_twitch->get($url);

		return $this->_output->output(200, $output, false);
	}
}