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
		$stmt = $this->_db->prepare("SELECT * FROM pkmn_events WHERE sanction_id = :id");
		$stmt->execute(
			[
				':id' => $id
			]
		);
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
}