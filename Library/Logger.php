<?php
/**
 * Logger Library
 *
 * Logs errors, warning and other features (TBC)
 *
 * @package        API
 * @author        Marc Towler <marc@marctowler.co.uk>
 * @copyright    Copyright (c) 2017 Marc Towler
 * @license        https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link        https://api.itslit.uk
 * @since        Version 0.2
 * @filesource
 */

namespace API\Library;

use API\Library\Config;

class Logger
{
    private $_start;
    private $_end;
    private $_db;
    private $_message = [];

    public function __construct()
    {
        $tmp = new Config();
        $this->_db = $tmp->database();
    }

    public function start()
    {
        $this->_start = microtime(true);
    }

    public function end()
    {
        $this->_end = microtime(true);
    }

    public function load()
    {
        return $this->_end - $this->_start;
    }

    public function set_message($message, $level)
    {
        $this->_message = ['level' => $level, 'message' => $message];

        return $this->_message;
    }

    public function saveMessage()
    {
        if(empty($this->_message))
        {
            $this->_message = ['level' => 'Error', 'message' => 'Tried to call Logger::saveMessage() without setting the message'];
        }

        $stmt = $this->_db->prepare("INSERT INTO logs (err_level, msg, date) VALUES (:level, :msg)");
        $stmt->execute(
            [
                ':level' => $this->_message['level'],
                ':msg' => $this->_message['message']
            ]
        );
    }
}