<?php
/**
 * Event Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2025 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 2.0
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Event extends Library\BaseController
{
    protected $_db;

    public function __construct()
    {
        parent::__construct();

        $this->_db = new Model\EventModel();
    }

    public function listEvents()
    {
        //if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $events['Applications'] = $this->_db->listEventApplications();
        $events['EventCosts']   = $this->_db->listEventCosts();

        return $this->_output->output(200, $events, false);
    }

    public function summarizeEvents()
    {
        //if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $events = $this->_db->summarizeEvents();

        return $this->_output->output(200, $events, false);
    }
}