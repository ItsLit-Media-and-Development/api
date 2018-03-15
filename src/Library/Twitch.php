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


class Twitch
{
    public $base_uri = 'https://api.twitch.tv/kraken/';
    private $_defaultAvatar = 'https://static-cdn.jtvnw.net/jtv-static/404_preview-300x300.png';
    private $_clientID = NULL;
    private $_secret = NULL;
    private $_scope = ['user_read', 'channel_read', 'chat_login', 'user_follows_edit', 'channel_editor', 'channel_commercial', 'channel_check_subscription'];
    private $_redirect_URI = '';


    public function __construct()
    {
        $tmp = new Config();

        $this->_clientID = $tmp->getSettings('CLIENT_ID');
        $this->_secret = $tmp->getSettings('TWITCH_SECRET');
    }

    public function getScope()
    {
        return $this->_scope;
    }

    public function setScope(array $scope)
    {
        $this->_scope = $scope;
    }

    public function getRedirectURI()
    {
        return $this->_redirect_URI;
    }

    public function setRedirectURI($redirecturi)
    {
        $this->_redirect_URI = $redirecturi;
    }

    protected function isValidStreamType($type)
    {
        return in_array(strtolower($type), ['live', 'playlist', 'all']);
    }

    protected function isValidBroadcastType($type)
    {
        $validTypes = ['archive', 'highlight', 'upload'];

        $broadcastArray = explode(',', $type);

        foreach($broadcastArray as $types)
        {
            if(!in_array($types, $validTypes))
            {
                return false;
            }
        }

        return true;
    }
}