<?php
/**
 * Stream Labs Library
 *
 * Working with streamelements
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.0
 * @filesource
 */

namespace API\Library;


use API\Model\OauthModel;

class Streamlabs
{
	private $_model;

	public function __construct()
	{
		$this->_model = new OauthModel();
	}

	public function authorise($user)
	{
		if($this->_model->is_token_valid($user) === true) {
			return true;
		} else {
			header('Location: https://www.streamlabs.com/api/v1.0/authorize?client_id=vKoCYYMK2vcHO5yA4YUHupwSSnlJwP6VqnClL5HA&redirect_uri=https://api.itslit.uk/oauth/streamlabs/&response_type=code&scope=donations.read+donations.create+alerts.create+points.read+points.write+alerts.write');
		}
	}
}