<?php
/**
 * Clients Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;
use GuzzleHttp\Client;

class Clients extends Library\BaseController
{
    private $_g;

	public function __construct()
	{
        parent::__construct();
        
        $this->_db = new Model\ClientsModel();
        $this->_g  = new Client();
    }
    
    public function add_win()
    {
        $data = $this->_db->modifyData('add', 'win');

        return $this->_output->output(200, "Another win added", true);
    }

    public function add_loss()
    {
        $data = $this->_db->modifyData('add', 'loss');

        return $this->_output->output(200, "Another loss added", true);
    }

    public function remove_win()
    {
        $data = $this->_db->modifyData('remove', 'win');

        return $this->_output->output(200, "Win removed", true);
    }

    public function remove_loss()
    {
        $data = $this->_db->modifyData('remove', 'loss');

        return $this->_output->output(200, "Loss removed", true);
    }

    public function get_win_loss()
    {
        $data = $this->_db->getData();

        return $this->_output->output(200, $data, false);
    }
}