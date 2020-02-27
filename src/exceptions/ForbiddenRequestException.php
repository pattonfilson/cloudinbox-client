<?php

namespace cloudsystems\cloudinbox\exceptions;

use Exception;


class ForbiddenRequestException extends Exception
{


	/**
	 * Create a new exception instance.
	 *
	 * @return void
	 *
	 */
	public function __construct()
	{
		parent::__construct('Forbiddden request.');
	}


}
