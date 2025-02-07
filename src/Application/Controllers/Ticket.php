<?php
/**
 * Ticket Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2019 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 1.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Ticket extends Library\BaseController
{
    private $_db;

    public function __construct()
    {
        parent::__construct();

        $this->_db = new Model\TicketModel();
    }

    public function createTicket()
    {
        if(!$this->authenticate(3)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        //Quick check to make sure the data is not empty
        if(!isset($data) || empty($data))
        {
            return $this->_output->output(400, "No Data sent", false);
        }

        $output = $this->_db->createTicket($data);

        //Check to see if we had an error
        if(!is_bool($output))
        {
            return $this->_output->output(500, $output, false);
        }

        if($output === false)
        {
            return $this->_output->output(400, $output, false);
        }

        return $this->_output->output(200, $output, false);
    }

    public function viewTicket()
    {
        if(!$this->authenticate(3)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $id = $this->_params[0];

        //Check it is actually a number
        if(filter_var($id, FILTER_VALIDATE_INT) === false)
        {
            return $this->_output->output(400, "Ticket ID should be numeric", false);
        }

        $ticket = $this->_db->viewTicket($id);

        if($ticket === false)
        {
            return $this->_output->output(404, "Ticket ID {$id} not found", false);
        }

        return $this->_output->output(200, $ticket, false);
    }

    public function listTickets()
    {
        if(!$this->authenticate(3)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $ticket = $this->_db->listTickets();

        if($ticket === false)
        {
            return $this->_output->output(404, "No tickets have been found", false);
        }

        return $this->_output->output(200, $ticket, false);
    }

    public function deleteTicket()
    {
        if(!$this->authenticate(3)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('DELETE')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $id = $this->_params[0];

        //Check it is actually a number
        if(filter_var($id, FILTER_VALIDATE_INT) === false)
        {
            return $this->_output->output(400, "Ticket ID should be numeric", false);
        }

        $result = $this->_db->deleteTicket($id);

        return ($result) ? $this->_output->output(204, "", false) : $this->_output->output(404, "Ticket not found", false);
    }

    public function toggleStatus()
    {

    }
}