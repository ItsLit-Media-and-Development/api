<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 18/03/2018
 * Time: 01:18
 */

namespace API\Exceptions;


class InvalidDirectionException extends APIException
{
    public function __construct()
    {
        parent::__construct('Invalid \'direction\' provided. Direction can only be set to \'asc\' or\'desc\'.');
    }
}