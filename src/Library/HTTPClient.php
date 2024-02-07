<?php
/**
 * HTTP Client Library
 *
 * Working with Outgoing HTTP Requests
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 2.0
 * @filesource
 */


namespace API\Library;

use API\Exceptions\InvalidIdentifierException;
use GuzzleHttp\Client;

class HTTPClient
{
    private $_client;
    private $_headers = [];
    private $_config;
    private $_lastResponse;
    private $_url;

    public function __construct()
    {
        $this->_config = new Config();

		$this->_client = ($this->_config->getSettings('ENV') == "DEV") ? new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),)) : new Client();
        
    }

    public function getLastResponse()
    {
        return $this->_lastResponse;
    }

    private function setLastResponse($response)
    {
        $router = new Router();

        //Lets make sure we empty it out
        $this->_lastReponse = [];

        //Lets set the data
        $this->_lastResponse['SentHeaders']  = $this->getHeaders();
        $this->_lastResponse['ReceivedHeaders'] = $router->getAllHeaders();
        $this->_lastResponse['Response'] = $response;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    public function setHeaders(array $headerInfo)
    {
        foreach($headerInfo as $key => $value)
        {
            $this->_headers[$key] = $value;
        }
    }

    public function get(string $url)
    {
        $this->_url = $url;

        $result = $this->_client->request('GET', $this->_url, $this->_headers);
        $this->setLastResponse('test');
		return json_decode($result->getBody(), true);
    }

    public function post(string $url, array $body)
    {
        $this->_url = $url;

        $request = $this->_client->post($this->_url, $this->_headers, $body);

        return $request->send();
    }

    public function put()
    {

    }

    public function patch()
    {

    }

    public function delete()
    {

    }
}