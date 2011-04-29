<?php

/************************************************************
 * Bounce v0.1-alpha                                        *
 * Author: rintaun - Matthew J. Lanigan <rintaun@gmail.com> *
 *                                                          *
 * Copyright 2011 Matthew J. Lanigan.                       *
 * See LICENSE file for licensing details.                  *
 ************************************************************
 * src/SocketHandler.php                                    *
 *                                                          *
 * Description: Socket handling subsystem                   *
 ************************************************************/

final class SocketHandler extends Singleton
{
	private $sockets = array();
	private $q = array();
	private $interrupt = FALSE;

	protected function __construct()
	{
	}

	public function loop()
	{
		$x = 0;
		while (!$this->interrupt)
		{
		}		
	}

	public function interrupt()
	{
		$this->interrupt = TRUE;
	}

	protected function _destroy()
	{
	}
}
class Test { }
