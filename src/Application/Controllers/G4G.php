<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 11/12/2018
 * Time: 16:15
 */

namespace API\Controllers;

use API\Library;
use API\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class G4G extends Library\BaseController
{
	private $_db;
	private $_g;

	public function __construct()
	{
		parent::__construct();

		$this->_db = new Model\G4GModel();
		$this->_g = new Library\Guilded();
		$this->_guzzle = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));
	}
/*
	public function archive()
	{
		$this->_log->set_message("G4G::archive() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$guilded = $this->_params[0]; // id from guilded URL
		$bungie = $this->_params[1]; // bungie event id from DTR
		$status = $this->_params[2]; // complete, checkpoint, incomplete, cancelled
		$user = $this->_params[3]; // which officer called it
		$bot = (isset($this->_params[4])) ? $this->_params[4] : true;
		$notes = (isset($this->_params[5])) ? $this->_params[5] : false;
		$g_stats = $this->_g->get_event($this->_params[0]);

		if(is_array($g_stats)) {

			if(!isset($g_stats['allowedRoleIds']) || empty($g_stats['allowedRoleIds'])) {
				$output = "The Guilded Calendar event has not had any role restrictions set, please fix this and resubmit";

				return $this->_output->output(200, $output, $bot);
			}

			$title = explode('-', $g_stats['name']);
			$title[2] = trim($title[2], " ");

			$allowed = ['beginner', 'training', 'intermediate', 'advanced'];

			if(sizeof($title) < 3)
			{
				return $this->_output->output(200, "Sorry but the title of your event doesn't look right, it should be PLATFORM-EVENT-ABILITY (remember the hyphens)", $bot);
			}

			if(!in_array(strtolower($title[2]), $allowed))
			{
				return $this->_output->output(200, "Sorry but the ability in your title must be either Training, Beginner, Intermediate or Advanced, you have $title[2]", $bot);
			}

			$output = $g_stats;

			$output['createdBy'] = $this->_translate_name($g_stats['createdBy']);
			$output['allowedRoleIds'] = $this->_translate_roles($g_stats['allowedRoleIds'][0]);

			$output = $this->_db->add_event($guilded, $bungie, $user, $output, $status, $notes, $title);
		} else {
			$output = "The Guilded ID is incorrect or the event is marked as Member's Only, please try again!";

			return $this->_output->output(200, $output, $bot);
		}

		if($output == false) {
			$output = "Something went wrong, KillerAuzzie will take a look into it";
		} else {
			$output = "The event " . $g_stats['name'] . " has been archived with the status of $status, thank you " . urldecode($user);
		}

		return $this->_output->output(200, $output, $bot);
	}
*/
	public function archive()
	{
		$this->_log->set_message("G4G::archive() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$guilded = $this->_params[0]; // id from guilded URL
		$bungie = $this->_params[1]; // bungie event id from DTR
		$status = $this->_params[2]; // complete, checkpoint, incomplete, cancelled
		$user = $this->_params[3]; // which officer called it
		$bot = (isset($this->_params[4])) ? $this->_params[4] : true;
		$notes = (isset($this->_params[5])) ? $this->_params[5] : false;
		$g_stats = $this->_g->get_event($this->_params[0]);

		if(is_array($g_stats)) {

			if(!isset($g_stats['allowedRoleIds']) || empty($g_stats['allowedRoleIds'])) {
				$output = "The Guilded Calendar event has not had any role restrictions set, please fix this and resubmit";

				return $this->_output->output(200, $output, $bot);
			}

			$title = explode('-', $g_stats['name']);
			$title[2] = trim($title[2], " ");
			$title[3] = (isset($title[3])) ? $title[3] : false;

			$allowed_mode = ['beginner', 'training', 'intermediate', 'advanced'];
			$modeNum = 0;
			$title[1] = trim($title[1]);

			switch(true)
			{
				case stristr($title[1], 'sotp'):
				case stristr($title[1],'lw'):
				case stristr($title[1],'scourge of the past'):
				case stristr($title[1],'last wish'):
				case stristr($title[1],'eow'):
				case stristr($title[1],'eater of worlds'):
				case stristr($title[1],'leviathan'):
				case stristr($title[1],'sos'):
				case stristr($title[1],'spire of stars'):
					$modeNum = 4;

					break;
				case stristr($title[1],'nightfall'):
					$modeNum = 16;
				
					break;
				case stristr($title[1],'quickplay'):
					$modeNum = 70;

					break;
				case stristr($title[1],'crucible'):
					$modeNum = 5;

					break;
				case stristr($title[1],'iron banner'):
				case stristr($title[1],'iron banana'):
					$modeNum = 19;

					break;
				case stristr($title[1],'gambit'):
					$modeNum = 63;

					break;
				case stristr($title[1], 'reckoning'):
					$modeNum = 76;
					
					break;
				case stristr($title[1], 'milestone'):
					$modeNum = 7;

					break;
				default:
					$output = "The game event `".trim(strtolower($title[1]))."` doesn't look right, please correct";

					return $this->_output->output(200, $output, $bot);
			}

			if(sizeof($title) < 3) {
				return $this->_output->output(200, "Sorry but the title of your event doesn't look right, it should be PLATFORM-EVENT-ABILITY (remember the hyphens)", $bot);
			}

			if(!in_array(strtolower($title[2]), $allowed_mode)) {
				return $this->_output->output(200, "Sorry but the ability in your title must be either Training, Beginner, Intermediate or Advanced, you have $title[2]", $bot);
			}

			$output = $g_stats;

			$output['createdBy'] = $this->_translate_name($g_stats['createdBy']);
			$output['allowedRoleIds'] = $this->_translate_roles($g_stats['allowedRoleIds'][0]);

			$payload = [
				'ModeType' => $modeNum,
				'Platform' => (strtolower($output['allowedRoleIds']) === 'playstation') ? 2 : ((strtolower($output['allowedRoleIds']) === 'xbox') ? 1 : 4),
				'EventName' => trim($title[1]),
				'EventDescription' => $this->_params[0] . " - " . trim($title[1]),
				'StartTime' => $g_stats['happensAt'],
				'DurationInMinutes' => 120,
				'OfficerDisplayName' => ($output['createdBy'] === false) ? $this->_params[3] : $output['createdBy'],
				'Cancelled' => (substr($status, 0, 3) == 'can') ? true : false,
				'skillLevel' => (strtolower($title[2]) == 'advanced') ? 2 : ((strtolower($title[2]) == 'intermediate') ? 1 : 0),
				'Training' => (strtolower(trim($title[3])) == 'training') ? true : false,
				'Request' => false
			];

			//$output = $this->_db->add_event($guilded, $bungie, $user, $output, $status, $notes, $title);
			//NOW WE SEND THIS TO CLANEVENTS
			try {
				$request = $this->_guzzle->post('https://clanevents.net/api/ClanEvents/ArchiveEvent', [
					'body' => json_encode($payload),
					'headers' => [
						'Content-Type' => 'application/json',
						'API_Key'      => 'A26A6177-A85F-490B-9D35-F3C992825694'
					]
				]);

				$output = json_decode($request->getBody(), true)['message'];
			} catch (ClientException $e) {
				$response = $e->getResponse();
				echo($response->getStatusCode());
				//$responseBodyAsString = $response->getBody()->getContents();
			} catch(RequestException $f) {
				$response = $f->getResponse();
				
				if($response->getStatusCode() == 400)
				{
					$output = "Something went wrong: " . json_decode($response->getBody(), true)['message'];

					return $this->_output->output(200, $output, $bot);
				}
			}
		} else {
			$output = "The Guilded ID is incorrect or the event is marked as Member's Only, please try again!";

			return $this->_output->output(200, $output, $bot);
		}

		if($output == false) {
			$output = "Something went wrong, KillerAuzzie will take a look into it";
		} else {
			$output = "The event " . $g_stats['name'] . " has been archived with the status of $status, thank you " . urldecode($user);
		}

		return $this->_output->output(200, $output, $bot);
	}

	public function list_archive()
	{
		//lets just have it do all
		return $this->_output->output(200, $this->_db->get_archive(), false);
	}

	public function upcoming()
	{
		$date = strtotime("+7 day");
		$output = $this->_g->get('teams/QR4AyKlP/events?endDate=' . date('Y-m-d', $date));
		$output['endDate'] = date('d-M-y', $date);

		for($i = 0; $i < sizeof($output['events']); $i++)
		{
			$output['events'][$i]['name'] = explode('-', $output['events'][$i]['name'])[1];
			$output['events'][$i]['createdBy'] = $this->_translate_name($output['events'][$i]['createdBy']);
			$output['events'][$i]['happensAt'] = date('d/M @ h:ia', strtotime($output['events'][$i]['happensAt']));
		}
		return $this->_output->output(200, $output, false);
	}

	private function _translate_name($gid)
	{
		//need to check if the user id is in the db already, if not then we will pull it from guilded and store in the db
		//$exists = $this->_db->check_user_exists($gid);
		$exists = false;

		if($exists === false) {
			$output = $this->_g->get('users/' . $gid);

			//$output = $this->_db->add_user($output['user']['id'], $output['user']['name']);
			$output = $output['user']['name'];
		} else {
			$output = $exists;
		}

		return $output;
	}

	private function _translate_roles($id)
	{
		//50317 is actually G4G Orion
		$roles = ['50317' => 'PlayStation', '411' => 'PlayStation', '412' => 'Xbox', '413' => 'PC', '859' => 'Officer', '856' => 'Clan Council',
				  '858' => 'Web Team'];

		return $roles[$id];
	}		

	public function points()
	{
		$this->_log->set_message("G4G::points() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$mode = (strtolower($this->_params[0]) === "pvp") ? "PvP" :
				((strtolower($this->_params[0]) === "gambit") ? "Gambit" : "PvE");

		$user = $this->_params[1];

		try {
			$request = $this->_guzzle->get("https://clanevents.net/api/ClanRankings/$user", [
				'headers' => [
					'Content-Type' => 'application/json',
					'API_Key'      => 'A26A6177-A85F-490B-9D35-F3C992825694'
				]
			]);

			$output = json_decode($request->getBody(), true);

			if($output['success'])
			{
				$data = $output['responseBody']['ranksByLadder'];
				
				switch(strtolower($mode))
				{
					case 'pve':
						$data = ($data[0]['ladderName'] == 'PvE Ladder') ? $data[0] : (($data[1]['ladderName'] == 'PvE Ladder') ? $data[1] : $data[2]);

						break;
					case 'pvp':
					$data = ($data[0]['ladderName'] == 'PvP Ladder') ? $data[0] : (($data[1]['ladderName'] == 'PvP Ladder') ? $data[1] : $data[2]);

						break;
					default:
					$data = ($data[0]['ladderName'] == 'Gambit Ladder') ? $data[0] : (($data[1]['ladderName'] == 'Gambit Ladder') ? $data[1] : $data[2]);
				}

				return $this->_output->output(200, $data, false);
			}
		} catch(RequestException $e) {
			if ($e->getResponse()->getStatusCode() == '400') {
				$output = json_decode((string) $e->getResponse()->getBody(), true)['message'];
			}

		}
	}

	public function prestige()
	{
		$this->_log->set_message("G4G::prestige() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$mode = $this->_params[0];
		$target = $this->_params[1];
		$event = strtolower($this->_params[2]);
		$bot = (isset($this->_params[3])) ? $this->_params[3] : false;

		if($mode == "add") {
			$this->_db->add_prestige($event, $target);
		} else {
			$this->_db->remove_prestige($event, $target);
		}

		return $this->_output->output(200, "Prestige has been " . (($event == "add") ? "added to" :
											 "removed from") . " $target", $bot);
	}

	public function register()
	{
		$this->_log->set_message("G4G::register() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		if(sizeof($this->_params) < 2) {
			$query = "Something was not quite right, the command is `!pointsregister D2_NAME`";
		}
		$discord = $this->_params[0];
		$tag = $this->_params[1];

		$this->_output->setOutput('plain');

		$query = $this->_db->link_user($discord, $tag);

		$query = ($query === true) ? "Registration is successful" :
			"Something went wrong with the registration, tag the Web Team";

		return $this->_output->output(200, $query, true);
	}

	public function add_warn()
	{
		$data = json_decode(file_get_contents('php://input'), true);

		if(!$data) { return $this->_output->output(405, "This is a POST endpoint", false); }
		
		$number = $this->_db->addWarn($data);

		return $this->_output->output(200, $number, false);
	}

	public function get_warn()
	{
		$number = $this->_params[0];

		$warning = $this->_db->getWarn($number);

		return $this->_output->output(200, $warning, false);
	}

	public function remove_warn()
	{
		$number = $this->_params[0];

		$removed = $this->_db->removeWarn($number);

		return $this->_output->output(200, $removed, false);
	}

}