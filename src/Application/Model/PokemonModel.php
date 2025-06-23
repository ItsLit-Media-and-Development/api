<?php
/**
 * Pokemon Model Class
 *
 * All database functions regarding the Pokemon endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2023 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 2.0
 * @filesource
 */

namespace API\Model;

use API\Library;

class PokemonModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getEvent(string $id)
	{
		try
		{
			$stmt = $this->_db->prepare("SELECT * FROM pkmn_events WHERE sanction_id = :id");
			$stmt->execute(
				[
					':id' => $id
				]
			);

			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

			return $result;
		} catch (\Exception $e) {
			new Exceptions\DBException($e->getMessage());

			return [];
		}
	}

	public function getEvents()
	{

	}

	public function addEvent(array $details)
	{
		$details = array_values($details);

		//First lets check to see if we already have this event in the DB, if so, remove it from the array otherwise insert it
		for($i = 0; $i < count($details); $i++)
		{
			try
			{
				$sel = $this->_db->prepare("SELECT count(*) FROM rk9_events WHERE Event_Name = :ename");
				$sel->execute(
					[
						':ename' => $details[$i]['Event Name']
					]
				);

				if(count($sel->fetch()) == 0)
				{
					unset($details[$i]);
				}
			}
			catch(\PDOException $e)
			{
				$this->_output = $e->getMessage();
			}
		}

		try
		{
			foreach($details as $d)
			{
				$ins = $this->_db->prepare("INSERT INTO rk9_events (Event_Name, StartDate, EndDate, Event_Location, Event_Link, TCG, VGC, POGO, Unite, Spectator) VALUES(:Event_Name, :StartDate, :EndDate, :Event_Location, :Event_Link, :TCG, :VGC, :POGO, :Unite, :Spectator)");

				$ins->execute(
					[
						':Event_Name'     => $d['Event Name'], 
						':StartDate'      => $d['Event Start Date']->format('Y-m-d'),
						':EndDate'        => $d['Event End Date']->format('Y-m-d'), 
						':Event_Location' => $d['Event Location'], 
						':Event_Link'     => $d['Links']['Event'], 
						':TCG'            => $d['Links']['TCG'], 
						':VGC'            => $d['Links']['VGC'], 
						':POGO'           => $d['Links']['GO'], 
						':Unite'          => (isset($d['Links']['Unite']) ? $d['Links']['Unite'] : null), 
						':Spectator'      => $d['Links']['Spectator']
					]
				);
			}

			$this->_output = ($ins->rowCount() > 0) ? true : false;
		}
		catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

		return $this->_output;
	}

	public function modifyEvent(string $id, array $payload)
	{

	}

	public function removeEvent(string $id)
	{

	}

	public function addStandings(string $id, array $standings)
	{

	}

	public function verifyStandings(string $id)
	{

	}

	public function updateStandings(string $id, array $standings)
	{

	}

	public function getDecklists()
	{
		try
		{
			$stmt = $this->_db->prepare("SELECT id, deck_name, decklist, season, played, wins, losses, ties, (wins / (wins + losses + ties)) AS win_percent FROM decklists");
			$stmt->execute();

			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

			return $result;
		} catch (\Exception $e) {
			new Exceptions\DBException($e->getMessage());

			return [];
		}
	}

	public function getDeck(string $id)
	{
		try 
		{
			$stmt = $this->_db->prepare("SELECT id, deck_name, decklist, season, played, wins, losses, ties, (wins / (wins + losses + ties)) AS win_percent FROM decklists WHERE id = :id");
			$stmt->execute(
				[
					':id' => $id
				]
			);

			$result = $stmt->fetch(\PDO::FETCH_ASSOC);

			return $result;
		} catch (\Exception $e) {
			new Exceptions\DBException($e->getMessage());

			return [];
		}
	}

	public function addDecklist(array $details)
	{
		try
		{
			$ins = $this->_db->prepare("INSERT INTO decklists (deck_name, decklist, season) VALUES(:deckname, :decklist, :season)");
			$ins->execute(
				[
					':deckname' => $details['deck_name'],
					':decklist' => $details['decklist'],
					':season'   => $details['season']
				]
			);

			$this->_output = ($ins->rowCount() > 0) ? true : false;
        }
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
	}

	public function updateDeckResults(array $details)
	{
		try 
		{
			$upd = $this->_db->prepare("UPDATE decklists SET wins = wins + :win, losses = losses + :loss, ties = ties + :tie, played = 1 WHERE id = :id");
			$upd->execute(
				[
					':id'   => $details['id'],
					':win'  => $details['win'],
					':loss' => $details['loss'],
					':tie'  => $details['tie']
				]
			);

				$this->_output = ($upd->rowCount() > 0) ? true : false;
			}
			catch(\PDOException $e)
			{
				$this->_output = $e->getMessage();
			}
	
			return $this->_output;
	}

	public function deleteDecklist(int $id)
    {
        try 
        {
            $del = $this->_db->prepare("DELETE FROM decklists WHERE id = :id");
            $del->execute(
                [
                    ':id' => $id
                ]
            );

            $this->_output = ($del->rowCount() > 0) ? true : false;
        } 
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

	public function getTeamlists()
	{
		try
		{
			$stmt = $this->_db->prepare("SELECT id, Team_name, Teamlist, season, played, wins, losses, ties, (wins / (wins + losses + ties)) AS win_percent FROM Teamlists");
			$stmt->execute();

			$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

			return $result;
		} catch (\Exception $e) {
			new Exceptions\DBException($e->getMessage());

			return [];
		}
	}

	public function getTeam(string $id)
	{
		try 
		{
			$stmt = $this->_db->prepare("SELECT id, Team_name, Teamlist, season, played, wins, losses, ties, (wins / (wins + losses + ties)) AS win_percent FROM Teamlists WHERE id = :id");
			$stmt->execute(
				[
					':id' => $id
				]
			);

			$result = $stmt->fetch(\PDO::FETCH_ASSOC);

			return $result;
		} catch (\Exception $e) {
			new Exceptions\DBException($e->getMessage());

			return [];
		}
	}

	public function addTeamlist(array $details)
	{
		try
		{
			$ins = $this->_db->prepare("INSERT INTO Teamlists (Team_name, Teamlist, season) VALUES(:Teamname, :Teamlist, :season)");
			$ins->execute(
				[
					':Teamname' => $details['Team_name'],
					':Teamlist' => $details['Teamlist'],
					':season'   => $details['season']
				]
			);

			$this->_output = ($ins->rowCount() > 0) ? true : false;
        }
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
	}

	public function updateTeamResults(array $details)
	{
		try 
		{
			$upd = $this->_db->prepare("UPDATE Teamlists SET wins = wins + :win, losses = losses + :loss, ties = ties + :tie, played = 1 WHERE id = :id");
			$upd->execute(
				[
					':id'   => $details['id'],
					':win'  => $details['win'],
					':loss' => $details['loss'],
					':tie'  => $details['tie']
				]
			);

				$this->_output = ($upd->rowCount() > 0) ? true : false;
			}
			catch(\PDOException $e)
			{
				$this->_output = $e->getMessage();
			}
	
			return $this->_output;
	}

	public function deleteTeamlist(int $id)
    {
        try 
        {
            $del = $this->_db->prepare("DELETE FROM Teamlists WHERE id = :id");
            $del->execute(
                [
                    ':id' => $id
                ]
            );

            $this->_output = ($del->rowCount() > 0) ? true : false;
        } 
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }
}