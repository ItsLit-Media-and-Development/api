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

    public function get_current_stats()
    {
        $stmt = $this->_db->prepare("SELECT `name`, worth, investment FROM shop_titans WHERE last_updated >= DATE_ADD(CURDATE(), INTERVAL -6 DAY) ORDER BY last_updated DESC");
        $stmt->execute();

        $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->_output;
    }

    public function get_past_stats()
    {
        $stmt = $this->_db->prepare("SELECT `name`, worth, investment, last_updated FROM shop_titans WHERE last_updated < DATE_ADD(CURDATE(), INTERVAL -6 DAY) AND last_updated >= DATE_ADD(CURDATE(), INTERVAL -15 DAY) ORDER BY last_updated DESC");
        $stmt->execute();

        $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->_output;
    }

    public function get_player_investment($user)
    {
        $stmt = $this->_db->prepare("SELECT worth, investment FROM shop_titans WHERE name = :user ORDER BY last_updated DESC LIMIT 1");
        $stmt->execute([':user' => $user]);

        $this->_output = ($stmt->rowCount() > 0) ? $stmt->fetch(\PDO::FETCH_ASSOC) : 0;

        return $this->_output;
    }

    public function get_gc()
    {
        $stmt = $this->_db->prepare("SELECT `name` FROM shop_titans_gc WHERE completed = false ORDER BY submitted_on ASC");
        $stmt->execute();

        $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->_output;
    }

    public function addToList($user, $building)
    {
        $ins = $this->_db->prepare("INSERT INTO shop_titans_gc (`name`, submitor, completed_on, completed) VALUES(:building, :user, '0000', 0)");
        $ins->execute(
            [
                ':user' => $user,
                ':building' => $building
            ]
            );

        $this->_output = ($ins->rowCount() > 0) ? true : false;

        return $this->_output;
    }

    public function markComplete()
    {
        $stmt = $this->_db->prepare("SELECT `name` FROM shop_titans_gc WHERE completed = false ORDER BY submitted_on ASC LIMIT 1");
        $stmt->execute();

        $gc = $stmt->fetch();

        $upd = $this->_db->prepare("UPDATE shop_titans_gc SET completed = 1, completed_on = Now() WHERE `name` = :gc");
        $upd->execute([':gc' => $gc[0]]);

        $this->_output = ($upd->rowCount() > 0) ? true : false;
        
        return $this->$gc[0];
    }
}