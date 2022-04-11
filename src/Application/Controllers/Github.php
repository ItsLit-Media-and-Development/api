<?php
/**
 * Github Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2021 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 1.2
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;


class Github extends Library\BaseController
{
    protected $_config;
    protected $_gh;

    public function __construct()
    {
		parent::__construct();

        $this->_config = new Library\Config();
        $this->_gh = new Library\Github();
    }

    public function login()
    {
        header('Location: https://github.com/login/oauth/authorize?client_id=' . $this->_gh->getID() . '&scope=repo%20user%20read:org%20security_events%20repo:status');
    }

    public function issues()
    {

    }

    public function releases()
    {

    }

    public function project()
    {

    }
}