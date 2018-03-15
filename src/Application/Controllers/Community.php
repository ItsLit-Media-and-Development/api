<?php
/**
 * Community Endpoint
 *
 * @package        API
 * @author        Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright    Copyright (c) 2018 Marc Towler
 * @license        https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link        https://api.itslit.uk
 * @since        Version 1.0
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Community
{
    public $service;
    private $_db;
    private $_params;
    private $_output;
    private $_log;

    public function __construct()
    {
        $tmp = new Library\Router();
        $this->_db = new Model\CommunityModel();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
        $this->_log = new Library\Logger();
    }

    public function __destruct()
    {
        $this->_log->saveMessage();
    }

    public function get_points()
    {
        $this->_log->set_message("Community::get_points() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $botService = $this->_params[0];
        $channel = $this->_params[1];
        $type = $this->_params[2]; //Either channel or user
        $qParam = $this->_params[3];
        $bot = (isset($this->_params[4])) ? $this->_params[4] : true;

        if(isset($this->_params[5]))
        {
            $this->_output->setOutput($this->_params[5]);
        }

        switch($bot)
        {
            case 'streamelements':
                $this->service = new Library\Streamelements();
                break;
            case 'streamlabs':
                $this->service = new Library\Streamlabs();
                break;
        }

        $this->service->set_channel_id($channel);

        if($type == 'channel')
        {
            $output = $this->service->get_channel_points($qParam);

            if($output != false)
            {
                return $this->_output->output(400, "Unable to retrieve data, is the channel ID correct?", $bot);
            }
            else
            {
                return $this->_output->output(200, $output, $bot);
            }
        }
        elseif($type == 'user')
        {
            $output = $this->service->get_user_points($qParam);

            if($output != false)
            {
                return $this->_output->output(400, "Unable to retrieve data, is the channel ID correct?", $bot);
            }
            else
            {
                return $this->_output->output(200, $output, $bot);
            }
        }

        return $this->_output->output(400, "URL is malformed", $bot);
    }
}