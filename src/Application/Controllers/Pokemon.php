<?php
/**
 * Pokemon Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2023 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 2.0
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Pokemon extends Library\BaseController
{
    protected $_db;

    public function __construct()
    {
        parent::__construct();

        $this->_db = new Model\PokemonModel();
    }

    //Pull the events that have been stored in the database from the website scrape
    public function listEvents()
    {
        if(!$this->authenticate(1)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        
    }

    //Add event, potentially via automation or via manually
    public function addEvent()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }

        
    }

    //Update event details, add standings etc
    public function updateEvent()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }

    //Delete event, soft delete only
    public function deleteEvent()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('DELETE')) { return $this->_output->output(405, "Method Not Allowed", false); }

        
    }

    public function addPlayers()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }

    public function updatePlayers()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }

    public function deletePlayers()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('DELETE')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }
}