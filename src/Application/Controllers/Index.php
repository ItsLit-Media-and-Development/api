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

class Index extends Library\BaseController
{
	//private $_db;

    public function __construct()
    {
		parent::__construct();

		//$this->_db = new Model\IndexModel();
    }
}