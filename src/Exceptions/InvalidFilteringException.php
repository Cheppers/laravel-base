<?php

namespace Cheppers\LaravelBase\Exceptions;

use \Exception;

class InvalidFilteringException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, null, null);
    }
}
