<?php
/**
 * Twitter Endpoint
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

class Twitter
{
	private $_db;
	private $_params;
	private $_output;
	private $_log;

	public function __construct()
	{
		$tmp = new Library\Router();
		$this->_params = $tmp->getAllParameters();
		$this->_output = new Library\Output();
		$this->_log = new Library\Logger();

	}

	public function __destruct()
	{
		$this->_log->saveMessage();
	}
}