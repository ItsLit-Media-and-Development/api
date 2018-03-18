<?php
/**
 * Twitch Library
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

use API\Exceptions\ClientIdRequiredException;
use API\Exceptions\InvalidTypeException;
use API\Exceptions\UnsupportedApiVersionException;


class Twitch extends TwitchRequest
{
    /**
     * @var int
     */
    protected $defaultApiVersion = 5;
    /**
     * @var array
     */
    protected $supportedApiVersions = [3, 5];
    /**
     * @var string
     */
    protected $clientId;
    /**
     * @var string
     */
    protected $clientSecret;
    /**
     * @var int
     */
    protected $apiVersion;
    /**
     * @var string
     */
    protected $redirectUri;
    /**
     * @var array
     */
    protected $scope;
    /**
     * @var string
     */
    protected $state;
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * Instantiate a new TwitchApi instance
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        if(!isset($options['client_id']))
        {
            throw new ClientIdRequiredException();
        }
        $this->setClientId($options['client_id']);
        $this->setClientSecret(isset($options['client_secret']) ? $options['client_secret'] : NULL);
        $this->setRedirectUri(isset($options['redirect_uri']) ? $options['redirect_uri'] : NULL);
        $this->setApiVersion(isset($options['api_version']) ? $options['api_version'] : $this->getDefaultApiVersion());
        $this->setScope(isset($options['scope']) ? $options['scope'] : []);
    }

    /**
     * Get defaultApiVersion
     *
     * @return int
     */
    public function getDefaultApiVersion()
    {
        return $this->defaultApiVersion;
    }

    /**
     * Get supportedApiVersions
     *
     * @return array
     */
    public function getSupportedApiVersions()
    {
        return $this->supportedApiVersions;
    }

    /**
     * @param String $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * Get client ID
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set client secret
     *
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get client secret
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Set API version
     *
     * @param string|int $apiVersion
     * @throws Exception
     */
    public function setApiVersion($apiVersion)
    {
        if(!in_array($apiVersion = intval($apiVersion), $this->getSupportedApiVersions()))
        {
            throw new UnsupportedApiVersionException();
        }
        $this->apiVersion = $apiVersion;
    }

    /**
     * Get API version
     *
     * @return int
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Set redirect URI
     *
     * @param string $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * Get redirect URI
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * Set scope
     *
     * @param array $scope
     * @throws Exception
     */
    public function setScope($scope)
    {
        if(!is_array($scope))
        {
            throw new InvalidTypeException('Scope', 'array', gettype($scope));
        }
        $this->scope = $scope;
    }

    /**
     * Get scope
     *
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Returns true if the set API version is greater than v3
     *
     * @return bool
     */
    protected function apiVersionIsGreaterThanV3()
    {
        return $this->getApiVersion() > 3;
    }

    /**
     * Return true if the provided limit is valid
     *
     * @param string|int $limit
     * @return bool
     */
    protected function isValidLimit($limit)
    {
        return is_numeric($limit) && $limit > 0;
    }

    /**
     * Return true if the provided offset is valid
     *
     * @param string|int $offset
     * @return bool
     */
    protected function isValidOffset($offset)
    {
        return is_numeric($offset) && $offset > -1;
    }

    /**
     * Return true if the provided direction is valid
     *
     * @param string $direction
     * @return bool
     */
    protected function isValidDirection($direction)
    {
        return in_array(strtolower($direction), ['asc', 'desc']);
    }

    /**
     * Return true if the provided broadcast type is valid
     *
     * @param string $broadcastType
     * @return bool
     */
    protected function isValidBroadcastType($broadcastType)
    {
        $validBroadcastTypes = ['archive', 'highlight', 'upload'];
        $broadcastTypeArray = explode(',', $broadcastType);
        foreach($broadcastTypeArray as $type)
        {
            if(!in_array($type, $validBroadcastTypes))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Return true if the provided stream type is valid
     *
     * @param string $streamType
     * @return bool
     */
    protected function isValidStreamType($streamType)
    {
        return in_array(strtolower($streamType), ['live', 'playlist', 'all']);
    }
}