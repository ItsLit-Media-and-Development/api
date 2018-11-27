<?php
/**
 * Community Model Class
 *
 * All database functions regarding the Community endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.0
 * @filesource
 */

namespace API\Model;

use API\Library;

class CommunityModel extends Library\BaseModel
{
    public function __construct()
    {
		parent::__construct();
    }

    public function get_SE_Token($channel)
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT SE_token FROM api_users WHERE channel = :channel");
            $stmt->execute([':channel' => $channel]);

            $this->_output = $stmt->fetch();
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

	public function ban_user($channel, $user, $requester, $reason)
	{
		try {
			$stmt = $this->_db->prepare("INSERT INTO twitch_logs (channel, user, requester, reason, type) VALUES(:chan, :user, :requester, :reason, :type)");
			$stmt->execute(
				[
					':chan'      => $channel,
					':user'      => $user,
					':requester' => $requester,
					':reason'    => preg_replace("/^(\w+\s)/", "", urldecode($reason)),
					':type'      => 'ban'
				]
			);

			if($stmt->rowCount() > 0) {
				$this->_output = true;
			} else {
				$this->_output = false;
			}
		} catch(\PDOException $e) {
			$this->_output = $e->getMessage();
		}

		return $this->_output;
	}
}