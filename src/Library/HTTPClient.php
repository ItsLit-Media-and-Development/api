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

    private function setLastResponse($response, string $type)
    {
        $router = new Router();

        //Lets make sure we empty it out
        $this->_lastReponse = [];

        //Lets set the data
        $this->_lastResponse['RequestType']     = $type;
        $this->_lastResponse['SentHeaders']     = $this->getHeaders();
        $this->_lastResponse['ReceivedHeaders'] = $router->getAllHeaders();
        $this->_lastResponse['URL']             = $this->_url;
        $this->_lastResponse['Response']        = $response;
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
        $response = json_decode($result->getBody(), true);

        $this->setLastResponse($response, 'GET');

        return $response;
    }

    public function post(string $url, array $body)
    {
        $this->_url = $url;

        $request = $this->_client->post($this->_url, $this->_headers, ['body' => $body]);
        $response = $request->send();

        $this->setLastResponse($response, 'POST');

        return $response;
    }

    public function put(string $url, array $data)
    {
        $this->_url = $url;

        $request = $this->_client->post($this->_url, $this->_headers, ['body' => $body]);
        $response = $request->send();

        $this->setLastResponse($response, 'PUT');

        return $response;
    }

    public function patch()
    {
        $this->_url = $url;

        $result = $this->_client->request('PATCH', $this->_url, $this->_headers);
        $response = $request->send();

        return $response;
    }

    public function delete(string $url, array $body)
    {
        $this->_url = $url;

        $result = $this->_client->request('DELETE', $this->_url, $this->_headers);
        $response = $request->send();

        return $response;
    }
}