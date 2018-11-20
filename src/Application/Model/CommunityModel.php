<?php
/**
 * Community Model Class
 *
 * All database functions regarding the Community endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.0
 * @filesource
 */

namespace API\Model;

use API\Library;

class CommunityModel
{
    private $_db;
    private $_config;
    private $_output;

    public function __construct()
    {
        $this->_config = new Library\Config();
        $this->_db = $this->_config->database();
    }

    public function get_SE_Token($channel)
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT SE_token FROM api_users WHERE channel = :channel");
            $stmt->execute([':channel' => $channel]);

            $this->_output = $stmt->fetch();
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }
}