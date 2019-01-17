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


class G4G extends Library\BaseController
{
	private $_db;
	private $_g;

	public function __construct()
	{
		parent::__construct();

		$this->_db = new Model\G4GModel();
		$this->_g = new Library\Guilded();
	}

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

			$output = $g_stats;

			$output['createdBy'] = $this->_translate_name($g_stats['createdBy']);
			$output['allowedRoleIds'] = $this->_translate_roles($g_stats['allowedRoleIds'][0]);

			$output = $this->_db->add_event($guilded, $bungie, $user, $output, $status, $notes);
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

	private function _translate_name($gid)
	{
		//need to check if the user id is in the db already, if not then we will pull it from guilded and store in the db
		$exists = $this->_db->check_user_exists($gid);

		if($exists === false) {
			$output = $this->_g->get('users/' . $gid);

			$output = $this->_db->add_user($output['user']['name'], $output['user']['name']);
		} else {
			$output = $exists;
		}

		return $output;
	}

	private function _translate_roles($id)
	{
		$roles = ['411' => 'PlayStation', '412' => 'Xbox', '413' => 'PC', '859' => 'Officer', '856' => 'Clan Council',
				  '858' => 'Web Team'];

		return $roles[$id];
	}

	public function add()
	{
		$this->_log->set_message("G4G::add() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		//format !addpoints 100 @p_rigz PVE
		$points = $this->_params[0];
		$target = $this->_params[1];
		$mode = strtolower($this->_params[2]);
		//$auth = $this->_authenticate($this->_params[3]);
		$auth = $this->_params[3];
		$bot = (isset($this->_params[4])) ? $this->_params[4] : true;
		$output = '';

		if(!$auth) {
			$output = "You are too much of a scrub to use this!";
		} else {

			switch($mode) {
				case 'pvp':
					if($this->_db->add_pvp_points($target, $points, $this->_params[3]) == true) {
						$output = "$points PvP points have been added to $target by " . $this->_params[3];
					}
					break;
				case 'pve':
					if($this->_db->add_pve_points($target, $points, $this->_params[3]) == true) {
						$output = "$points PvE points have been added to $target by " . $this->_params[3];
					}
					break;
				case 'gambit':
					if($this->_db->add_gambit_points($target, $points, $this->_params[3]) == true) {
						$output = "$points Gambit points have been added to $target by " . $this->_params[3];
					}
					break;
				default:
					$output = "WTF is $mode!?!?!";
			}
		}

		return $this->_output->output(200, $output, $bot);
	}

	private function _authenticate($user)
	{
		$allowed = ['xonar3', 'P_Rigz', 'Kayowin', 'Ruxomar', 'whatshisname00', 'Sig_shezza', 'LexaKB', 'MattMan7496',
					'R3AP3RSE7EN', 'U.P.S', 'x-Maw-x', 'ypk2909', 'itslittany'];

		return (in_array($user, $allowed)) ? true : false;
	}

	public function remove()
	{
		$this->_log->set_message("G4G::remove() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		//!removepoints 100 @p_rigz pve
		$points = $this->_params[0];
		$target = str_replace('@', '', $this->_params[1]);
		$mode = strtolower($this->_params[2]);
		//$auth = $this->_authenticate($this->_params[3]);
		$auth = true;
		$bot = (isset($this->_params[4])) ? $this->_params[4] : true;
		$output = '';

		if(!$auth) {
			$output = "You are too much of a scrub to use this!";
		} else {

			switch($mode) {
				case 'pvp':
					if($this->_db->remove_pvp_points($target, $points, $this->_params[3]) == true) {
						$output = "$points PvP points have been removed from $target by " . $this->_params[3];
					}
					break;
				case 'pve':
					if($this->_db->remove_pve_points($target, $points, $this->_params[3]) == true) {
						$output = "$points PvE points have been removed from $target by " . $this->_params[3];
					}
					break;
				case 'gambit':
					if($this->_db->remove_gambit_points($target, $points, $this->_params[3]) == true) {
						$output = "$points Gambit points have been removed from $target by " . $this->_params[3];
					}
					break;
				default:
					$output = "WTF is $mode!?!?!";
			}
		}

		return $this->_output->output(200, $output, $bot);
	}

	public function getList()
	{
		$this->_log->set_message("G4G::getList() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$qty = $this->_params[0];
		$mode = (strtolower($this->_params[1]) == "pvp") ? "PvP" :
			(strtolower($this->_params[1]) == "gambit") ? "Gambit" : "PvE";
		$this->_params[3] = (isset($this->_params[3])) ? str_replace(' ', '-', $this->_params[3]) : '';
		$this->_params[3] = (isset($this->_params[3])) ? preg_replace('/-+/', '-', $this->_params[3]) : '';
		$user = '';
		$query = "Sorry but you do not seem to have any $mode Points $user. If $user is not your D2 name, please do `!pointsregister D2_name` i.e. `!pointsregister KillerAuzzie`";

		if($this->_params[2] != 'null') {

			$bot = (isset($this->_params[5])) ? $this->_params[5] : false;

			if(isset($this->_params[4])) {
				$this->_output->setOutput($this->_params[4]);
			}
		} else {
			$user = ($this->_db->check_link($this->_params[3]) !== false) ? $this->_db->check_link($this->_params[3]) :
				$this->_params[3];

			$bot = (isset($this->_params[5])) ? $this->_params[5] : false;

			if(isset($this->_params[4])) {
				$this->_output->setOutput($this->_params[4]);
			}
		}

		$query = $this->_db->get_list($qty, $mode, $user);

		if(empty($query)) {
			if($this->_db->check_link($user) === false) {
				$query = "Sorry but it seems like you do not have any $mode points $user. If this is wrong, make sure you have registered `!pointsregister D2_name` and ping the Web Team if you still see this message and it is wrong.";
			}
		} else {
			if($qty == '1') {
				if($query == false) {
					$query = "Sorry but you do not seem to have any $mode Points $user. If $user is not your D2 name, please do `!pointsregister D2_name` i.e. `!pointsregister KillerAuzzie`";
				} else {
					$query = $query['0']['name'] . " Prestige: " . $query['0']['prestige'] . ", Rank: " . $query['0']['rank'] . ", " . $query['0']['points'] . " $mode Points";
				}
			} else {
				$tmp = [];

				for($i = 0; $i < sizeof($query); $i++) {
					$tmp[$i]['Name'] = $query[$i]['name'];
					$tmp[$i]['Prestige'] = $query[$i]['prestige'];
					$tmp[$i]['rank'] = $query[$i]['rank'];
					$tmp[$i]['points'] = $query[$i]['points'];
				}

				$query = $tmp;
			}
		}
		
		return $this->_output->output(200, $query, $bot);
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
}