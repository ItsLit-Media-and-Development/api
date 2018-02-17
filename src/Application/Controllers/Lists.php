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
        $qty = (isset($this->_params[2])) ? $this->_params[2] : "all";
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

    /**
     * Adds either a new list to the system or an item to a list
     * As it is an add, should technically be a POST setup but as bots dont handle that we cant atm
     *
     * @return array|string Output either confirming submission or returning an error
     * @throws \Exception
     */
    public function add()
    {
        $this->_log->set_message("Lists::add() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $type = $this->_params[0];
        $owner = $this->_params[1];
        $lName = $this->_params[2];

        //First lets see if it is a list or entry then input from there as the URL will vary
        if($type == 'newlist')
        {
            $bot = (isset($this->_params[3])) ? $this->_params[3] : false;

            $query = $this->_db->add_list($owner, $lName);

            if(is_bool($query))
            {
                return $this->_output->output(200, "List $lName has been created!", $bot);
            }
            else
            {
                return $this->_output->output(400, $query, $bot);
            }
        }
        elseif($type == 'addentry')
        {
            $name = $this->_params[3];
            $info = (isset($this->_params[4])) ? $this->_params[4] : '';

            $bot = (isset($this->_params[5])) ? $this->_params[5] : false;

            if(isset($this->_params[6]))
            {
                $this->_output->setOutput($this->_params[6]);
            }

            $query = $this->_db->add_entry($owner, $lName, $name, $info);

            if(is_bool($query))
            {
                $name = str_replace("%20", " ", $name);
                return $this->_output->output(200, "$name was added to $lName!", $bot);
            }
            else
            {
                return $this->_output->output(400, $query, $bot);
            }
        }
        else
        {
            //Something isn't right, throw an error!!!
            return $this->_output->output(400, "The add type was incorrect, only newlist or addentry is accepted", false);
        }
    }

    /**
     * Returns an item out of the specified list, throws a 204 error if the info field is left empty
     *
     * @return array|string Output either information, a 204 confirmation or returning an error on no result
     * @throws \Exception
     */
    public function getItem()
    {
        $owner = $this->_params[0];
        $lName = $this->_params[1];
        $item = $this->_params[2];

        $bot = (isset($this->_params[3])) ? $this->_params[3] : false;

        if(isset($this->_params[4]))
        {
            $this->_output->setOutput($this->_params[4]);
        }

        $query = $this->_db->get_item($owner, $lName, $item);

        return $this->_output->output(200, $query, $bot);
    }
}