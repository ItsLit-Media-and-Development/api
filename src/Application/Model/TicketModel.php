<?php
/**
 * Ticket Model Class
 *
 * All database functions regarding the Ticket endpoint is stored here
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

class TicketModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

	public function createTicket(array $data)
	{
		try 
		{
			$stmt = $this->_db->prepare("INSERT INTO ticket (name, email, message, status, submitted_at) VALUES (:name, :email, :message, 0, now())");
			$stmt->execute(
				[
					':name'    => $data['name'],
					':email'   => $data['email'],
					':message' => $data['message']
				]
			)

			$this->_output = ($stmt->rowCount() > 0) ? true : false;
		}
		catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

		return $this->_output;
	}

	public function viewTicket($id)
	{
		try 
		{
			$stmt = $this->_db->prepare("SELECT name, email, message, status, submitted_at FROM ticket WHERE id = :id");
			$stmt->execute(
				[
					':id' => $id
				]
			);

			$this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);

		}
		catch(\PDOException $e)
		{
			$this->_output = $e->getMessage();
		}

		return $this->_output;
	}

	public function listTickets()
	{
		try
		{
			$stmt = $this->_db->prepare("SELECT * FROM ticket");
			$stmt->execute();

			$this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);
		}
		catch(\PDOException $e)
		{
			$this->_output = $e->getMessage();
		}

		return $this->_output;
	}

	public function deleteTicket($id)
	{
		try
		{
			$del = $this->_db->prepare("DELETE FROM ticket WHERE id = :id");
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

	public function toggleTicket($id)
	{
		try
		{
			$upd = $this->_db->prepare("UPDATE ticket SET status = !status WHERE id = :id");
			$upd->execute(
				[
					':id' => $id
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
}