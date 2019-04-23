<?php
/**
 * Index Model Class
 *
 * All database functions regarding the Index endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 0.6
 * @filesource
 */

namespace API\Model;

use API\Library;

class IndexModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

}