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

        $data = json_decode(file_get_contents('php://input'), true);

        //Quick check to make sure the data is not empty
        if(!isset($data) || empty($data))
        {
            return $this->_output->output(400, "No Data sent", false);
        }

        $output = $this->_db->updateDecklist($data);

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

    public function deleteDecklist()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('DELETE')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $id = $this->_params[0];

        //Check it is actually a number
        if(filter_var($id, FILTER_VALIDATE_INT) === false)
        {
            return $this->_output->output(400, "Decklist ID should be numeric", false);
        }

        $result = $this->_db->deleteDecklist($id);

        return ($result) ? $this->_output->output(204, true, false) : $this->_output->output(404, ["Deck List not found"], false);
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

    public function getTeamlists()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = $this->_db->getTeamlists();

        return $this->_output->output(200, $data, false);
    }

    public function getTeam()
    {
        //if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $id = $this->_params[0];

        //Check it is actually a number
        if(filter_var($id, FILTER_VALIDATE_INT) === false)
        {
            return $this->_output->output(400, "Post ID should be numeric", false);
        }

        $data =  $this->_db->getTeam($id);

        return $this->_output->output(200, $data, false);
    }

    public function addTeamlist()
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
        if(!array_key_exists('Team_name', $data) || !array_key_exists('Teamlist', $data) || !array_key_exists('season', $data))
        {
            return $this->_output->output(400, "Missing data", false);
        }

        $output = $this->_db->addTeamlist($data);

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

    public function updateTeamlist()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        //Quick check to make sure the data is not empty
        if(!isset($data) || empty($data))
        {
            return $this->_output->output(400, "No Data sent", false);
        }

        $output = $this->_db->updateTeamlist($data);

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

    public function deleteTeamlist()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('DELETE')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $id = $this->_params[0];

        //Check it is actually a number
        if(filter_var($id, FILTER_VALIDATE_INT) === false)
        {
            return $this->_output->output(400, "Teamlist ID should be numeric", false);
        }

        $result = $this->_db->deleteTeamlist($id);

        return ($result) ? $this->_output->output(204, true, false) : $this->_output->output(404, ["Team List not found"], false);
    }

    public function updateTeamResult()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        //Quick check to make sure the data is not empty
        if(!isset($data) || empty($data))
        {
            return $this->_output->output(400, "No Data sent", false);
        }

        $output = $this->_db->updateTeamResults($data);

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

    public function ptcglConverter()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        //Quick check to make sure the data is not empty
        if(!isset($data) || empty($data) || !isset($data['decklist']))
        {
            return $this->_output->output(400, "No Data sent", false);
        }

        //Split the string to get pokemon, trainers and energy into seperate arrays... Need to make this cleaner at some point
        $pksplit = explode("Trainer: ", $data['decklist']);
        $trsplit = explode("Energy: ", $pksplit[1]);

        $pokemon = explode("\n", substr($pksplit[0], 11));
        $trainer = explode("\n", substr($trsplit[0], 2));
        $energy  = explode("\n", substr($trsplit[1], 2));

        $pkmn = [];
        $trnr = [];
        $enrg = [];

        for($i = 0; $i < sizeof($pokemon); $i++)
        {
            $tmp = explode(" ", $pokemon[$i]);

            if(sizeof($tmp) === 4)
            {
                $pkmn[$i]["Qty"]  = $tmp[0];
                $pkmn[$i]["Name"] = $tmp[1];
                $pkmn[$i]["Set"]  = $tmp[2];
                $pkmn[$i]["Num"]  = $tmp[3];
            } 
            elseif(sizeof($tmp) === 5) 
            {
                $pkmn[$i]["Qty"]  = $tmp[0];
                $pkmn[$i]["Name"] = $tmp[1] . " " . $tmp[2];
                $pkmn[$i]["Set"]  = $tmp[3];
                $pkmn[$i]["Num"]  = $tmp[4];
            }
        }

        for($i = 0; $i < sizeof($trainer); $i++)
        {
            $tmp = explode(" ", $trainer[$i]);

            if(sizeof($tmp) === 4)
            {
                $trnr[$i]["Qty"]  = $tmp[0];
                $trnr[$i]["Name"] = $tmp[1];
                $trnr[$i]["Set"]  = $tmp[2];
                $trnr[$i]["Num"]  = $tmp[3];
            } 
            elseif(sizeof($tmp) === 5) 
            {
                $trnr[$i]["Qty"]  = $tmp[0];
                $trnr[$i]["Name"] = $tmp[1] . " " . $tmp[2];
                $trnr[$i]["Set"]  = $tmp[3];
                $trnr[$i]["Num"]  = $tmp[4];
            }
            elseif(sizeof($tmp) === 6) 
            {
                $trnr[$i]["Qty"]  = $tmp[0];
                $trnr[$i]["Name"] = $tmp[1] . " " . $tmp[2] . " " . $tmp[3];
                $trnr[$i]["Set"]  = $tmp[4];
                $trnr[$i]["Num"]  = $tmp[5];
            }
        }

        for($i = 0; $i < sizeof($energy); $i++)
        {
            $tmp = explode(" ", $energy[$i]);

            if(sizeof($tmp) === 5)
            {
                $enrg[$i]["Qty"]  = $tmp[0];
                $enrg[$i]["Name"] = $tmp[1] . " " . $tmp[2];
                $enrg[$i]["Set"]  = $tmp[3];
                $enrg[$i]["Num"]  = $tmp[4];
            } 
            elseif(sizeof($tmp) === 6) 
            {
                $enrg[$i]["Qty"]  = $tmp[0];
                $enrg[$i]["Name"] = $tmp[1] . " " . $tmp[2] . " " . $tmp[3];
                $enrg[$i]["Set"]  = $tmp[4];
                $enrg[$i]["Num"]  = $tmp[5];
            }
        }

        $decklist['pokemon']  = $pkmn;
        $decklist['trainers'] = $trnr;
        $decklist['energy']   = $enrg;

        return $this->_output->output(200, $decklist, false);
    }

    public function rk9Events()
    {
        $dom = new \DomDocument();
        $tmp = file_get_contents("https://rk9.gg/events/pokemon");

        @$dom->loadHTML($tmp);

        $xpath = new \DOMXPath($dom);
        $table     = $xpath->query('//table[@id="dtUpcomingEvents"]')->item(0); //this or the line above strips links
        $baseURI   = "https://rk9.gg";
        $links     = [];
        $data      = [];
        $i         = 0;
        $j         = 5;
        
        $rows = $table->getElementsByTagName('tr');

        foreach ($rows as $row) 
        {
            // Iterate through the rows of the table
            $rows = $table->getElementsByTagName('tr');

            foreach ($rows as $row) 
            {
                $columns = $row->getElementsByTagName('td');

                $rowData = [];

                foreach ($columns as $col) 
                {
                        $rowData[] = trim($col->textContent);
                }

                if (!empty($rowData)) 
                {
                    unset($rowData[count($rowData) - 1]);

                    //ICs and above, unite comes in on 8 and spectators on 10 but at regionals, spectators are 9 so this checks what it is
                    if(strpos($xpath->query('//a/@href')->item($j + 4)->value, "spectator"))
                    {
                        $links['Event']     = $baseURI . $xpath->query('//a/@href')->item($j)->value;
                        $links['GO']        = $baseURI . $xpath->query('//a/@href')->item($j + 1)->value;
                        $links['TCG']       = $baseURI . $xpath->query('//a/@href')->item($j + 2)->value;
                        $links['VGC']       = $baseURI . $xpath->query('//a/@href')->item($j + 3)->value;
                        $links['Spectator'] = $baseURI . $xpath->query('//a/@href')->item($j + 4)->value;
                        $links['Unite']     = null;

                        $j += 5;
                    } else {
                        $links['Event']     = $baseURI . $xpath->query('//a/@href')->item($j)->value;
                        $links['GO']        = $baseURI . $xpath->query('//a/@href')->item($j + 1)->value;
                        $links['TCG']       = $baseURI . $xpath->query('//a/@href')->item($j + 2)->value;
                        $links['Unite']     = $baseURI . $xpath->query('//a/@href')->item($j + 3)->value;
                        $links['VGC']       = $baseURI . $xpath->query('//a/@href')->item($j + 4)->value;
                        $links['Spectator'] = null;

                        $j += 5;
                    }
                    $dateString = $rowData[0];

                    $data[$i]['Event Name']       = $rowData[2];
                    $data[$i]['Event Start Date'] = \DateTime::createFromFormat('F d, Y', substr($dateString, 0, strpos($dateString, '-')) . ", " . substr($dateString, -4)) ?: false;
                    $data[$i]['Event End Date']   = \DateTime::createFromFormat('F d, Y', substr($dateString, 0, strpos($dateString, '-')) . ", " . substr($dateString, -4)) ?: false;
                    $data[$i]['Event Location']   = $rowData[3];
                    $data[$i]['Links']            = $links;

                    //Add days to the end date depending on if it is an international or not - Current issue is US regionals show 3 days but all others 2
                    (strpos($rowData[2], "International") !== false) ? date_add($data[$i]['Event End Date'], date_interval_create_from_date_string("2 days")) : date_add($data[$i]['Event End Date'], date_interval_create_from_date_string("1 days"));

                    $i += 1;
                }
            }
        }

        $output = $this->_db->addEvent($data);

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