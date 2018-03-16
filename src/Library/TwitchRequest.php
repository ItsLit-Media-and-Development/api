<?php
/**
 * TwitchRequest Library
 *
 * Working with Twitch
 *
 * @package       API
 * @author        Marc Towler <marc@marctowler.co.uk>
 * @copyright     Copyright (c) 2018 Marc Towler
 * @license       https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link          https://api.itslit.uk
 * @since         Version 1.0
 * @filesource
 */

namespace API\Library;

use GuzzleHttp;

class TwitchRequest
{
    const GET_METHOD = 'GET';
    const PUT_METHOD = 'PUT';
    const POST_METHOD = 'POST';
    const DELETE_METHOD = 'DELETE';
    /**
     * @var string
     */
    protected $baseUri = 'https://api.twitch.tv/kraken/';
    /**
     * @var float
     */
    protected $timeout = 5.0;
    /**
     * @var string
     */
    protected $userAgent;
    /**
     * @var bool
     */
    protected $httpErrors = false;
    /**
     * @var bool
     */
    protected $returnJson = false;

    /**
     * Send a GET request
     *
     * @param string $endpoint
     * @param array $params
     * @param bool $accessToken
     * @return array
     */
    protected function get($endpoint, $params = [], $accessToken = NULL)
    {
        return $this->sendRequest(self::GET_METHOD, $endpoint, $params, $accessToken);
    }

    /**
     * Send the request
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @param bool $accessToken
     * @return mixed
     */
    protected function sendRequest($method, $endpoint, $params = [], $accessToken = NULL)
    {
        $client = $this->getNewHttpClient($method, $params, $accessToken);
        $response = $client->request($method, $endpoint);
        $responseBody = $response->getBody()->getContents();
        return $this->getReturnJson() ? $responseBody : json_decode($responseBody, true);
    }

    /**
     * Get a new HTTP Client
     *
     * @param string $method
     * @param array $params
     * @param string $accessToken
     * @return GuzzleHttp\Client
     */
    protected function getNewHttpClient($method, $params, $accessToken = NULL)
    {
        $config = [
            'http_errors' => $this->getHttpErrors(),
            'base_uri' => $this->baseUri,
            'timeout' => $this->getTimeout(),
            'headers' => [
                //'Client-ID' => $this->getClientId(),
                //'Accept' => sprintf('application/vnd.twitchtv.v%d+json', $this->getApiVersion()),
                'User-Agent' => ($this->getUserAgent() !== NULL) ? $this->getUserAgent() : GuzzleHttp\default_user_agent(),
            ],
        ];
        if($accessToken)
        {
            $config['headers']['Authorization'] = sprintf('OAuth %s', $accessToken);
        }
        if(!empty($params))
        {
            $config[($method == self::GET_METHOD) ? 'query' : 'json'] = $params;
        }
        return new GuzzleHttp\Client($config);
    }

    /**
     * Get HTTP errors
     *
     * @return bool
     */
    public function getHttpErrors()
    {
        return $this->httpErrors;
    }

    /**
     * Set HTTP errors
     *
     * @param bool $httpErrors
     */
    public function setHttpErrors($httpErrors)
    {
        $this->httpErrors = boolval($httpErrors);
    }

    /**
     * Get timeout
     *
     * @return float
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set timeout
     *
     * @param float $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Get user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set user agent
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = (string)$userAgent;
    }

    /**
     * Get return as JSON
     *
     * @return bool
     */
    public function getReturnJson()
    {
        return $this->returnJson;
    }

    /**
     * Set return as JSON
     *
     * @param bool $returnJson
     */
    public function setReturnJson($returnJson)
    {
        $this->returnJson = boolval($returnJson);
    }

    /**
     * Send a POST request
     *
     * @param string $endpoint
     * @param array $params
     * @param bool $accessToken
     * @return array
     */
    protected function post($endpoint, $params = [], $accessToken = NULL)
    {
        return $this->sendRequest(self::POST_METHOD, $endpoint, $params, $accessToken);
    }

    /**
     * Send a PUT request
     *
     * @param string $endpoint
     * @param array $params
     * @param bool $accessToken
     * @return array
     */
    protected function put($endpoint, $params = [], $accessToken = NULL)
    {
        return $this->sendRequest(self::PUT_METHOD, $endpoint, $params, $accessToken);
    }

    /**
     * Send a DELETE request
     *
     * @param string $endpoint
     * @param array $params
     * @param bool $accessToken
     * @return null|array
     */
    protected function delete($endpoint, $params = [], $accessToken = NULL)
    {
        return $this->sendRequest(self::DELETE_METHOD, $endpoint, $params, $accessToken);
    }
}