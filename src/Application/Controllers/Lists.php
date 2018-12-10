<?php
/**
 * Lists Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 0.8
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Lists extends Library\BaseController
{
    private $_db;

    public function __construct()
    {
		parent::__construct();

        $this->_db = new Model\ListModel();
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
        $bot = (isset($this->_params[3])) ? $this->_params[3] : false;

        if(isset($this->_params[4]))
        {
            $this->_output->setOutput($this->_params[4]);
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

    public function remove()
    {
        $this->_log->set_message("Lists::remove() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $type = $this->_params[0];
        $owner = $this->_params[1];
        $lName = $this->_params[2];

        if($type == 'item')
        {
            $name = $this->_params[3];

            $bot = (isset($this->_params[4])) ? $this->_params[4] : false;

            $this->_output->setOutput((isset($this->_params[5])) ? $this->_params[5] : NULL);

            $query = $this->_db->delete_item($owner, $lName, $name);

			//return ($query != NULL) ? $this->_output->output(200, str_replace("%20", " ", $name) . " was removed from the $lName list!", $bot) : $this->_output->output(400, $query, $bot);
			return ($query != NULL) ?
				$this->_output->output(200, urldecode($name) . " was removed from the $lName list!", $bot) :
				$this->_output->output(400, $query, $bot);
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

            return (is_bool($query)) ? $this->_output->output(200, "List $lName has been created!", $bot) : $this->_output->output(400, $query, $bot);
        }
        elseif($type == 'addentry')
        {
            $name = $this->_params[3];
            $info = (isset($this->_params[4])) ? $this->_params[4] : '';
            $bot = (isset($this->_params[5])) ? $this->_params[5] : false;

            $this->_output->setOutput((isset($this->_params[6])) ? $this->_params[6] : NULL);

            $query = $this->_db->add_entry($owner, $lName, $name, $info);

			//return (is_bool($query)) ? $this->_output->output(200, str_replace("%20", " ", $name) . " was added to the $lName list!", $bot) : $this->_output->output(400, $query, $bot);
			return (is_bool($query)) ?
				$this->_output->output(200, urldecode($name) . " was added to the $lName list!", $bot) :
				$this->_output->output(400, $query, $bot);
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
        $this->_log->set_message("Lists::getItem() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $owner = $this->_params[0];
        $lName = $this->_params[1];
        $item = $this->_params[2];
        $bot = (isset($this->_params[3])) ? $this->_params[3] : false;

        $this->_output->setOutput((isset($this->_params[4])) ? $this->_params[4] : NULL);

        return $this->_output->output(200, $this->_db->get_item($owner, $lName, $item), $bot);
    }

    /**
     * Get a random item from the specified list
     *
     * @return array The item specified
     * @throws \Exception
     */
    public function randItem()
    {
        $this->_log->set_message("Lists::randItem() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $owner = $this->_params[0];
        $lName = $this->_params[1];
        $bot = (isset($this->_params[2])) ? $this->_params[2] : false;

        $this->_output->setOutput((isset($this->_params[3])) ? $this->_params[3] : NULL);

        return $this->_output->output(200, $this->_db->get_random_item($owner, $lName), $bot);
    }

    /**
     * Allows a list to be searched by what is stored in it's info column
     *
     * @return array|string
     * @throws \Exception
     */
    public function getItemByInfo()
    {
        $this->_log->set_message("Lists::getItemByInfo() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $owner = $this->_params[0];
        $lName = $this->_params[1];
        $info = $this->_params[2];
        $bot = (isset($this->_params[3])) ? $this->_params[3] : false;

        $this->_output->setOutput((isset($this->_params[4])) ? $this->_params[4] : NULL);

        return $this->_output->output(200, $this->_db->get_item_by_info($owner, $lName, $info), $bot);
    }
}