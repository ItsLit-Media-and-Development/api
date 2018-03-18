<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 18/03/2018
 * Time: 01:24
 */

namespace API\Exceptions;


class UnsupportedApiVersionException extends APIException
{
    public function __construct()
    {
        parent::__construct('Unsupported Twitch API Version');
    }
}