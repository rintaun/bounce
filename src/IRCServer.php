<?php

/************************************************************
 * Bounce v0.1-alpha                                        *
 * Author: rintaun - Matthew J. Lanigan <rintaun@gmail.com> *
 *                                                          *
 * Copyright 2011 Matthew J. Lanigan.                       *
 * See LICENSE file for licensing details.                  *
 ************************************************************
 * src/IRCServer.php                                        *
 *                                                          *
 * Description: IRC Server class                            *
 ************************************************************/

if (!defined('_BOUNCE_')) die('This script may not be invoked directly.');

class IRCServer
{
	private $sid = "";

	protected function __construct($sid)
	{
		$this->sid = $sid;
	}

	protected function _destroy()
	{
	}
}
