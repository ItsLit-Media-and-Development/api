<?php
/**
 * Shop Titans Model Class
 *
 * All database functions regarding the Shop Titans endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.1
 * @filesource
 */

namespace API\Model;

use API\Library;

class ShopTitansModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
    }

    public function get_player($name)
    {
        $stmt = $this->_db->prepare("SELECT * FROM shop_titans WHERE name = :name ORDER BY last_updated DESC LIMIT 1");
        $stmt->execute([':name' => $name]);

        $this->_output = ($stmt->rowCount() > 0) ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : 0;

        return $this->_output;
    }
}