<?php
/**
 * Sub Model Class
 *
 * All database functions regarding the Sub endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2017 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 0.3
 * @filesource
 */

namespace API\Model;

use API\Library;

class SubModel
{
    private $_db;
    private $_config;

    public function __construct()
    {
        $this->_config = new Library\Config();
        $this->_db     = $this->_config->database();
    }
}