<?php
/**
 * Github Library
 *
 * Working with GitHub
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.2
 * @filesource
 */


namespace API\Library;

use API\Exceptions\InvalidIdentifierException;
use GuzzleHttp\Client;

class Github
{
	const API_BASE = 'https://api.github.com/';
	private $_client;
	private $_clientid;
	private $_clientsecret;
    private $_headers = [];

	public function __construct()
    {
		$this->_client = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));
		$tmp = new Config();
		$this->_clientid = $tmp->getSettings('GITHUB_CLIENT_ID');
    }
    
    public function getID()
    {
        return $this->_clientid;
    }
}