<?php

namespace App\Exceptions;

use Exception;

class DuplicateException extends Exception
{
    public function __construct($message = "Duplicate entry")
    {
        parent::__construct($message);
    }
}