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

	public function __construct()
	{
		parent::__construct();

		$this->_db = new Model\G4GModel();
	}

	public function add()
	{
		//format !addpoints 100 @p_rigz PVE
		$points = $this->_params[0];
		$target = str_replace('@', '', $this->_params[1]);
		$mode = strtolower($this->_params[2]);
		$auth = $this->_authenticate($this->_params[3]);
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
		//!removepoints 100 @p_rigz pve
		$points = $this->_params[0];
		$target = str_replace('@', '', $this->_params[1]);
		$mode = strtolower($this->_params[2]);
		$auth = $this->_authenticate($this->_params[3]);
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
				default:
					$output = "WTF is $mode!?!?!";
			}
		}

		return $this->_output->output(200, $output, $bot);
	}

	public function getList()
	{
		if($this->_params[2] != 'null') {
			$qty = $this->_params[0];
			$mode = (strtolower($this->_params[1]) == "pvp") ? "PvP" : "PvE";
			$user = $this->_params[2];
			$bot = (isset($this->_params[5])) ? $this->_params[5] : false;

			if(isset($this->_params[4])) {
				$this->_output->setOutput($this->_params[4]);
			}
		} else {
			//!unknown(top10, all, single) MODE
			$qty = $this->_params[0];
			$mode = (strtolower($this->_params[1]) == "pvp") ? "PvP" : "PvE";
			$user = $this->_params[3];
			$bot = (isset($this->_params[5])) ? $this->_params[5] : false;

			if(isset($this->_params[4])) {
				$this->_output->setOutput($this->_params[4]);
			}
		}

		$query = $this->_db->get_list($qty, $mode, $user);

		if($qty == '1') {
			$query = "<" . $query['0']['name'] . "> Prestige: " . $query['0']['prestige'] . ", Rank: " . $query['0']['rank'] . ", " . $query['0']['points'] . " $mode Points";
		}

		return $this->_output->output(200, $query, $bot);
	}

	public function prestige()
	{
		$mode = $this->_params[0];
		$target = $this->_params[1];
		$event = strtolower($this->_params[2]);
		$bot = (isset($this->_params[3])) ? $this->_params[3] : false;

		if($mode == "add") {
			$this->_db->add_prestige($mode, $target);
		} else {
			$this->_db->add_prestige($mode, $target);
		}

		return $this->_output->output(200, "Prestige has been " . (($event == "add") ? "added to" :
											 "removed from") . " $target", $bot);
	}
}