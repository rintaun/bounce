<?php

/************************************************************
 * Bounce v0.1-alpha                                        *
 * Author: rintaun - Matthew J. Lanigan <rintaun@gmail.com> *
 *                                                          *
 * Copyright 2011 Matthew J. Lanigan.                       *
 * See LICENSE file for licensing details.                  *
 ************************************************************
 * src/core.php                                             *
 *                                                          *
 * Description: Bounce core                                 *
 ************************************************************/

ini_set('memory_limit', '512M');

require_once("Singleton.php");
require_once("Configurator.php");
require_once("Logger.php");

final class Bounce extends Singleton
{

	protected function __construct()
	{
	}

	public function start()
	{
	}


	protected function _destroy()
	{
	}
}
