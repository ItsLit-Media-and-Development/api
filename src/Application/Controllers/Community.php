<?php
/**
 * Community Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.0
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Community extends Library\BaseController
{
    public $service;
    private $_db;

    public function __construct()
    {
		parent::__construct();

        $this->_db = new Model\CommunityModel();
    }

    public function get_points()
    {
        $this->_log->set_message("Community::get_points() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $botService = $this->_params[0];
        $channel = $this->_params[1];
        $type = $this->_params[2]; //Either channel or user
        $qParam = (isset($this->_params[3])) ? $this->_params[3] : 1000;
        $bot = (isset($this->_params[4])) ? $this->_params[4] : false;

        $this->_output->setOutput((isset($this->_params[5])) ? $this->_params[5] : NULL);

        switch($botService)
        {
            case 'streamelements':
                $this->service = new Library\Streamelements();


				$this->service->set_token($this->_db->get_SE_Token($channel));
				//$this->service->set_channel_id('599537d2d0cacf1582b82b0d');
                break;
            case 'streamlabs':
                $this->service = new Library\Streamlabs();

				$this->service->authorise($qParam);
                break;
        }

        $this->service->set_channel_id($channel);

        $output = ($type == 'channel') ? $this->service->get_channel_points($qParam) : $this->service->get_user_points($qParam);

        return ($output == false) ? $this->_output->output(400, "Unable to retrieve data, is the channel ID correct?", $bot) : $this->_output->output(200, $output, $bot);
    }

    public function getItems()
    {
        $this->service = new Library\Streamelements();

        $channel = $this->_params[0];

        $output = $this->service->get_store_items($channel);

        var_dump($output);
    }

    public function redeem()
    {
        $this->service = new Library\Streamelements();

        $channel = $this->_params[0];
        $item    = $this->_params[1];

        $output = $this->service->redeem($channel, $item);

        var_dump($output);
    }
}