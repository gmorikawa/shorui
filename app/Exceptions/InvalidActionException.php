<?php

namespace App\Exceptions;

use Exception;

class InvalidActionException extends Exception
{
    public function __construct($message = "Forbidden")
    {
        parent::__construct($message);
    }
}