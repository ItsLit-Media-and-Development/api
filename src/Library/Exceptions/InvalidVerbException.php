<?php
/**
 * Invalid Verb Exception
 *
 *
 * @package       API
 * @author        Marc Towler <marc@marctowler.co.uk>
 * @copyright     Copyright (c) 2023 Marc Towler
 * @license       https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link          https://api.itslit.uk
 * @since         Version 2.0
 * @filesource
 */

namespace API\Exceptions;


class InvalidVerbException extends APIException
{
    public function __construct()
    {
        parent::__construct('Invalid Request verb');
    }
}