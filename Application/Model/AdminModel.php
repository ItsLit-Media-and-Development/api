<?php
/**
 * Administration Model Class
 *
 * All database functions regarding the Admin endpoint is stored here
 *
 * @package        API
 * @author        Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright    Copyright (c) 2017 Marc Towler
 * @license        https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link        https://api.itslit.uk
 * @since        Version 0.7
 * @filesource
 */

namespace API\Model;

use API\Library;

class AdminModel
{
    private $_db;
    private $_config;
    private $_output;

    public function __construct()
    {
        $this->_config = new Library\Config();
        $this->_db = $this->_config->database();
    }

    public function generate_token($user, $token, $level)
    {
        try
        {
            $stmt = $this->_db->prepare("INSERT INTO auth_token (name, token, level) VALUES (:name, :token, :level)");
            $stmt->execute(
                [
                    ':name' => $user,
                    ':level' => $level,
                    ':token' => $token
                ]
            );

            if($stmt->rowCount() > 0)
            {
                $this->_output = true;
            } else
            {
                $this->_output = false;
            }
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }
}