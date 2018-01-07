<?php
/**
 * Statistics Endpoint
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

class Stats
{
    private $_db;
    private $_config;
    private $_params;
    private $_output;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_config = new Library\Config();
        $this->_db     = $this->_config->database();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
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
     * Synxiec: "How many viewers do I have for each time I play these games so I can correlate my viewership to the games I play"
    "How many viewers are unique versus non-unique? I want to be able to determine which games are my staples and which ones tend to bring newer audiences"
    "Can I break this down by genre as well as individual games?"
    "Can I have a weekly, monthly, quarterly, and annual view of the above information?"(edited)
    [21:23] Synxiec: "Can I have teams? If so, can I see this information for individuals and for the entire team?"
    "Does this account for co-streams with other streamers?"
     */
}