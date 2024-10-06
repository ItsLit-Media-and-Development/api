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
        //if(!$this->authenticate(1)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $test = file_get_contents('https://op-core.pokemon.com/api/v2/event_locator/search/?distance=50&format=api&latitude=53.8007554&longitude=-1.5490774');
        echo $test;
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

    public function getDeck()
    {
        //if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $id = $this->_params[0];

        //Check it is actually a number
        if(filter_var($id, FILTER_VALIDATE_INT) === false)
        {
            return $this->_output->output(400, "Post ID should be numeric", false);
        }

        $data =  $this->_db->getDeck($id);

        return $this->_output->output(200, $data, false);
    }

    public function getDecklists()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = $this->_db->getDecklists();

        return $this->_output->output(200, $data, false);
    }

    public function addDecklist()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        //Quick check to make sure the data is not empty
        if(!isset($data) || empty($data))
        {
            return $this->_output->output(400, "No Data sent", false);
        }

        //Check that the minimum data points are present
        if(!array_key_exists('deck_name', $data) || !array_key_exists('decklist', $data) || !array_key_exists('season', $data))
        {
            return $this->_output->output(400, "Missing data", false);
        }

        $output = $this->_db->addDecklist($data);

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

    public function updateDecklist()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }

    public function deleteDecklist()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('DELETE')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }

    public function getTeamlists()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }

    public function updateDeckResult()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        //Quick check to make sure the data is not empty
        if(!isset($data) || empty($data))
        {
            return $this->_output->output(400, "No Data sent", false);
        }

        $output = $this->_db->updateDeckResults($data);

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
}