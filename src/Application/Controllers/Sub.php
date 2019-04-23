<?php
/**
 * Subscriber Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 0.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Sub extends Library\BaseController
{
    private $_db;

    public function __construct()
    {
		parent::__construct();

		$this->_db = new Model\SubModel();
    }

    /**
     * @TODO Implement the actual functionality
     * @param string $user
     * @return array|string
     * @throws \Exception
     */
    public function tier($user = '')
    {
        $this->_log->set_message("Sub::tier() called from " . $_SERVER['REMOTE_ADDR'] . " for $user, 501 returned", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }

    public function listgames()
    {
        $this->_log->set_message("Sub::listgames() called from " . $_SERVER['REMOTE_ADDR'] . ", 501 returned", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }

    public function queuegame()
    {
        $this->_log->set_message("Sub::queuegame() called from " . $_SERVER['REMOTE_ADDR'] . ", 501 returned", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }
}