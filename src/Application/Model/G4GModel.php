<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 11/12/2018
 * Time: 16:18
 */

namespace API\Model;

use API\Library;


class G4GModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

	public function add_pvp_points($user, $points, $requester)
	{
		$rankup = false;

		try {
			//Lets find out if the user actually exists already
			$exist = $this->_db->prepare("SELECT points, rank FROM G4G_PVP WHERE name = :name");
			$exist->execute([':name' => $user]);
			$existing_points = $exist->fetch();

			//check to see if the user actually existed
			if($existing_points) {
				$points = $points + $existing_points['points'];
			}

			$rankup = $this->_rank_up($points, $existing_points['rank']);

			if($points < 1500 || $rankup == false) {
				//Lets put a call in to add points
				$stmt = $this->_db->prepare("INSERT INTO G4G_PVP(name, points) VALUES(:name, :points) ON DUPLICATE KEY UPDATE points = :points");
				$stmt->execute(
					[
						':name'   => $user,
						':points' => $points
					]
				);
			} else {
				$stmt = $this->_db->prepare("INSERT INTO G4G_PVP(name, rank, points) VALUES(:name, :points) ON DUPLICATE KEY UPDATE rank = :rank, points = :points");
				$stmt->execute(
					[
						':name'   => $user,
						':rank'   => $rankup,
						':points' => $points
					]
				);
			}



			return true;
		} catch(\PDOException $e) {
			return $e->getMessage();
		}
	}

	public function add_pve_points($user, $points, $requester)
	{
		try {
			//Lets find out if the user actually exists already
			$exist = $this->_db->prepare("SELECT points, rank FROM G4G_PVE WHERE name = :name");
			$exist->execute([':name' => $user]);
			$existing_points = $exist->fetch();

			//check to see if the user actually existed
			if($existing_points) {
				$points = $points + $existing_points['points'];
			}

			$rankup = $this->_rank_up($points, $existing_points['rank']);

			if($points < 1500 || $rankup == false) {
				//Lets put a call in to add points
				$stmt = $this->_db->prepare("INSERT INTO G4G_PVE(name, points) VALUES(:name, :points) ON DUPLICATE KEY UPDATE points = :points");
				$stmt->execute(
					[
						':name'   => $user,
						':points' => $points
					]
				);
			} else {
				$stmt = $this->_db->prepare("INSERT INTO G4G_PVE(name, rank, points) VALUES(:name, :rank, :points) ON DUPLICATE KEY UPDATE rank = :rank, points = :points");
				$stmt->execute(
					[
						':name'   => $user,
						':rank'   => $rankup,
						':points' => $points
					]
				);
			}

			return true;
		} catch(\PDOException $e) {
			return $e->getMessage();
		}
	}

	public function remove_pvp_points($user, $points, $requester)
	{
		try {
			//Lets find out if the user actually exists already
			$exist = $this->_db->prepare("SELECT points FROM G4G_PVP WHERE name = :name");
			$exist->execute([':name' => $user]);
			$existing_points = $exist->fetch();

			//check to see if the user actually existed
			if($existing_points) {
				$points = $existing_points['points'] - $points;
			} else {
				return false;
			}

			//Lets put a call in to add points
			$stmt = $this->_db->prepare("UPDATE G4G_PVP SET points = :points WHERE name = :user");
			$stmt->execute(
				[
					':user'   => $user,
					':points' => $points
				]
			);

			return true;
		} catch(\PDOException $e) {
			return $e->getMessage();
		}
	}

	public function remove_pve_points($user, $points, $requester)
	{
		try {
			//Lets find out if the user actually exists already
			$exist = $this->_db->prepare("SELECT points FROM G4G_PVE WHERE name = :name");
			$exist->execute([':name' => $user]);
			$existing_points = $exist->fetch();

			//check to see if the user actually existed
			if($existing_points) {
				$new_points = (($existing_points['points'] - $points) < 0) ? 0 : ($existing_points['points'] - $points);
			} else {
				echo "hi";
				return false;
			}

			//Lets put a call in to add points
			$stmt = $this->_db->prepare("UPDATE G4G_PVE SET points = :points WHERE name = :user");
			$stmt->execute(
				[
					':user'   => $user,
					':points' => $new_points
				]
			);

			return true;
		} catch(\PDOException $e) {
			return $e->getMessage();
		}
	}

	public function get_list($qty, $mode, $user)
	{
		//check to see if the quantity is all or a fixed amount
		if(strtolower($qty) == "all") {
			try {
				$stmt = $this->_db->prepare("SELECT name, rank, points, prestige FROM G4G_" . strtoupper($mode));
				$stmt->execute();

				$this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			} catch(\PDOException $e) {
				$this->_output = $e->getMessage();
			}
		} elseif(strtolower($qty) == "1") {
			try {
				$stmt = $this->_db->prepare("SELECT name, rank, points, prestige FROM G4G_" . strtoupper($mode) . " WHERE name = :name");
				$stmt->execute([':name' => $user]);

				$this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);

			} catch(\PDOException $e) {
				$this->_output = $e->getMessage();
			}
		} else {
			try {
				$qty = str_replace("top", "", $qty);

				$stmt = $this->_db->prepare("SELECT name, rank, points, prestige FROM G4G_" . strtolower($mode) . " ORDER BY points DESC LIMIT $qty");

				$stmt->execute();

				$this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);
			} catch(\PDOException $e) {
				$this->_output = $e->getMessage();
			}
		}

		return $this->_output;
	}

	public function add_prestige($mode, $user)
	{
		try {
			$stmt = $this->_db->prepare("UPDATE G4G_" . strtolower($mode) . " SET points = 0, prestige = prestige + 1 WHERE name = :name");

			$stmt->execute([':name' => $user]);

		} catch(\PDOException $e) {
			$this->_output = $e->getMessage();
		}
	}

	public function remove_prestige($mode, $user)
	{
		try {
			$stmt = $this->_db->prepare("UPDATE G4G_" . strtolower($mode) . " SET points = 0, prestige = prestige + 1 WHERE name = :name");

			$stmt->execute([':name' => $user]);

		} catch(\PDOException $e) {
			$this->_output = $e->getMessage();
		}
	}

	private function _rank_up($points, $existing_rank)
	{
		$return = false;

		if($points >= 1500 && $points < 3000) {
			$return = ($existing_rank == "none" || $existing_rank == "Harbinger") ? "Harbinger" : false;
		} elseif($points >= 3000 && $points < 4500) {
			$return = ($existing_rank == "none" || $existing_rank == "Harbinger" || $existing_rank == "Chaos Bringer Apprentice") ?
				"Chaos Bringer Apprentice" : false;
		} elseif($points >= 4500 && $points < 6000) {
			$return = ($existing_rank == "none" || $existing_rank == "Harbinger" || $existing_rank == "Chaos Bringer Apprentice" || $existing_rank == "Chaos Bringer") ?
				"Chaos Bringer" : false;
		} elseif($points >= 6000 && $points < 7500) {
			$return = ($existing_rank == "none" || $existing_rank == "Harbinger" || $existing_rank == "Chaos Bringer Apprentice" || $existing_rank == "Chaos Bringer" || $existing_rank == "Furion Apprentice") ?
				"Furion Apprentice" : false;
		} elseif($points >= 7500) {
			$return = ($existing_rank == "none" || $existing_rank == "Harbinger" || $existing_rank == "Chaos Bringer Apprentice" || $existing_rank == "Chaos Bringer" || $existing_rank == "Furion Apprentice" || $existing_rank == "Furion") ?
				"Furion" : false;
		}

		return $return;
	}

	public function link_user($discord, $tag)
	{
		try {
			$stmt = $this->_db->prepare("INSERT INTO g4g_link (discord, tag) VALUES(:discord, :tag) ON DUPLICATE KEY UPDATE discord = :discord, tag = :tag");
			$stmt->execute(
				[
					':discord' => $discord,
					':tag'     => $tag
				]
			);

			return true;
		} catch(\PDOException $e) {
			return $e->getMessage();
		}
	}

	public function check_link($discord)
	{
		try {
			$stmt = $this->_db->prepare("SELECT tag FROM g4g_link WHERE discord = :discord");
			$stmt->execute([':discord' => $discord]);

			$this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);

			$this->_output = (is_array($this->_output)) ? $this->_output['tag'] : false;
		} catch(\PDOException $e) {
			$this->_output = false;
		}

		return $this->_output;
	}
}