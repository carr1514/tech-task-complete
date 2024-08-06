<?php

namespace App\Exceptions;

use Exception;

class OnlySupportsIDsException extends Exception
{
    protected $message = 'Steam only supports IDs';
    protected $code = 400;
}
