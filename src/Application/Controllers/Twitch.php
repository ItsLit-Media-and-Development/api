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
use API\Model\OauthModel;

class Twitch
{
	private $_params;
	private $_output;
	private $_log;
	private $_twitch;
	private $_db;

	public function __construct()
	{
		$tmp = new Library\Router();
		$this->_params = $tmp->getAllParameters();
		$this->_output = new Library\Output();
		$this->_log = new Library\Logger();
		$this->_twitch = new Library\Twitch();
		$this->_db = new OauthModel();
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

	/**
	 * Returns how long a user has followed the channel
	 *
	 * @return array
	 * @throws \API\Exceptions\InvalidIdentifierException
	 */
	public function followage()
	{
		$this->_log->set_message("Twitch::followage() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$user = $this->_twitch->get_user_id($this->_params[1]);

		$output = $this->_twitch->get('users/' . $user . '/follows/channels/' . $channel, false);

		return $this->_output->output(200, $this->_getDateDiff($output['created_at'], time(), 2), true);
	}

	/**
	 * Returns the channel's chat rules
	 *
	 * @return array
	 * @throws \API\Exceptions\InvalidIdentifierException
	 */
	public function getchatrules()
	{
		$this->_log->set_message("Twitch::getchatrules() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$bot = (isset($this->_params[1]) ? $this->_params[1] : false);

		$output = $this->_twitch->get('api/channels/' . $channel . '/chat_properties', false);

		return $this->_output->output(200, $output['chat_rules'], $bot);
	}

	/**
	 * Returns how old a user's account is
	 *
	 * @return array
	 * @throws \API\Exceptions\InvalidIdentifierException
	 */
	public function howold()
	{
		$this->_log->set_message("Twitch::howold() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$user = $this->_twitch->get_user_id($this->_params[0]);
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		return $this->_output->output(200, $this->_getDateDiff($this->_users($user)['created_at'], time(), 2), $bot);
	}

	public function recentfollowers()
	{
		$this->_log->set_message('Twitch::recentfollowers() was called from ' . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$limit = $this->_params[1];
		$dir = isset($this->_params[2]) ? $this->_params[2] : 'desc';
		$bot = (isset($this->_params[3]) ? $this->_params[3] : false);

		$output = $this->_twitch->get(sprintf('channels/%s/follows?limit=%d&offset=0&direction=%s', $channel, $limit, $dir));

		return $this->_output->output(200, $output, $bot);
	}

	/**
	 * Allows a user to create a clip via command to edit it later
	 *
	 * @return array|string URL of clip
	 * @throws \API\Exceptions\InvalidIdentifierException
	 */
	public function clip()
	{
		$this->_log->set_message('Twitch::clip() was called from ' . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$user = $this->_authorise($this->_params[1]);
		$bot = isset($this->_params[2]) ? $this->_params[2] : false;

		$output = $this->_twitch->post('https://api.twitch.tv/helix/clips?broadcaster_id=' . $channel, ['Authorization' => 'Bearer ' . $user]);

		return $this->_output->output(200, $output['edit_url'], $bot);
	}

	public function randomsub()
	{
		$this->_log->set_message("Twitch::randomsub() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

		return $this->_output->output(501, "Function not implemented", false);
	}

	public function randomuser()
	{
		$this->_log->set_message("Twitch::randomuser() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

		return $this->_output->output(501, "Function not implemented", false);
	}

	/**
	 * Returns the emote URL's available to a channel
	 *
	 * @return array
	 */
	public function subemotes()
	{
		$this->_log->set_message("Twitch::subemotes() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_params[0];
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		$output = $this->_twitch->get('chat/' . $channel . '/emoticons', false, ['nover' => true]);

		return $this->_output->output(200, $output, $bot);
	}

	public function status()
	{

	}

	public function totalviews()
	{
		$this->_log->set_message("Twitch::totalviews() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		$output = $this->_twitch->get('channels/' . $channel);

		return $this->_output->output(200, $output['views'], $bot);
	}

	public function viewercount()
	{
		$this->_log->set_message("Twitch::viercount() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_params[0];
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		$output = $this->_twitch->get('https://tmi.twitch.tv/group/user/' . $channel . '/chatters', true);

		return $this->_output->output('200', $output['chatter_count'], $bot);
	}

	private function _users($userid)
	{
		return $this->_twitch->get('users/' . $userid);
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

	private function _authorise($user)
	{
		$output = $this->_db->authorize($user, 'twitch');

		if($output === false) {
			header('Location: https://id.twitch.tv/oauth2/authorize?client_id=6ewkckds8dw9unp55rmfk0w8opnh2f&redirect_uri=https://api.itslit.co.uk/oauth/twitch/&response_type=token&scope=bits:read clips:edit user:edit channel_check_subscription channel_subscriptions user:read');
		}

		return $output;
	}
}