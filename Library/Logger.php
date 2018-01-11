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

    public function write($message, $level)
    {
        
    }
}