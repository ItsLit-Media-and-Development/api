<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 18/03/2018
 * Time: 01:17
 */

namespace API\Exceptions;


class EndpointNotSupportedByApiVersionException extends APIException
{
    /**
     * @var string $endpoint
     */
    public function __construct()
    {
        parent::__construct('This endpoint is not supported by the set API version.');
    }
}