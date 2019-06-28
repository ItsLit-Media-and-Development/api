<?php
/**
 * Bungie Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Bungie extends Library\BaseController
{
	private $_d2;

	public function __construct()
	{
		parent::__construct();

		//$this->_d2 = new Library\Bungie();
	}
}