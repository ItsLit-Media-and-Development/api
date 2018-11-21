<?php
/**
 * Wrigglemania's Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 0.8
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Wriggle extends Library\BaseController
{
    private $_db;

    public function __construct()
    {
		parent::__construct();

        $this->_db = new Model\WriggleModel();
    }

    /**
     * Records a user that draws a card from the deck
     *
     * @return array|string
     * @throws \Exception
     */
    public function draw()
    {
        $this->_log->set_message("Wriggle::draw() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $card = $this->_params[0];
        $user = $this->_params[1];
        $victim = (isset($this->_params[3])) ? $this->_params[3] : '';
        $this->_output->setOutput((isset($this->_params[2])) ? $this->_params[2] : NULL);

        if($user != '' && $card != '')
        {
            $query = $this->_db->add_draw($user, $card);

            if(!is_string($query) && $query == true)
            {
                if($card != "steal" || $card != "swap")
                {
                    return $this->_output->output(200, "$user draw a card!");
                }
                else
                {
                    if($card == "steal")
                    {
                        return ($victim == '') ? $this->_output->output(200, "$user stole caps!") : $this->_output->output(200, "$user stole caps from " . $victim . "!");
                    }
                    else
                    {
                        return ($victim == '') ? $this->_output->output(200, "$user swapped caps!") : $this->_output->output(200, "$user swapped caps from " . $victim . "!");
                    }
                }
            }
            else
            {
                $this->_log->set_message("Something went wrong, PDO error: $query", "ERROR");

                return $this->_output->output(400, $query);
            }
        }
        else
        {
            $this->_log->set_message("URI is missing parameters, we have: $user, $card", "ERROR");

            return $this->_output->output(400, "URI is missing all its parameters... Should look like https://api.itslit.uk/Wriggle/draw/card/username/(optional)victim");
        }
    }

    /**
     * Reutrns the latest draw in the queue
     *
     * @return array|string The output
     * @throws \Exception
     */
    public function showlist()
    {
        $this->_log->set_message("Wriggle::showlist() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $bot = false;

        $cards = [];

        foreach($this->_params as $key => $param)
        {
            array_push($cards, $param);
        }

        $query = $this->_db->list_draw($cards);

        //lets actually check we have results!
        if(is_array($query))
        {
            return $this->_output->output(200, $query, $bot);
        }
        else
        {
            return $this->_output->output(200, "There are currently no draws", $bot);
        }
    }
}