<?php

namespace cloudsystems\cloudinbox\exceptions;

use Exception;


class AuthenticationFailedException extends Exception
{


    /**
     * Create a new exception instance.
     *
     * @return void
     *
     */
    public function __construct()
    {
        parent::__construct('Authentication failed. Check your credentials');
    }


}
