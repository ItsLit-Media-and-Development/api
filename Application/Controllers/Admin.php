<?php
/**
 * Admin Endpoint
 *
 * @package        API
 * @author        Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright    Copyright (c) 2017 Marc Towler
 * @license        https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link        https://api.itslit.uk
 * @since        Version 0.7
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Admin
{
    private $_db;
    private $_params;
    private $_output;
    private $_token;
    private $_config;
    private $_log;

    public function __construct()
    {
        $tmp = new Library\Router();
        $this->_db = new Model\AdminModel();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
        $this->_token = new Library\JWT();
        $this->_config = new Library\Config();
        $this->_log = new Library\Logger();
    }

    public function __destruct()
    {
        $this->_log->saveMessage();
    }

    /**
     * Covers the router's default method incase a part of the URL was missed
     *
     * @return array|string
     * @throws \Exception
     */
    public function main()
    {
        $this->_log->set_message("Admin::main() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }

    /**
     * Creates the JWT token for a user
     *
     * @return array|string
     * @throws \Exception
     */
    public function create_token()
    {
        $this->_log->set_message("Admin::create_token() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        if(!isset($this->_params[0]))
        {
            $this->_log->set_message("No username called in Admin::create_token() returning 400", "WARNING");

            return $this->_output->output(400, "Missing the username! Refer to the docs", false);
        }

        $user = $this->_params[0];
        $level = (isset($this->_params[1])) ? $this->_params[1] : 1;

        $enc_token = $this->_token->encode(['user' => $user, 'level' => $level], $this->_config->getSettings('TOKEN'));

        $tmp = $this->_db->generate_token($user, $enc_token, $level);

        if($tmp === true)
        {
            return $this->_output->output(200, "Successfully created a new auth token for $user, their token is $enc_token", false);
        } elseif($tmp === false)
        {
            return $this->_output->output(500, "OOPS! Something stopped us creating the token, an admin has been notified.", false);
        } else
        {
            $this->_log->set_message("Something went wrong, here is the PDO error $tmp", "ERROR");

            return $this->_output->output(500, $tmp, false);
        }
    }

    /**
     * Retrieves logs from the database
     *
     * @return array|string
     * @throws \Exception
     */
    public function getLogs()
    {
        $this->_log->set_message("Admin::getLogs() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }
}