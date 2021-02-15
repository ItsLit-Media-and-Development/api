<?php
/**
 * User Endpoint
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

        $output = $this->_db->get_wow_stats();

        return $this->_output->output(200, $output, false);
    }

    public function getInvestment()
    {
        if(!$this->authenticate()) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->validRequest('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $user = $this->_params[0];

        $output = $this->_db->get_player_investment($user);

        return $this->_output->output(200, $output, false);
    }
}