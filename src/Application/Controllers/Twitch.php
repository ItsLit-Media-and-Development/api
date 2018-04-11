<?php
/**
 * Twitch Endpoint
 *
 * @package       API
 * @author        Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright     Copyright (c) 2018 Marc Towler
 * @license       https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link          https://api.itslit.uk
 * @since         Version 1.0
 * @filesource
 */

namespace API\Controllers;

use API\Library;

class Twitch
{
	private $_params;
	private $_output;
	private $_log;
	private $_twitch;

	public function __construct()
	{
		$tmp = new Library\Router();
		$this->_params = $tmp->getAllParameters();
		$this->_output = new Library\Output();
		$this->_log = new Library\Logger();
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
		$this->_log->set_message("Twitch::main() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

		return $this->_output->output(501, "Function not implemented", false);
	}

	public function followage()
	{
		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$user = $this->_twitch->get_user_id($this->_params[1]);

		$output = $this->_twitch->get('users/' . $user . '/follows/channels/' . $channel, false);

		return $this->_output->output(200, $this->_getDateDiff($output['created_at'], time(), 2), false);
	}

	public function getchatrules()
	{

	}

	public function howold()
	{

	}

	public function recentfollowers()
	{

	}

	public function highlight()
	{

	}

	public function randomsub()
	{

	}

	public function randomuser()
	{

	}

	public function subemotes()
	{

	}

	public function status()
	{

	}

	public function totalviews()
	{

	}

	public function viewercount()
	{

	}

	private function _getDateDiff($time1, $time2, $precision = 2)
	{
		if($precision === 0) {
			$precision = 2;
		}
		// If not numeric then convert timestamps
		if(!is_int($time1)) {
			$time1 = strtotime($time1);
		}
		if(!is_int($time2)) {
			$time2 = strtotime($time2);
		}
		// If time1 > time2 then swap the 2 values
		if($time1 > $time2) {
			list($time1, $time2) = array($time2, $time1);
		}
		// Set up intervals and diffs arrays
		$intervals = array('year', 'month', 'week', 'day', 'hour', 'minute', 'second');
		$diffs = array();
		foreach($intervals as $interval) {
			// Create temp time from time1 and interval
			$ttime = strtotime('+1 ' . $interval, $time1);
			// Set initial values
			$add = 1;
			$looped = 0;
			// Loop until temp time is smaller than time2
			while($time2 >= $ttime) {
				// Create new temp time from time1 and interval
				$add++;
				$ttime = strtotime("+" . $add . " " . $interval, $time1);
				$looped++;
			}
			$time1 = strtotime("+" . $looped . " " . $interval, $time1);
			$diffs[$interval] = $looped;
		}
		$count = 0;
		$times = array();
		foreach($diffs as $interval => $value) {
			// Break if we have needed precission
			if($count >= $precision) {
				break;
			}
			// Add value and interval if value is bigger than 0
			if($value > 0) {
				if($value != 1) {
					$interval .= "s";
				}
				// Add value and interval to times array
				$times[] = $value . " " . $interval;
				$count++;
			}
		}
		// Return string with times
		return implode(", ", $times);
	}
}