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
    private $_types = ['json', 'xml', 'html', 'plain'];

    public function __construct()
    {
    }

    /**
     * setOutput() function is used to define what format the output to the end user will be.
     *
     * @param String $type What is the output type?
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
     * @param array|String $response The text to send to the user
     * @param bool $bot Whether or not the output is to be designed for a bot
     * @return array|string The converted output to send to the user.
     */
    public function output($code, $response, $bot = true)
    {
        $out = [];

        if($this->_output == '')
        {
            return $this->_outputError(400, "Output type is not a valid type!");
        } else {
            if($code >= 400)
            {
                return $this->_outputError($code, $response);
            }

            header("Access-Control-Allow-Origin: *");

            //Bots can't handle anything more then plain text so lets change the output as such.
            if($bot == true)
            {
                $this->_output = "plain";
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
                case 'plain':
                    header('Content-Type: text/plain');

                    $out = '';

                    if(is_array($response))
                    {
                        foreach($response as $item)
                        {
                            if(is_array($item))
                            {
                                foreach($item as $key => $val)
                                {
                                    $out .= str_replace("%3A", ":", str_replace("%20", " ", $val)) . ", ";
                                }
                            }
                        }
                    }
                    else
                    {
                        $out = $response;
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
     * @param array|String $response The response to return to the user
     * @return array|string The converted output for the end user
     */
    private function _outputError($code, $response)
    {
        if(is_int($code))
        {
            header('HTTP/1.1 ' . $code . ' ' . $response);
            header("Access-Control-Allow-Origin: *");

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
                case 'plain':
                    header('Content-Type: text/plain');

                    //This is generally only going to be used for chat bots so lets make it muggle readable
                    $out = "An error occured: $response";
                    break;
                default:
                    header('Content-Type: application/json');

                    $out = json_encode(['status' => $code, 'response' => $response]);
            }
        } else {
            return $this->_outputError(500, '$code was not set as an integer... Lets get it right!');
        }

        return $out;
    }
}