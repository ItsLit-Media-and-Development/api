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

    public function get_players()
    {
        $last_update = $this->_db->prepare("SELECT last_updated FROM shop_titans ORDER BY last_updated DESC limit 1");
        $last_update->execute();

        $stmt = $this->_db->prepare("SELECT * FROM shop_titans WHERE last_updated = :updated");
        $stmt->execute([':updated' => $last_update->fetch(\PDO::FETCH_ASSOC)['last_updated']]);

        $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->_output;
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

    public function getEventScore(string $event)
    {
        $event = ($event == "caprice") ? "caprice" : "city of gold";

        $stmt = $this->_db->prepare("SELECT SUM(points) AS points FROM shop_titans_event WHERE event_name = :name ORDER BY last_updated DESC LIMIT 1");
        $stmt->execute(
            [
                ':name' => $event
            ]
            );

        $this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->_output;
    }

    public function UpdateAll(array $data)
    {
        //The submitted timestamp needs to be the same for all users for the page display to work on a future load
        $time = date('Y-m-d H:i:s');

        //Player name is the Key for the array of data as I used a multi-level POST array
        foreach($data as $player => $stats)
        {
            //We can skip the submit button
            if($player == "Submit")
            { 
                continue;
            }

            //Set the defaults for posting into the DB shortly
            $name = $player;
            $worth = 0;
            $invest = 0;
            $level = 0;
            $bounty = 0;

            //Update each stat's value
            foreach($stats as $key => $value)
            {
                switch($key)
                {
                    case 'level':
                        $level = $value;

                        break;
                    case 'worth':
                        $worth = $value;

                        break;
                    case 'investment':
                        $invest = $value;

                        break;
                    case 'total_bounties':
                        $bounty = $value;

                        break;
                }
            }
            
            //var_dump("Name: {$name}, Level: {$level}, Worth: {$worth}, Investment: {$invest}, Bounties: {$bounty}, Time: {$time}");die;

            //Its Database time!
            $stmt = $this->_db->prepare("INSERT INTO shop_titans (`name`, `level`, worth, investment, total_bounties, last_updated) VALUES (:player, :lvl, :net, :invest, :bounties, :updated)");
            $stmt->execute(
                [
                    ':player'   => $name,
                    ':lvl'      => (int)$level,
                    ':net'      => (int)$worth,
                    ':invest'   => (int)$invest,
                    ':bounties' => (int)$bounty,
                    ':updated'  => $time
                ]
            );
        }

        $this->_output = ($stmt->rowCount() > 0) ? true : false;

        return $this->_output;
    }
}