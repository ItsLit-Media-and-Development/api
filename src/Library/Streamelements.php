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


use GuzzleHttp\Client;

class Streamelements
{
    public $data;
    private $_url = 'https://api.streamelements.com/kappa/v2/';
    private $_channelID = '';
    private $_JWT;
    private $_guzzle;

    public function __construct()
    {
        $this->_guzzle = new Client();
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
        if($channelID != 0)
        {
            $this->_channelID = $channelID;
        }
    }

    /**
     * Returns the SE JWT Token
     *
     * @return string
     */
    public function get_token()
    {
        return $this->_JWT;
    }

    /**
     * Sets the SE JWT Token
     *
     * @param string $token
     */
    public function set_token($token)
    {
        $this->_JWT = $token;
    }

    /**
     * Pulls the points of $limit's users inside the specified channel
     *
     * @param int $limit
     * @param int $channelID
     * @return bool|array
     */
    public function get_channel_points($limit, $channelID = 0)
    {
        $this->set_channel_id($channelID);

        $this->_url .= 'points/' . $this->_channelID . '/alltime?limit=' . $limit;

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
        $this->set_channel_id($channelID);

        $this->_url .= 'points/' . $this->_channelID . '/' . $username;

        $data = file_get_contents($this->_url);

        $this->data = json_decode($data, true);

        if(array_key_exists('statusCode', $this->data))
        {
            return false;
        }

        return $this->data;
    }

    /**
     * Modifies the amount of points for user $username
     *
     * @param string $username
     * @param int $channelID
     * @param int $points
     * @return array|\Psr\Http\Message\StreamInterface
     */
    public function modify_user_points($username, $channelID = 0, $points)
    {
        $this->set_channel_id($channelID);

        $res = $this->_guzzle->request('PUT', $this->_url . 'points/' . $this->_channelID . '/' . $username . '/' . $points,
            ['Authorization' => $this->_JWT]);

        return ($res->getStatusCode() == 200) ? $res->getBody() : ["Error" => 401, "response" => $res->getBody()];
    }

    /**
     * Shows all active giveaways for channel specified by channelID
     * @param int $channelID
     * @return bool|array
     */
    public function get_giveaways($channelID = 0)
    {
        if($channelID == 0)
        {
            return false;
        }
      
        $this->set_channel_id($channelID);

        $this->_url .= '/giveaways/' . $this->_channelID;

        $data = file_get_contents($this->_url);

        $this->data = json_decode($data, true);

        return $this->data;
    }

    /**
     * Returns if a user is entered into the specified giveaway and if so, how many entries they have
     *
     * @param int $channelID
     * @param int $giveawayID
     * @return array|\Psr\Http\Message\StreamInterface
     */
    public function check_entry_giveaway($channelID = 0, $giveawayID)
    {
        $this->set_channel_id($channelID);

        $res = $this->_guzzle->request('GET', $this->_url . $this->_channelID . '/' . $giveawayID . '/joined',
            ['Authorization' => $this->_JWT]);

        return ($res->getStatusCode() != 401) ? $res->getBody() : ["Error" => 401, "response" => $res->getBody()];
    }

    /**
     * Allows a user to enter a giveaway
     *
     * @TODO The JWT needs to be set by the end user, not using my token
     * @param int $channelID
     * @param int $giveawayID
     * @param int $tickets
     * @return bool|\Psr\Http\Message\StreamInterface
     */
    public function enter_giveaway($channelID = 0, $giveawayID, $tickets)
    {
        $this->set_channel_id($channelID);

        $res = $this->_guzzle->request('POST', $this->_url . $this->_channelID . '/' . $giveawayID,
            [
                'Authorization' => $this->_JWT,
                'Body' => $tickets
            ]);

        return ($res->getStatusCode() == 201) ? true : $res->getBody();
    }
}