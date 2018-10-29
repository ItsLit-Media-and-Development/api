<?php
/**
 * League of Legends Endpoint
 *
 * @package       API
 * @author        Marc Towler <marc@marctowler.co.uk>
 * @copyright     Copyright (c) 2018 Marc Towler
 * @license       https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link          https://api.itslit.uk
 * @since         Version 1.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;

class Lol
{
    private $_params;
    private $_output;
    private $_log;
    private $_riot;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
        $this->_log    = new Library\Logger();
        $this->_riot   = new Library\Riot();
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
        $this->_log->set_message("Twitch::main() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }

    /**
     * Returns a list of all champions currently live in LoL
     *
     * @return array
     */
    public function getChampions()
    {
        $this->_log->set_message("Lol::getChampions() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        //Set the platform
        $this->_riot->setPlatform($this->_params[0]);
        $bot = $this->_params[1];

        $output = $this->_riot->get('platform/v3/champions');

        return $this->_output->output(200, $output, $bot);
    }

    /**
     * Returns a list of all free champions currently live in LoL
     *
     * @return array
     */
    public function getFreeChampions()
    {
        $this->_log->set_message("Lol::getFreeChampions() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        //Set the platform
        $this->_riot->setPlatform($this->_params[0]);
        $bot = $this->_params[1];

        $output = $this->_riot->get('platform/v3/champions?freeToPlay=true');

        return $this->_output->output(200, $output, $bot);
    }
}