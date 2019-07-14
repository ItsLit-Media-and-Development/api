<?php
namespace API\Model;

use API\Library;


class G4GModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_archive()
	{
		try {
			$stmt = $this->_db->prepare("SELECT * FROM g4g_events");
			$stmt->execute();
			$this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch(\PDOException $e) {
			$this->_output = $e->getMessage();
		}

		return $this->_output;
	}

	public function add_event($guilded, $bungie, $officer, array $g_stats, $status = false, $notes, $title)
	{
		try {
			$stmt = $this->_db->prepare("INSERT INTO g4g_events(gid, bid, officer, event_owner, event_name, event_time," .
										" platform, role_restriction, is_archived, event_status, notes, event_type) VALUES (:gid, :bid, :officer, :owner," .
										" :name, :time, :platform, :roles, :archive, :status, :notes, :type)");
			$stmt->execute(
				[
					':gid'      => $guilded,
					':bid'      => $bungie,
					':officer'  => $officer,
					':owner'    => $g_stats['createdBy'],
					':name'     => $title[1],
					':time'     => $g_stats['happensAt'],
					':platform' => $g_stats['allowedRoleIds'],
					':roles'    => $g_stats['allowedRoleIds'],
					':archive'  => ($status !== false) ? 1 : 0,
					':status'   => $status,
					':notes'    => $notes,
					':type'     => $title[2]
				]
			);

			$this->_output = $stmt->rowCount();
		} catch(\PDOException $e) {
			$this->_output = false;
		}

		return $this->_output;
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
				$stmt = $this->_db->prepare("SELECT name, rank, points, prestige FROM G4G_" . strtoupper($mode) . " ORDER BY points DESC");
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

				$stmt = $this->_db->prepare("SELECT name, rank, points, prestige FROM G4G_" . strtoupper($mode) . " ORDER BY points DESC LIMIT 0, $qty");

				$stmt->execute();

				$this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			} catch(\PDOException $e) {
				$this->_output = $e->getMessage();
			}
		}

		return $this->_output;
	}

	public function add_prestige($mode, $user)
	{
		try {
			$stmt = $this->_db->prepare("UPDATE G4G_" . strtoupper($mode) . " SET points = 0, prestige = prestige + 1 WHERE name = :name");

			$stmt->execute([':name' => $user]);

		} catch(\PDOException $e) {
			$this->_output = $e->getMessage();
		}
	}

	public function remove_prestige($mode, $user)
	{
		try {
			$stmt = $this->_db->prepare("UPDATE G4G_" . strtoupper($mode) . " SET points = 0, prestige = prestige + 1 WHERE name = :name");

			$stmt->execute([':name' => $user]);

		} catch(\PDOException $e) {
			$this->_output = $e->getMessage();
		}
	}

	private function _rank_up($points, $existing_rank, $mode)
	{
		$return = false;

		switch($mode) {
			case 'pvp':

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

				break;
			case 'pve':
				if($points >= 1500 && $points < 3000) {
					$return = ($existing_rank == "none" || $existing_rank == "Luminary") ? "Luminary" : false;
				} elseif($points >= 3000 && $points < 4500) {
					$return = ($existing_rank == "none" || $existing_rank == "Luminary" || $existing_rank == "Commodore Jr") ?
						"Commodore Jr" : false;
				} elseif($points >= 4500 && $points < 6000) {
					$return = ($existing_rank == "none" || $existing_rank == "Luminary" || $existing_rank == "Commodore Jr" || $existing_rank == "Commodore") ?
						"Commodore" : false;
				} elseif($points >= 6000 && $points < 7500) {
					$return = ($existing_rank == "none" || $existing_rank == "Luminary" || $existing_rank == "Commodore Jr" || $existing_rank == "Commodore" || $existing_rank == "Apex Jr") ?
						"Apex Jr" : false;
				} elseif($points >= 7500) {
					$return = ($existing_rank == "none" || $existing_rank == "Luminary" || $existing_rank == "Commodore Jr" || $existing_rank == "Commodore" || $existing_rank == "Apex Jr" || $existing_rank == "Apex") ?
						"Apex" : false;
				}

				break;
			case 'gambit':
				if($points >= 1500 && $points < 3000) {
					$return = ($existing_rank == "none" || $existing_rank == "Outlaw") ? "Outlaw" : false;
				} elseif($points >= 3000 && $points < 4500) {
					$return = ($existing_rank == "none" || $existing_rank == "Outlaw" || $existing_rank == "Maverick") ?
						"Maverick" : false;
				} elseif($points >= 4500 && $points < 6000) {
					$return = ($existing_rank == "none" || $existing_rank == "Outlaw" || $existing_rank == "Maverick" || $existing_rank == "Renegade") ?
						"Renegade" : false;
				} elseif($points >= 6000 && $points < 7500) {
					$return = ($existing_rank == "none" || $existing_rank == "Outlaw" || $existing_rank == "Maverick" || $existing_rank == "Renegade" || $existing_rank == "Assassin") ?
						"Assassin" : false;
				} elseif($points >= 7500) {
					$return = ($existing_rank == "none" || $existing_rank == "Outlaw" || $existing_rank == "Maverick" || $existing_rank == "Renegade" || $existing_rank == "Assassin" || $existing_rank == "Infamous") ?
						"Infamous" : false;
				}

				break;
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

	public function check_user_exists($gid)
	{
		try {
			//lets checkt to see if we have the user already
			$stmt = $this->_db->prepare("SELECT tag FROM g4g_link WHERE guilded = :gid");
			$stmt->execute([':gid' => $gid]);

			$name = $stmt->fetch();

			$this->_output = (isset($name['tag'])) ? $name['tag'] : false;
		} catch(\PDOException $e) {
			$this->_output = false;
		}

		return $this->_output;
	}

	public function add_user($gid, $discord)
	{
		try {
			//we will run an insert update query
			$stmt = $this->_db->prepare("SELECT COUNT(discord) FROM g4g_link WHERE discord = :discord");
			$stmt->execute([':discord' => $discord]);

			$exists = $stmt->fetch(\PDO::FETCH_NUM);

			if($exists[0] > 0) {
				//user exists lets update
				$update = $this->_db->prepare("UPDATE g4g_link SET guilded = :gid WHERE discord = :discord");
				$update->execute(
					[
						':discord' => $discord,
						':gid'     => $gid
					]
				);

				$this->_output = $update->rowCount();
			} else {
				//they aren't in the db right now
				$insert = $this->_db->prepare("INSERT INTO g4g_link (discord, guilded) VALUES(:discord, :guilded) ON DUPLICATE KEY UPDATE guilded = :guilded");
				$insert->execute(
					[
						':discord' => $discord,
						':gid'     => $gid
					]
				);

				$this->_output = $insert->rowCount();
			}
		} catch(\PDOException $e) {
			$this->_output = false;
		}

		return $this->_output;
	}

	public function addWarn(array $data)
	{
		$stmt = $this->_db->prepare("INSERT INTO g4g_warn (user, sender, reason) VALUES (:user, :sender, :reason)");
		$stmt->execute([
			':user'   => $data['user'],
			':sender' => $data['sender'],
			':reason' => $data['reason']
		]);

		return $this->_db->lastInsertId();
	}

	public function getWarn($id)
	{
		$stmt = $this->_db->prepare("SELECT user, sender, reason FROM g4g_warn WHERE id = :id AND active = 1");
		$stmt->execute([':id' => $id]);

		$this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);

		return $this->_output;
	}

	public function removeWarn($id)
	{
		$stmt = $this->_db->prepare("UPDATE g4g_warn SET active = 0 WHERE id = :id");
		$stmt->execute([':id' => $id]);

		$this->_output = $stmt->rowCount();

		return $this->_output;
	}
}