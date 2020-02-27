<?php

namespace cloudsystems\cloudinbox\exceptions;

use Exception;


class UnwantedRedirectException extends Exception
{


    /**
     * Create a new exception instance.
     *
     * @return void
     *
     */
    public function __construct()
    {
        parent::__construct('Unwanted redirect exception');
    }


}
