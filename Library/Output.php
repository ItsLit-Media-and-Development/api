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
        } else {
            throw new \Exception("Output type " . $type . " is not a valid type!");
        }
    }


    public function getOutput()
    {
        return $this->_output;
    }

    public function output($code, $response, $bot = true)
    {
        $out = [];

        if($this->_output == '')
        {
            throw new \Exception("Output type is not a valid type!");
        } else {
            if($code >= 400)
            {
                return $this->_outputError($code, $response);
            }

            switch ($this->_output)
            {
                case 'json':
                    header('Content-Type: application/json');

                    if($bot)
                    {
                        $out = json_encode($response);
                    } else {
                        $out = json_encode(['status' => $code, 'response' => $response]);
                    }

                    break;
                case 'xml':
                    header('Content-Type: text/xml');

                    $conv = '';

                    if(is_array($response))
                    {
                        foreach ($response as $item)
                        {
                            if (is_array($item))
                            {
                                foreach ($item as $key => $val)
                                {
                                    $conv .= "<$key>$val</$key>";
                                }
                            }
                        }
                        $response = $conv;
                    }

                    $out = '<rsp stat="ok">' . $response . '</rsp>';
                    break;
                case 'html':
                    header('Content-Type: text/html');

                    $conv = '<table id="rsp-stat-ok"><tr>';

                    if(is_array($response))
                    {
                        foreach($response as $item)
                        {
                            if (is_array($item))
                            {
                                foreach ($item as $key => $val)
                                {
                                    $conv .= "<td id='$key'>$val</td>";
                                }
                            }
                        }

                        $conv .= "</tr></table>";
                        $out = $conv;
                    }

                    break;
                default:
                    header('Content-Type: application/json');

                    if($bot)
                    {
                        $out = json_encode($response);
                    } else {
                        $out = json_encode(['status' => $code, 'response' => $response]);
                    }
            }
        }

        return $out;
    }

    private function _outputError($code, $response)
    {
        $out = [];

        if(is_int($code))
        {
            header('HTTP/1.1 ' . $code . ' ' . $response);

            switch($this->_output)
            {
                case 'json':
                    header('Content-Type: application/json');

                    $out = json_encode(['status' => $code, 'response' => $response]);
                    break;
                case 'xml':
                    header('Content-Type: text/xml');

                    $out = '<rsp stat="fail"><err-code=' . $code . ' response="' . $response . '" /></rsp>';
                    break;
                case 'html':
                    header('Content-Type: text/html');

                    $out = '<table id="rsp-stat-fail"><tr><td>Error Code: ' . $code . '</td><td>Response: ' . $response . '</td></tr></table>';
                    break;
                default:
                    header('Content-Type: application/json');

                    $out = json_encode(['status' => $code, 'response' => $response]);
            }
        } else {
            throw new \Exception('$code was not set as an integer... Lets get it right!');
        }

        return $out;
    }
}