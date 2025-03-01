<?php
/**
 * Event Model Class
 *
 * All database functions regarding the Event endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2025 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 2.0
 * @filesource
 */

namespace API\Model;

use API\Library;

class EventModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

    public function listEventApplications()
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT ea.*, ROUND( SUM(CASE WHEN ec.entry_type = 'income' THEN ec.value ELSE 0 END) - SUM(CASE WHEN ec.entry_type = 'cost' THEN ec.value ELSE 0 END), 2 ) AS profit FROM event_applications ea LEFT JOIN event_costs ec ON ea.eaid = ec.eid GROUP BY ea.eaid");
            $stmt->execute();

            $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function listEventCosts()
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT * FROM event_costs");
            $stmt->execute();

            $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function summarizeEvents()
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT event_type, season, concat(round(((sum(invited) / sum(applied)) * 100),2), '%') as ApplicationSuccess FROM `event_applications` group by event_type, season");
            $stmt->execute();

            $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\PDOException $e)
        {
            $this->_ouput = $e->getMessage();
        }

        return $this->_output;
    }
}