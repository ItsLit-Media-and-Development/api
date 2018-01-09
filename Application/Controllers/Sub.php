<?php
/**
 * Subscriber Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2017 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 0.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Sub
{
    private $_params;
    private $_output;
    private $_db;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
        $this->_db     = new Model\SubModel();
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
     * @TODO Implement the actual functionality
     * @param string $user
     * @return array|string
     * @throws \Exception
     */
    public function tier($user = '')
    {
        return $this->_output->output(501, "Function not implemented", false);
    }

    public function listgames()
    {
        return $this->_output->output(501, "Function not implemented", false);
    }

    public function queuegame()
    {
        return $this->_output->output(501, "Function not implemented", false);
    }
}