<?php
/**
 * Rewards Model Class
 *
 * All database functions regarding the Rewards endpoint is stored here
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

class RewardModel
{
    private $_db;
    private $_config;
    private $_output;

    public function __construct()
    {
        $this->_config = new Library\Config();
        $this->_db     = $this->_config->database();
    }

    public function add_reward($chan, $reward, $desc)
    {
        try {
            $stmt = $this->_db->prepare("INSERT INTO rewards(channel, name, description) VALUES(:chan, :reward, :desc)");
            $stmt->execute(
                [
                    ':chan' => $chan,
                    ':reward' => $reward,
                    ':desc' => $desc
                ]
            );

            if ($stmt->rowCount() > 0) {
                $this->_output = true;
            } else {
                $this->_output = false;
            }
        } catch (\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function add_code($title, $code, $platform, $expires)
    {
        try
        {
            $stmt = $this->_db->prepare("INSERT INTO codes(title, code, platform, expiration) VALUES(:title, :code, :platform, :expiration)");
            $stmt->execute(
                [
                    ':title' => $title,
                    ':code' => $code,
                    ':platform' => $platform,
                    ':expiration' => $expires
                ]
            );

            if($stmt->rowCount() > 0)
            {
                $this->_output = true;
            }

        } catch(\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function redeem_reward($chan, $reward, $user)
    {
        //lets check the reward actually exists or is allowed
        try
        {
            $stmt = $this->_db->prepare("SELECT id FROM rewards WHERE name = :name AND channel = :chan");
            $stmt->execute(
                [
                    ':chan' => $chan,
                    ':name' => $reward
                ]
            );
            $row = $stmt->fetch();

            if($stmt->rowCount() > 0)
            {
                $ins = $this->_db->prepare("INSERT INTO redemption(channel, user, reward, date) VALUES(:chan, :user, :id, DATE)");
                $ins->execute(
                    [
                        ':chan' => $chan,
                        ':user' => $user,
                        ':id' => $row['id']
                    ]
                );

                if($ins->rowCount() > 0)
                {
                    $this->_output = true;
                }
            }
        } catch(\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function redeem_code($reward, $user)
    {
        try {
            $stmt = $this->_db->prepare("SELECT id, code FROM codes WHERE title = :title AND ISNULL(redeemed_by) LIMIT 1");
            $stmt->execute([':title' => $reward]);

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(empty($result))
            {
                $this->_output = "Sorry there are no codes left for $reward";
            }

            $ins = $this->_db->prepare("UPDATE codes SET redeemed_by = :user WHERE id = :id");
            $ins->execute(
                [
                    ':user' => $user,
                    ':id'   => $result['id']
                ]
            );

            if($ins->rowCount() > 0)
            {
                $this->_output = $result['code'];
            }
            else
            {
                $this->_output = false;
            }
        } catch(\PDOException $e) {
            $this->_output = false;
        }

        return $this->_output;
    }

    public function list_reward($chan)
    {
        $stmt = $this->_db->prepare("SELECT name FROM rewards WHERE channel = :chan");
        $stmt->execute([':chan' => $chan]);
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($res) > 0)
        {
            $this->_output = $res;
        } else {
            $this->_output = false;
        }

        return $this->_output;
    }

    public function list_code()
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT title, platform FROM codes WHERE redeemed_by = NULL AND expiration > CURRENT_TIMESTAMP ORDER BY expiration ASC");
            $stmt->execute();

            $tmp = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            //lets actually check we have results!
            if($stmt->rowCount() > 0)
            {
                $this->_output = $tmp;
            } else {
                $this->_output = false;
            }
        } catch(\PDOException $e) {
            $this->_output = false;
        }

        return $this->_output;
    }

    public function delete_reward($chan, $reward)
    {
        try
        {
            $stmt = $this->_db->prepare("DELETE FROM rewards WHERE channel = :chan AND name = :reward");
            $this->_output = $stmt->execute(
                [
                    ':chan' => $chan,
                    ':reward' => $reward
                ]
            );
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function delete_code($title)
    {
        try
        {
            $stmt = $this->_db->prepare("DELETE FROM codes WHERE title = :title");
            $this->_output = $stmt->execute([":title" => $title]);
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }
}