<?php
/**
 * Clips Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.0
 * @filesource
 */
namespace API\Controllers;
use API\Library;

class Clips
{
	private $_params;
	private $_output;
	private $_log;
	private $_twitch;

	public function __construct()
	{
		$tmp           = new Library\Router();
		$this->_params = $tmp->getAllParameters();
		$this->_output = new Library\Output();
		$this->_log    = new Library\Logger();
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
		$limit = isset($this->_params[1]) ? $this->_params[1] : 100;
		$period = isset($this->_params[2]) ? $this->_params[2] : 'all';
		$cursor = isset($this->_params[3]) ? $this->_params[3] : NULL;
		$url = is_null($cursor) ? "clips/top?channel=$username&limit=$limit&period=$period" :
			"clips/top?channel=$username&limit=$limit&period=$period&cursor=$cursor";

		$output = $this->_twitch->get($url);

		return $this->_output->output(200, $output, false);
	}
}