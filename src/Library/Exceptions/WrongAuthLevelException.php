<?php
/**
 * Wrong Authentication Level Exception
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


class WrongAuthLevelException extends APIException
{
    /**
     * @var string $message
     * @var int $code
     */
    public function __construct($message)
    {
        parent::__construct($message, 2);
    }
}