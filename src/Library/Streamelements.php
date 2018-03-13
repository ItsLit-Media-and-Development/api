<?php
/**
 * Stream Elements Library
 *
 * Working with streamelements
 *
 * @package        API
 * @author        Marc Towler <marc@marctowler.co.uk>
 * @copyright    Copyright (c) 2018 Marc Towler
 * @license        https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link        https://api.itslit.uk
 * @since        Version 1.0
 * @filesource
 */

namespace API\Library;


class Streamelements
{
    public $data;
    private $_url = 'https://api.streamelements.com/kappa/v2';
    private $_channelID = '';

    public function __construct()
    {
    }

    /**
     * Pulls through the channel's ID
     *
     * @return string
     */
    public function get_channel_id()
    {
        return $this->_channelID;
    }

    /**
     * Sets the channel's ID
     *
     * @param string $channelID The channel's ID
     */
    public function set_channel_id($channelID)
    {
        $this->_channelID = $channelID;
    }

    /**
     * Pulls the points of $limit's users inside the specified channel
     *
     * @param int $limit
     * @param int $channelID
     * @return bool|array
     */
    public function get_channel_points($limit = 1000, $channelID = 0)
    {
        if($channelID != 0)
        {
            $this->set_channel_id($channelID);
        }
        elseif($this->_channelID == '')
        {
            return false;
        }

        $this->_url .= '/points/' . $this->_channelID . '/alltime?limit=' . $limit;

        $data = file_get_contents($this->_url);

        $this->data = json_decode($data, true);

        return $this->data['users'];
    }

    /**
     * Pulls the points of the specified user from the channel
     *
     * @param string $username
     * @param int $channelID
     * @return bool|mixed
     */
    public function get_user_points($username, $channelID = 0)
    {
        if($channelID != 0)
        {
            $this->set_channel_id($channelID);
        }
        elseif($this->_channelID == '')
        {
            return false;
        }

        $this->_url .= '/points/' . $this->_channelID . '/' . $username;

        $data = file_get_contents($this->_url);

        $this->data = json_decode($data, true);

        if(array_key_exists('statusCode', $this->data))
        {
            return false;
        }

        return $this->data;
    }
}