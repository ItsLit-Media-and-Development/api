<?php
/**
 * API Exception
 *
 *
 * @package       API
 * @author        Marc Towler <marc@marctowler.co.uk>
 * @copyright     Copyright (c) 2018 Marc Towler
 * @license       https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link          https://api.itslit.uk
 * @since         Version 2.0
 * @filesource
 */

namespace API\Library\Exceptions;

use API\Library;


class APIException extends \Exception
{
    protected $_message;
    protected $_file;
    protected $_line;
    protected $_trace;
    protected $_severity;
    protected $_log;

    /**
     * @var string $message
     * @var int $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);

        $this->_message  = $message;
        $this->_file     = $this->file;
        $this->_line     = $this->line;
        $this->_trace    = $this->getTrace();
        $this->_severity = ($code == 1) ? "Notice" : (($code == 2) ? "Warning" : "Error");
        $this->_log      = new Library\Logger();
    }
}