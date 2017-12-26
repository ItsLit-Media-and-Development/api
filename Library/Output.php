<?php
/**
 * Output Library
 *
 * Allows the end user to specify different types of output i.e. HTML, JSON (default), XML
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2017 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 0.1
 * @filesource
 */

namespace API\Library;


class Output
{
    private $_output = 'json';
    private $_types  = ['json', 'xml', 'html'];

    public function __construct()
    {
    }

    public function setOutput($type)
    {
        $type = strtolower($type);
        if(in_array($type, $this->_types, true))
        {
            $this->_output = $type;
        }
    }

    public function getOutput()
    {
        return $this->_output;
    }
}