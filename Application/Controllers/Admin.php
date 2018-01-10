<?php
/**
 * Administration Endpoint
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

    public function __construct()
    {
        $tmp = new Library\Router();
        $this->_db = new Model\AdminModel();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
        $this->_token = new Library\JWT();
        $this->_config = new Library\Config();
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    /**
     * Covers the router's default method incase a part of the URL was missed
     *
     * @return array|string
     * @throws \Exception
     */
    public function index()
    {
        return $this->_output->output(501, "Function not implemented", false);
    }

    public function getLogs()
    {

    }

    /**
     * Generates a JWT token for future authentication.
     *
     * @return array|string
     * @throws \Exception
     */
    public function generate_token()
    {
        //If the user isn't set then we cannot encrypt
        if(!isset($this->_params[0]))
        {
            return $this->_output->output(400, "Request is missing a required parameter, the username", false);
        } else
        {
            $user = $this->_params[0];
            $level = (isset($this->_params[1])) ? $this->_params[1] : 1;

            $tmp = ['user' => $user, 'level' => $level];
            $enc_token = $this->_token->encode($tmp, $this->_config->getSettings("TOKEN"));

            $output = $this->_db->insert_token($user, $enc_token, $level);

            if($output === true)
            {
                return $this->_output->output(200, "The token has been created for $user, it is: $enc_token \r\n Please keep this safe", false);
            } elseif($output === false)
            {
                return $this->_output->output(500, "Something went wrong generating a token, please try again later", false);
            } else
            {
                return $this->_output->output(400, $output, false);
            }
        }
    }
}