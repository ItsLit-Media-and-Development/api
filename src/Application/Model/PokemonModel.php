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

	public function addEvent(array $payload)
	{

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

	public function addEvent(array $details)
	{
		try
		{
			$ins = $this->_db->prepare("INSERT INTO events (Event_Name, Event_Dates, Event_Location, Event_Link, TCG, VGC, POGO, Unite, Spectator) VALUES(:Event_Name, :Event_Dates, :Event_Location, :Event_Link, :TCG, :VGC, :POGO, :Unite, :Spectator)");

			foreach($details as $d)
			{
				$ins->execute(
					[
						':Event_Name'     => $d['Event Name'], 
						':Event_Dates'    => $d['Event Dates'], 
						':Event_Location' => $d['Event Location'], 
						':Event_Link'     => $d['Event'], 
						':TCG'            => $d['TCG'], 
						':VGC'            => $d['VGC'], 
						':POGO'           => $d['GO'], 
						':Unite'          => (isset($d['Unite']) ? $d['Unite'] : null), 
						':Spectator'      => $d['Spectator']
					]
				)
			}
		}
		catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }
	}
}