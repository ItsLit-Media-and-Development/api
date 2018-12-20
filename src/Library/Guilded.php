<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 19/12/2018
 * Time: 22:29
 */

namespace API\Library;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class Guilded
{
	const API_BASE = "https://api.guilded.gg";

	private $_team_id;
	private $_client;

	public function __construct()
	{
		$this->_client = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));
		$tmp = new Config();
		$this->_team_id = $tmp->getSettings('TEAM_ID');
	}

	public function get_event($event_id)
	{

		$output = $this->get('teams/' . $this->_team_id . '/events/' . $event_id);

		if(!is_array($output)) {
			$output = "The Guilded ID is incorrect or the event is marked as Member's Only, please try again!";
		}

		return $output;
	}

	public function get($url = '', $override = false, $headers = [])
	{
		$settings['headers'] = $headers;
		$output = '';

		if(isset($headers['nover'])) {
			$settings = [];
		}

		try {
			$result = $this->_client->request('GET', (!$override ? self::API_BASE : '') . '/' . $url, $settings);

			$output = json_decode($result->getBody(), true);
		} catch(ClientException $e) {
			// catches all ClientExceptions
		} catch(RequestException $e) {
			// catches all RequestExceptions
			if($e['response']['statusCode'] == 404) {
				$output = "The Event does not exist";
			}
		}

		return $output;
	}
}