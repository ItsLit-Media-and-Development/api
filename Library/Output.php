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
 * @since		Version 0.2
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

    /**
     * setOutput() function is used to define what format the output to the end user will be.
     *
     * @param $type String What is the output type?
     * @throws \Exception String
     */
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


    /**
     * Query what the output type is set to
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->_output;
    }

    /**
     * The main output function, this is where the output is passed to for converting and
     * returning in the appropriate manner.
     *
     * @param integer $code The HTTP code to be passed back to the user.
     * @param String $response The text to send to the user
     * @param bool $bot Whether or not the output is to be designed for a bot
     * @return array|string The converted output to send to the user.
     * @throws \Exception
     */
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

    /**
     * The error function, works similar to output() except it only handles errors
     * @param integer $code
     * @param String $response The response to return to the user
     * @return array|string The converted output for the end user
     * @throws \Exception
     */
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