<?php

/************************************************************
 * Bounce v0.1-alpha                                        *
 * Author: rintaun - Matthew J. Lanigan <rintaun@gmail.com> *
 *                                                          *
 * Copyright 2011 Matthew J. Lanigan.                       *
 * See LICENSE file for licensing details.                  *
 ************************************************************
 * src/core.php                                             *
 * Last Modified: 4/28/2011                                 *
 *                                                          *
 * Description: Bounce core                                 *
 ************************************************************/

require_once("inc/Singleton.php");
require_once("inc/Configurator.php");
require_once("inc/Logger.php");

class Bounce extends Singleton
{

	protected function __construct()
	{
	}


	protected function __destruct()
	{
	}
}
