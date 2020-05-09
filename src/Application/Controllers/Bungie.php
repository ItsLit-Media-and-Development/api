<?php
/**
 * Bungie Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class Bungie extends Library\BaseController
{
	private $_d2;
	private $_guzzle;

	public function __construct()
	{
		parent::__construct();

		$this->_guzzle = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));
	}

	public function test()
	{
		$request = $this->_guzzle->get("https://www.bungie.net/Platform/GroupV2/1955871/Members/?memberType=1", [
			'headers' => [
				'X-API-Key' => $this->_config->getSettings('BUNGIE_API_KEY')
			]
		]);

		$output = json_decode($request->getBody(), true);

		var_dump($output['Response']['results'][0]['bungieNetUserInfo']);
	}
}