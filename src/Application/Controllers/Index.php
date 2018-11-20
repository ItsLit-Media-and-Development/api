<?php
/**
 * Default Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 0.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Index
{
    private $_params;
    private $_output;
    private $_db;
    private $_log;

    public function __construct()
    {
        $tmp = new Library\Router();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
        $this->_db = new Model\IndexModel();
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
        $this->_log->set_message("Index::main() called from " . $_SERVER['REMOTE_ADDR'] . ", 501 returned", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }

}