<?php
/**
 * Twitter Library
 *
 * Working with Twitter
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

use API\Exceptions\TwitterException;
use GuzzleHttp\Client;

class Twitter
{
	public $url;
	public $requestMethod;
	protected $_oauth;
	private $_guzzle;
	private $_oauth_token;
	private $_oauth_secret;
	private $_consumer_key;
	private $_consumer_secret;
	private $_post_fields;
	private $_get_field;

	public function __construct()
	{
		$config = new Config();
		$this->_guzzle = new Client();

		$this->_consumer_key    = $config->getSettings('CONSUMER_KEY');
		$this->_consumer_secret = $config->getSettings('CONSUMER_SECRET');
		$this->_oauth_token     = $config->getSettings('OAUTH_TOKEN');
		$this->_oauth_secret    = $config->getSettings('OAUTH_SECRET');
	}

	public function request($url, $method = 'get', $data = NULL, $curlOptions = array())
	{
		if(strtolower($method) === 'get') {
			$this->setGetfield($data);
		} else {
			$this->setPostfields($data);
		}

		return $this->buildOauth($url, $method)->performRequest(true);
	}

	public function performRequest(bool $return = true)
	{
		$curlOptions = [];
		$header = [$this->buildAuthorizationHeader($this->_oauth), 'Expect:'];
		$getfield = $this->getGetfield();
		$postfields = $this->getPostfields();

		if(in_array(strtolower($this->requestMethod), array('put', 'delete'))) {
			$curlOptions[CURLOPT_CUSTOMREQUEST] = $this->requestMethod;
		}
		$options = $curlOptions + array(
				CURLOPT_HTTPHEADER     => $header,
				CURLOPT_HEADER         => false,
				CURLOPT_URL            => $this->url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT        => 10,
			);
		if(!is_null($postfields)) {
			$options[CURLOPT_POSTFIELDS] = http_build_query($postfields, '', '&');
		} else {
			if($getfield !== '') {
				$options[CURLOPT_URL] .= $getfield;
			}
		}
		$feed = curl_init();
		curl_setopt_array($feed, $options);
		$json = curl_exec($feed);

		if(($error = curl_error($feed)) !== '') {
			curl_close($feed);
			throw new \Exception($error);
		}
		curl_close($feed);
		return $json;
	}

	private function buildAuthorizationHeader(array $oauth)
	{
		$return = 'Authorization: OAuth ';
		$values = array();
		foreach($oauth as $key => $value) {
			if(in_array($key, array('oauth_consumer_key', 'oauth_nonce', 'oauth_signature',
									'oauth_signature_method', 'oauth_timestamp', 'oauth_token', 'oauth_version'))) {
				$values[] = "$key=\"" . rawurlencode($value) . "\"";
			}
		}
		$return .= implode(', ', $values);
		return $return;
	}

	public function getGetfield()
	{
		return $this->_get_field;
	}

	public function setGetfield($string)
	{
		if(!is_null($this->getPostfields())) {
			throw new TwitterException('You can only choose get OR post fields');
		}

		$getfields = preg_replace('/^\?/', '', explode('&', $string));
		$params = array();

		foreach($getfields as $field) {
			if($field !== '') {
				list($key, $value) = explode('=', $field);
				$params[$key] = $value;
			}
		}

		$this->_get_field = '?' . http_build_query($params, '', '&');

		return $this;
	}

	public function getPostfields()
	{
		return $this->_post_fields;
	}

	public function setPostfields(array $array)
	{
		if(!is_null($this->getGetfield())) {
			throw new TwitterException('You can only choose get OR post fields');
		}

		if(isset($array['status']) && substr($array['status'], 0, 1) === '@') {
			$array['status'] = sprintf("\0%s", $array['status']);
		}

		foreach($array as $Key => $value) {
			if(is_bool($value)) {
				$value = ($value === true) ? 'true' : 'false';
			}
		}

		$this->_post_fields = $array;

		if(isset($this->_oauth['oauth_signature'])) {
			$this->buildOauth($this->url, $this->requestMethod);
		}

		return $this;
	}

	public function buildOauth($url, $requestMethod)
	{
		if(!in_array(strtolower($requestMethod), array('post', 'get', 'put', 'delete'))) {
			throw new TwitterException('Request method must be either POST, GET, PUT or DELETE');
		}

		$oauth = [
			'oauth_consumer_key'     => $this->_consumer_key,
			'oauth_nonce'            => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_token'            => $this->_oauth_token,
			'oauth_timestamp'        => time(),
			'oauth_version'          => '1.0'
		];

		$getfield = $this->getGetfield();

		if(!is_null($getfield)) {
			$getfields = str_replace('?', '', explode('&', $getfield));

			foreach($getfields as $g) {
				$split = explode('=', $g);

				if(isset($split[1])) {
					$oauth[$split[0]] = urldecode($split[1]);
				}
			}
		}

		$postfields = $this->getPostfields();

		if(!is_null($postfields)) {
			foreach($postfields as $key => $value) {
				$oauth[$key] = $value;
			}
		}

		$base_info = $this->buildBaseString($url, $requestMethod, $oauth);
		$composite_key = rawurlencode($this->_consumer_secret) . '&' . rawurlencode($this->_oauth_secret);
		$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
		$oauth['oauth_signature'] = $oauth_signature;

		$this->url = $url;
		$this->requestMethod = $requestMethod;
		$this->_oauth = $oauth;

		return $this;
	}

	private function buildBaseString($baseURI, $method, $params)
	{
		$return = array();
		ksort($params);
		foreach($params as $key => $value) {
			$return[] = rawurlencode($key) . '=' . rawurlencode($value);
		}
		return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
	}
}