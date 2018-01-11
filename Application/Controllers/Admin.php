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

    /**
     * Creates the JWT token for a user
     *
     * @return array|string
     * @throws \Exception
     */
    public function create_token()
    {
        if(!isset($this->_params[0]))
        {
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
            return $this->_output->output(500, $tmp, false);
        }
    }
}