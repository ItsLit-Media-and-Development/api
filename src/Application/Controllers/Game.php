<?php
/**
 * Game Endpoint
 *
 * @package        API
 * @author         Marc Towler <marc@marctowler.co.uk>
 * @copyright      Copyright (c) 2018 Marc Towler
 * @license        https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link           https://api.itslit.uk
 * @since          Version 1.0
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Game
{
	public $game;
	private $_db;
	private $_params;
	private $_output;
	private $_log;

	public function __construct()
	{
		$tmp = new Library\Router();
		$this->_db = new Model\CommunityModel();
		$this->_params = $tmp->getAllParameters();
		$this->_output = new Library\Output();
		$this->_log = new Library\Logger();
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
		$this->_log->set_message("Game::main() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

		return $this->_output->output(501, "Function not implemented", false);
	}

	/*public function get_points()
	{
		$this->_log->set_message("Community::get_points() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$botService = $this->_params[0];
		$channel = $this->_params[1];
		$type = $this->_params[2]; //Either channel or user
		$qParam = (isset($this->_params[3])) ? $this->_params[3] : 1000;
		$bot = (isset($this->_params[4])) ? $this->_params[4] : false;

		$this->_output->setOutput((isset($this->_params[5])) ? $this->_params[5] : NULL);

		switch($botService)
		{
			case 'streamelements':
				$this->game = new Library\Streamelements();


				$this->game->set_token($this->_db->get_SE_Token($channel));
				//$this->game->set_channel_id('599537d2d0cacf1582b82b0d');
				break;
			case 'streamlabs':
				$this->game = new Library\Streamlabs();

				$this->game->authorise($qParam);
				break;
		}

		$this->game->set_channel_id($channel);

		$output = ($type == 'channel') ? $this->game->get_channel_points($qParam) : $this->game->get_user_points($qParam);

		return ($output == false) ? $this->_output->output(400, "Unable to retrieve data, is the channel ID correct?", $bot) : $this->_output->output(200, $output, $bot);
	}*/
}