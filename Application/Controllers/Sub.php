<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 29/10/2017
 * Time: 15:57
 */

namespace API\Controllers;

use API\Library;

class Sub
{
    private $_db;
    private $_config;
    private $_params;
    private $_output;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_config = new Library\Config();
        $this->_db     = $this->_config->database();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    /**
     * @return array|string
     * @throws \Exception
     */
    public function index()
    {
        return $this->_output->output(501, "Function not implemented", false);
    }

    /**
     * @param string $user
     * @return array|string
     * @throws \Exception
     */
    public function tier($user = '')
    {
        return $this->_output->output(501, "Function not implemented", false);
    }
}