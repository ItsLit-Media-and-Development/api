<?php
/**
 * Twitch Endpoint
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
use API\Model\CommunityModel;
use API\Model\OauthModel;

class Twitch extends Library\BaseController
{
	private $_twitch;
	private $_db;
	private $_db2;

	public function __construct()
	{
		parent::__construct();

		$this->_twitch = new Library\Twitch();
		$this->_db = new OauthModel();
		$this->_db2 = new CommunityModel();
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

		if(isset($output['created_at'])) {
			return $this->_output->output(200, $this->_getDateDiff($output['created_at'], time(), 2), true);
		} else {
			return $this->_output->output(404, "{$this->_params[1]} does not follow {$this->_params[0]}", true);
		}
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

	public function totalfollowers()
	{
		$this->_log->set_message('Twitch::totalfollowers() was called from ' . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		$output = $this->_twitch->get('channels/' . $channel);

		return $this->_output->output(200, $output['followers'], $bot);
	}

	public function randomsub()
	{
		$this->_log->set_message("Twitch::randomsub() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

		return $this->_output->output(501, "Function not implemented", false);
	}

	public function randomuser()
	{
		$this->_log->set_message("Twitch::randomuser() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$users = [];
		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		$data = $this->_twitch->get('https://tmi.twitch.tv/group/user/' . $channel . '/chatters', true);

		foreach($data['chatters'] as $group => $chatters) {
			$users = array_merge($users, $chatters);
		}

		shuffle($users);
		$rand = mt_rand(0, count($users) - 1);

		return $this->_output->output(200, $users[$rand], $bot);
	}

	/**
	 * Returns the emote URL's available to a channel
	 *
	 * @TODO clean up the array output
	 *
	 * @return array
	 */
	public function emotes()
	{
		$this->_log->set_message("Twitch::emotes() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_params[0];
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		$output = $this->_twitch->get('chat/' . $channel . '/emoticons', false, ['nover' => true]);

		return $this->_output->output(200, $output, $bot);
	}

	public function subemotes()
	{
		$this->_log->set_message("Twitch::subemotes() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_params[0];
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		$output = $this->_twitch->get('https://api.twitch.tv/api/channels/' . $channel . '/product', true);

		return $this->_output->output(200, $output['plans'], $bot);
	}

	public function subbadges()
	{
		$this->_log->set_message("Twitch::subbadges() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		$output = $this->_twitch->get("https://badges.twitch.tv/v1/badges/channels/$channel/display?language=en", true, ['nover' => true]);

		return $this->_output->output(200, $output['badge_sets']['subscriber']['versions'], $bot);
	}

	public function status()
	{
		$this->_log->set_message("Twitch::status() called from " . $_SERVER['REMOTE_ADDR'], "INFO");
		$response = [];

		$channel = $this->_twitch->get_user_id($this->_params[0]);

		if(!$channel) {
			return $this->_output->output(400, ['error' => "Username " . $this->_params[0] . " does not exist"], false);
		}

		$output = $this->_twitch->get("https://api.twitch.tv/helix/streams?user_id=$channel", true);

		if(!empty($output['data'][0]))
		{
			$response['username'] = $output['data'][0]['user_name'];
			$response['title']    = $output['data'][0]['title'];
			$response['image']    = str_replace('{height}', '180', str_replace('{width}', '300', $output['data'][0]['thumbnail_url']));
		}


		return $this->_output->output(200, $response, false);

	}

	public function recent_vods()
	{
		$this->_log->set_message("Twitch::recent_vods() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$limit   = isset($this->_params[1]) ? $this->_params[1] : 10;
		$bot     = isset($this->_params[2]) ? $this->_params[2] : false;

		/* DEPRICATED
		$output = $this->_twitch->get("https://api.twitch.tv/kraken/channels/$channel/videos?limit=$limit&broadcasts=true", true, ['Accept' => 'application/vnd.twitchtv.v3+json']);
		*/
		$output = $this->_twitch->get("https://api.twitch.tv/helix/videos?user_id=$channel&type=archive&first=$limit", true);

		return $this->_output->output(200, $output['data'], $bot);
	}

	public function current_game()
	{
		$this->_log->set_message("Twitch::current_game() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		$output = $this->_twitch->get('channels/' . $channel);

		return $this->_output->output(200, $output['game'], $bot);
	}

	public function current_title()
	{
		$this->_log->set_message("Twitch::current_title() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);
		$bot = isset($this->_params[1]) ? $this->_params[1] : false;

		$output = $this->_twitch->get('channels/' . $channel);

		return $this->_output->output(200, $output['status'], $bot);
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

	public function ban()
	{
		$this->_log->set_message("Twitch::ban() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_params[0];
		$user    = $this->_params[1];
		$banner  = $this->_params[2];
		$reason  = $this->_params[3];
		$bot     = isset($this->_params[4]) ? $this->_params[4] : false;

		$output = $this->_db2->ban_user($channel, $user, $banner, $reason);

		if($output == false) {
			//shouldn't be 200 but fix later
			return $this->_output->output('200', 'There was an error with the request', $bot);
		}

		return $this->_output->output('200', "/ban $user", $bot);
	}

	public function getBannedList()
	{
		$received = json_decode(file_get_contents('php://input'), true);

		if(!isset($received['token']))
		{
			return $this->_output->output(400, "missing OAuth Token", false);
		}

		$channel  = ($received['id_flag']) ? $received['channel'] : $this->_twitch->get_user_id($received['channel']);
		$url      = (isset($received['user_id'])) ? "https://api.twitch.tv/helix/moderation/banned?broadcaster_id=$channel&user_id=" . $received['user_id'] : "https://api.twitch.tv/helix/moderation/banned?broadcaster_id=$channel";

		$output = $this->_twitch->get($url, true, ['Authorization' => "Bearer: " . $received['token']]);

		return $this->_output->output(200, $output, false);
	}

	public function getModerators()
	{
		$received = json_decode(file_get_contents('php://input'), true);

		if(!isset($received['token']))
		{
			return $this->_output->output(400, "missing OAuth Token", false);
		}

		$channel  = ($received['id_flag']) ? $received['channel'] : $this->_twitch->get_user_id($received['channel']);
		$url      = (isset($received['user_id'])) ? "https://api.twitch.tv/helix/moderation/moderators?broadcaster_id=$channel&user_id=" . $received['user_id'] : "https://api.twitch.tv/helix/moderation/moderators?broadcaster_id=$channel";

		$output = $this->_twitch->get($url, true, ['Authorization' => "Bearer: " . $received['token']]);

		return $this->_output->output(200, $output, false);
	}

	public function getModeratorEvents()
	{
		$received = json_decode(file_get_contents('php://input'), true);

		if(!isset($received['token']))
		{
			return $this->_output->output(400, "missing OAuth Token", false);
		}

		$channel  = ($received['id_flag']) ? $received['channel'] : $this->_twitch->get_user_id($received['channel']);
		$url      = (isset($received['user_id'])) ? "https://api.twitch.tv/helix/moderation/moderators/events?broadcaster_id=$channel&user_id=" . $received['user_id'] : "https://api.twitch.tv/helix/moderation/moderators/events?broadcaster_id=$channel";

		$output = $this->_twitch->get($url, true, ['Authorization' => "Bearer: " . $received['token']]);

		return $this->_output->output(200, $output, false);
	}

	public function getBannedEvents()
	{
		$received = json_decode(file_get_contents('php://input'), true);

		if(!isset($received['token']))
		{
			return $this->_output->output(400, "missing OAuth Token", false);
		}

		$channel  = ($received['id_flag']) ? $received['channel'] : $this->_twitch->get_user_id($received['channel']);
		$url      = (isset($received['user_id'])) ? "https://api.twitch.tv/helix/moderation/banned/events?broadcaster_id=$channel&user_id=" . $received['user_id'] : "https://api.twitch.tv/helix/moderation/banned/events?broadcaster_id=$channel";

		$output = $this->_twitch->get($url, true, ['Authorization' => "Bearer: " . $received['token']]);

		return $this->_output->output(200, $output, false);
	}

	public function getSubs()
	{
		$received = json_decode(file_get_contents('php://input'), true);

		if(!isset($received['token']))
		{
			return $this->_output->output(400, "missing OAuth Token", false);
		}

		$channel  = ($received['id_flag']) ? $received['channel'] : $this->_twitch->get_user_id($received['channel']);
		$url      = (isset($received['user_id'])) ? "https://api.twitch.tv/helix/subscriptions?broadcaster_id=$channel&user_id=" . $received['user_id'] : "https://api.twitch.tv/helix/subscriptions?broadcaster_id=$channel";

		$output = $this->_twitch->get($url, true, ['Authorization' => "Bearer: " . $received['token']]);

		return $this->_output->output(200, $output, false);
	}

	public function getStreamTags()
	{
		$channel = $this->_twitch->get_user_id($this->_params[0]);

		$output = $this->_twitch->get("https://api.twitch.tv/helix/streams/tags?broadcaster_id=$channel", true);

		return $this->_output->output(200, $output, false);
	}

	public function topclips()
	{
		$this->_log->set_message("Twitch::topclips() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_params[0];
		$limit   = (isset($this->_params[1]) ? $this->_params[1] : 10);
		$period  = (isset($this->_params[2]) ? $this->_params[2] : 'all');

		$output = $this->_twitch->get("clips/top?channel=$channel&period=$period&limit=$limit");

		return $this->_output->output(200, $output['clips'], false);
	}

	public function getclips()
	{
		$this->_log->set_message("Twitch::getclips() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel    = $this->_twitch->get_user_id($this->_params[0]);
		$start_time = (strpos(isset($this->_params[1]), "T") !== false) ? $this->_params[1] : date('Y-m-d',strtotime('-24 hours')) . "T" . date('H:i:s') . "Z";

		$output = $this->_twitch->get("https://api.twitch.tv/helix/clips?broadcaster_id=$channel&started_at=$start_time", true);

		return $this->_output->output(200, $output['data'], false);
	}

	public function getlogo()
	{
		$this->_log->set_message("Twitch::getclips() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);

		$output = $this->_twitch->get("users/$channel");

		return $this->_output->output(200, $output['logo'], false);
	}

	public function getteams()
	{
		$this->_log->set_message("Twitch::getteams() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$channel = $this->_twitch->get_user_id($this->_params[0]);

		$output = (empty($this->_twitch->get("channels/$channel/teams")['teams'])) ? "None" : $this->_twitch->get("channels/$channel/teams")['teams'];

		return $this->_output->output(200, $output, false);
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
			// Break if we have needed precision
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