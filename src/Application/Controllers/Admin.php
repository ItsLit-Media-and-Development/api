<?php
/**
 * Admin Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 0.7
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Admin extends Library\BaseController
{
    private $_db;
    private $_token;
    private $_config;
    private $_header;
    private $_auth;

    public function __construct()
    {
		parent::__construct();

		$this->_db = new Model\AdminModel();
		$this->_token = new Library\JWT();
        $this->_config = new Library\Config();
		$this->_header = $this->_router->getAllHeaders();
		$this->_auth = new Library\Authentication();
    }

    /**
     * Retrieves logs from the database
     *
     * @return array|string
     * @throws \Exception
     */
    public function getLogs()
    {
        if($this->_auth->validate_token($this->_header['auth_token'], $this->_header['auth_user']) < 3)
        {
            return $this->_output->output(403, "Invalid auth_token");
        }
        
        $this->_log->set_message("Admin::getLogs() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $type = (isset($this->_params[0])) ? $this->_params[0] : false;
        $from = (isset($this->_params[1])) ? $this->_params[1] : false;
        $to = (isset($this->_params[2])) ? $this->_params[2] : false;

        //we know if 0 isnt set then the rest wont be
        if($type === false)
        {
            return $this->_output->output(400, "URI is malformed, please check the documents", false);
        }

        $output = $this->_db->get_logs($type, $from, $to);

        return (is_array($output)) ? $this->_output->output(200, $output, false) : (is_int($output)) ? $this->_output->output(204, "There are no logs right now!", false) : $this->_output->output(500, "Something went wrong, PDO error: $output", false);
    }

    public function registerAPIuser()
    {
        if(isset($_POST))
        {
            $this->_log->set_message("Called Admin::registerAPIuser() from " . $_SERVER['REMOTE_ADDR'], "INFO");

            $user_info = $_POST;

            $this->_output->setOutput((isset($user_info['return_output'])) ? $user_info['return_output'] : NULL);

            $user_info['token'] = $this->_auth->create_token($user_info['username'], $user_info['level']);

            $query = $this->_db->add_api_user($user_info);

            //Lets see if it worked or not
            return (is_integer($query) && $query > 0) ? $this->_output->output(201, "API User was created, pending approval", false) : $this->_output->output(500, "Something went wrong, PDO error: $query", false);
        }

        return $this->_output->output(400, "Resource can only be accessed via POST", false);
    }


    public function revokeToken()
    {
        if($this->_auth->validate_token($this->_header['auth_token'], $this->_header['auth_user']) == 3)
        {
            $this->_log->set_message("Admin::revokeToken() Called from " . $_SERVER['REMOTE_ADDR'] . " by " . $this->_header['auth_user'], "INFO");

            $user = $this->_params[0];

            $this->_output->setOutput((isset($this->_params[1]) ? $this->_params[1] : NULL));

            $query = $this->_db->revoke_token($user);

            return (is_integer($query) && $query > 0) ? $this->_output->output(200, "Token revoked", false) : $this->_output->output(500, "Something went wrong, PDO error: $query", false);
        }
    }
}