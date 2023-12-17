<?php
/**
 * Authentication Library
 *

 * Creates and authenticates JWT tokens as an authentication method
 * Level 1 = bot only token
 * Level 2 = web token (QS Token)
 * Level 3 = header based token
 * Level 4 = admin token
 *
 * Each token can be used for the level it is issued at and below
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.0
 * @filesource
 */

namespace API\Library;


class Authentication
{
    private $_config;
    private $_db;
    private $_log;
    private $_JWT;

    public function __construct()
    {
        $this->_JWT = new JWT();
        $this->_config = new Config();
        $this->_db = $this->_config->database();
        $this->_log = new Logger();
    }

    public function __destruct()
    {
        $this->_log->saveMessage();
    }

    /**
     * Creates the JWT token for a user
     *
     * @param String $username
     * @param Integer $level
     * @return bool
     * @throws \Exception
     */
    public function create_token($username, $level)
    {
        $this->_log->set_message("Creating new token for $username from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $enc_token = $this->_JWT->encode(['user' => $username, 'level' => $level], $this->_config->getSettings('TOKEN'));

        $stmt = $this->_db->prepare("INSERT INTO api_users (name, token, auth_level) VALUES (:name, :token, :level)");
        $stmt->execute(
            [
                ':name' => $username,
                ':level' => $level,
                ':token' => $enc_token
            ]
        );

        return ($stmt->rowCount() > 0) ? true : false;
    }

    /**
     * Validate the authentication token
     *
     * @param String $token
     * @param String $user
     * @return int|mixed|string
     */
    public function validate_token($token, $user)
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT auth_level FROM api_users WHERE name = :name AND token = :token AND active = 1");
            $stmt->execute(
                [
                    ':name' => $user,
                    ':token' => $token
                ]
            );

            return ($stmt->rowCount() > 0) ? $stmt->fetch() : 0;
        } catch(\PDOException $e)
        {
            return $e->getMessage();
        }
    }

    /**
     * Activate the authentication token for use
     * 
     * @param String $user
     * @param String $token
     * 
     * @return int Value to determin result, 1 = activated, 0 = already active, -1 = user/token invalid
     */
    public function activate_token($user, $token)
    {
        //First lets validate the user/token exists      
        try {
            $stmt = $this->_db->prepare("SELECT active FROM api_users WHERE name = :name AND token = :token");
            $stmt->execute (
                [
                    ':name'  => $user,
                    ':token' => $token
                ]
            );

            if($stmt->rowCount() > 0)
            {
                if($stmt->fetch()['active'] == 1)
                {
                    return 0;
                } else {
                    try
                    {
                        $upd = $this->_db->prepare("UPDATE api_users SET active = 1 WHERE name = :name AND token = :token");
                        $upd->execute (
                            [
                                ':name'  => $user,
                                ':token' => $token
                            ]
                        );

                        return 1;
                    } 
                    catch (\PDOException $e)
                    {
                        return $e->getMessage();
                    }
                }
            } else {
                return -1;
            }
        }
        catch (\PDOException $e)
        {
            return $e->getMessage();
        }
    }

    /**
     * Deactivate the authentication token for use
     * 
     * @param String $user
     * @param String $token
     * 
     * @return int Value to determin result, 1 = deactivated, 0 = already deactive, -1 = user/token invalid
     */
    public function deactivate_token($user, $token)
    {
        //First lets validate the user/token exists      
        try {
            $stmt = $this->_db->prepare("SELECT active FROM api_users WHERE name = :name AND token = :token");
            $stmt->execute (
                [
                    ':name'  => $user,
                    ':token' => $token
                ]
            );

            if($stmt->rowCount() > 0)
            {
                if($stmt->fetch()['active'] == 0)
                {
                    return 0;
                } else {
                    try
                    {
                        $upd = $this->_db->prepare("UPDATE api_users SET active = 0 WHERE name = :name AND token = :token");
                        $upd->execute (
                            [
                                ':name'  => $user,
                                ':token' => $token
                            ]
                        );

                        return 1;
                    } 
                    catch (\PDOException $e)
                    {
                        return $e->getMessage();
                    }
                }
            } else {
                return -1;
            }
        }
        catch (\PDOException $e)
        {
            return $e->getMessage();
        }
    }
}