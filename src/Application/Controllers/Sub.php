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
    private $_log;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
        $this->_db     = new Model\SubModel();
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
        $this->_log->set_message("Sub::main() called from " . $_SERVER['REMOTE_ADDR'] . ", 501 returned", "INFO");

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
        $this->_log->set_message("Sub::tier() called from " . $_SERVER['REMOTE_ADDR'] . " for $user, 501 returned", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }

    public function listgames()
    {
        $this->_log->set_message("Sub::listgames() called from " . $_SERVER['REMOTE_ADDR'] . ", 501 returned", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }

    public function queuegame()
    {
        $this->_log->set_message("Sub::queuegame() called from " . $_SERVER['REMOTE_ADDR'] . ", 501 returned", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }
}