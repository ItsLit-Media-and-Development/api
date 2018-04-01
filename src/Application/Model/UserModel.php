<?php
/**
 * User Model Class
 *
 * All database functions regarding the User endpoint is stored here
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

class UserModel
{
    private $_db;
    private $_config;
    private $_hash;
    private $_output;

    public function __construct()
    {
        $this->_config = new Library\Config();
        $this->_db     = $this->_config->database();
        $this->_hash   = new Library\Password();
    }

    public function register($name, $password, $email, $status)
    {
        try {
            if(isset($password) && $password != '')
            {
                $password = $this->_hash->password_hash($password, PASSWORD_BCRYPT);

                $stmt = $this->_db->prepare("INSERT INTO users(name, email, password, status) VALUES (:name, :pass, :email, :status)");
                $stmt->execute([
                    ':name' => $name,
                    ':pass' => $password,
                    ':email' => $email,
                    ':status' => $status
                ]);

                $this->_output = ($stmt->rowCount() > 0) ? true : false;
            }
        } catch(\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function activate($user, $key)
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT act_key FROM activation WHERE NAME = :NAME");
            $stmt->execute([':name' => $user]);
            $row = $stmt->fetch();

            //is there a result? If so, check the retrieved key against what we have
            if(is_array($row))
            {
                if($row['key'] == $key)
                {
                    //remove the key
                    $stmt2 = $this->_db->prepare("DELETE FROM activation WHERE name = :name");
                    $stmt2->execute([':name' => $user]);
                }

                $this->_output = ($stmt->rowCount() > 0) ? true : false;
            }
        } catch(\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function profile_all($user)
    {
        try
        {
            //$stmt = $this->_db->prepare("SELECT u.name, u.email, u.twitter, u.facebook, u.youtube, u.timezone, u.rank, s.date, s.followers, s.views FROM users u INNER JOIN monthly_stats s ON u.id = s.uid WHERE name = :name");
            $stmt = $this->_db->prepare("SELECT id, name, email, twitter, facebook, youtube, timezone, rank FROM users WHERE name = :name");
            $stmt->execute([':name' => $user]);

            if($stmt->rowCount() > 0)
            {
                $tmp = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $sec = $this->_db->prepare("SELECT date, followers, views FROM monthly_stats WHERE uid = :id");
                $sec->execute(([':id' => $tmp[0]['id']]));

                if($sec->rowCount() > 0)
                {
                    $this->_output = array_merge($tmp, $sec->fetchAll(\PDO::FETCH_ASSOC));
                }
            } else
            {
                $this->_output = false;
            }
        } catch(\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function profile_follow($user)
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT u.name, u.email, s.date, s.followers FROM users u INNER JOIN monthly_stats s ON u.id = s.uid WHERE name = :name");
            $stmt->execute([':name' => $user]);

            if($stmt->rowCount() > 0)
            {
                $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else
            {
                $this->_output = false;
            }
        } catch(\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function profile_views($user)
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT u.name, u.email, s.date, s.views FROM users u INNER JOIN monthly_stats s ON u.id = s.uid WHERE name = :name");
            $stmt->execute([':name' => $user]);

            if($stmt->rowCount() > 0)
            {
                $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else
            {
                $this->_output = false;
            }
        } catch(\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function add_stats($name, $followers, $views)
    {
        try {
            $usr = $this->_db->prepare("SELECT ID FROM users WHERE name = :name");
            $usr->execute([':name' => $name]);

            $tmp = $usr->fetch();

            $stmt = $this->_db->prepare("INSERT INTO monthly_stats (uid, date, followers, views) VALUES (:id, :date, :follow, :views)");
            $this->_output = $stmt->execute(
                [
                    ':id' => $tmp["ID"],
                    ':date' => date('Y-m-d'),
                    ':follow' => $followers,
                    ':views' => $views
                ]
            );
        } catch(\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }
}