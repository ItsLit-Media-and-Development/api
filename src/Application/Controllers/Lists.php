<?php
/**
 * Lists Endpoint
 *
 * @package      API
 * @author       Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright    Copyright (c) 2017 Marc Towler
 * @license      https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link         https://api.itslit.uk
 * @since        Version 0.8
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Lists
{
    private $_db;
    private $_params;
    private $_output;
    private $_log;

    public function __construct()
    {
        $tmp = new Library\Router();
        $this->_db = new Model\ListModel();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
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
        $this->_log->set_message("Wriggle::main() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }

    /**
     * Reutrns all entries in the specified list
     *
     * @return array|string The output of the list
     * @throws \Exception
     */
    public function getList()
    {
        $this->_log->set_message("Lists::getList() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $owner = $this->_params[0];
        $lName = $this->_params[1];
        $qty = $this->_params[2];
        $bot = false;

        if(isset($this->_params[3]))
        {
            if(is_bool($this->_params[3]))
            {
                $bot = $this->_params[3];

                if(isset($this->_params[4]))
                {
                    $this->_output->setOutput($this->_params[4]);
                }
            }
            else
            {
                $this->_output->setOutput($this->_params[3]);
            }
        }

        $query = $this->_db->get_list($owner, $lName, $qty);

        if(is_array($query))
        {
            return $this->_output->output(200, $query, $bot);
        }
        else
        {
            return $this->_output->output(200, "There are currently no items in the $lName list", $bot);
        }
    }
}