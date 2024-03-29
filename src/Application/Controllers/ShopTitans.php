<?php
/**
 * Shop Titans Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2021 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 1.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;


class ShopTitans extends Library\BaseController
{
    private $_db;
    protected $_config;

    public function __construct()
    {
		parent::__construct();

        $this->_config = new Library\Config();
        $this->_db     = new Model\ShopTitansModel();
    }

    public function getAllUsers()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $output = $this->_db->get_players();

        return $this->_output->output(200, $output, false);
    }

    public function getUser()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $user = $this->_params[0];

        $output = $this->_db->get_player($user);

        return $this->_output->output(200, $output, false);
    }

    public function setUser()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $input = json_decode(file_get_contents('php://input'), true);
    }

    public function getWow()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $cur = $this->_db->get_current_stats();
        $last = $this->_db->get_past_stats();

        $last_invest = [];
        $vs_plan = [];

        for($i = 0; $i < sizeof($last); $i++)
        {
            $last_invest[$last[$i]['name']] = round($last[$i]['investment'] + ($last[$i]['worth'] * 0.02),0);
            var_dump($last[$i]['name'] . " investment " . $last[$i]['investment'] . " net worth " . $last[$i]['worth'] . " calc " . $last[$i]['worth'] * 0.02);
        }

        var_dump($last_invest);

        for($j = 0; $j < sizeof($cur); $j++)
        {
            if($cur[$j]['investment'] - ((isset($last_invest[$cur[$j]['name']]) ? $last_invest[$cur[$j]['name']] : $cur[$j]['investment'])) <= 0)
            {
                if(!isset($last_invest[$cur[$j]['name']]))
                {
                    $vs_plan[] = $cur[$j]['name'] . " (new?)";
                } else 
                {
                    $vs_plan[] = $cur[$j]['name'];
                }
            }
        }

        return $this->_output->output(200, $vs_plan, false);
    }

    public function getInvestment()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $user = $this->_params[0];

        $output = $this->_db->get_player_investment($user);

        return $this->_output->output(200, $output, false);
    }

    public function getGcList()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $output = $this->_db->get_gc();

        return $this->_output->output(200, $output, false);
    }

    public function addToGcList()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }
        if(!$this->hasBody()) { return $this->_output->output(400, "Bad Request", false); }

        $output = $this->_db->addToList($this->_input['user'], $this->_input['building']);

        return $this->_output->output(200, $output, false);
    }

    public function markComplete()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $output = $this->_db->markComplete();

        return $this->_output->output(200, $output, false);
    }

    public function getEventInfo()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $event = $this->_params[0];

        if($event != "caprice" && $event != "cityofgold")
        {
            return $this->_output->output(400, "Incorrect event type requested", false);
        }

        $output = $this->_db->getEventScore($event);

        return $this->_output->output(200, $output, false);
    }

    public function WebsiteUpdate()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }
        if(!$this->hasBody()) { return $this->_output->output(400, "Bad Request", false); }
        
        /* //check this works ok still
        parse_str($this->_input, $data);

        //Something weird is happening to the JSON POST data, its all being stored in the key for some reason, so I need to extract and decode just the key to pass to the DB
        $data = json_decode(key($data), true); */

        $data = $this->_input;

        $return = $this->_db->updateAll($data);

        if($return == null)
        {
            return $this->_output->output(400, 'Bad Request', false);
        }
        
        return $this->_output->output(200, ['success' => true], (isset($this->_param[0]) ? $this->_params[0] : false));
    }
}