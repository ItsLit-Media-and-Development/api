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

    public function create()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }

        parse_str(file_get_contents('php://input'), $data);

        $this->_db->create_ticket($data);
        
        return $this->_output->output(200, ['success' => true], (isset($this->_param[0]) ? $this->_params[0] : false));
    }
}