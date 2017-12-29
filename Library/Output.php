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
    private $_output = '';
    private $_types = ['json', 'xml', 'html'];

    public function __construct()
    {
    }

    public function setOutput($type)
    {
        $type = strtolower($type);
        if (in_array($type, $this->_types, true))
        {
            $this->_output = $type;
        }
    }


    public function getOutput()
    {
        return $this->_output;
    }

    protected function output($code, $response, $bot = true)
    {
        $out = [];

        if($this->_output == '')
        {
            throw new \Exception("You haven't used Output::setOutput() to specify output type");
        } else {
            if($code > 300)
            {
                $this->_outputError($code, $response);
            }

            switch ($this->_output)
            {
                case 'json':
                    header('Content-Type: application/json');

                    if($bot)
                    {
                        $out = $response;
                    } else {
                        $out = ['status' => $code, 'response' => $response];
                    }

                    break;
                case 'xml':
                    header('Content-Type: text/xml');
                    break;
                case 'html':

                    break;
                default:

            }
        }

        return $out;
    }

    private function _outputError($code, $response)
    {
        if(is_int($code))
        {
            header('HTTP/1.1 ' . $code . ' ' . $response);
        } else {
            throw new \Exception('$code was not set as an integer... Lets get it right!');
        }

        return $response;
    }
}