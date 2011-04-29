<?php

/************************************************************
 * Bounce v0.1-alpha                                        *
 * Author: rintaun - Matthew J. Lanigan <rintaun@gmail.com> *
 *                                                          *
 * Copyright 2011 Matthew J. Lanigan.                       *
 * See LICENSE file for licensing details.                  *
 ************************************************************
 * src/Configurator.php                                     *
 *                                                          *
 * Description: Configuration parser                        *
 ************************************************************/

require_once("Singleton.php");

final class Configurator extends Singleton
{
	private $configfile = NULL;
	private $config = array( // define defaults
		'logfile' => 'bounce.log',
		'loglevel' => L_ALL,
	);

	protected function __construct()
	{
		if (isset($GLOBALS['confoverride'])) $this->configfile = $GLOBALS['confoverride'];
		else $this->configfile = 'etc/bounce.conf';

		$this->parse();
	}

	public function parse()
	{
		
	}

	public function rehash()
	{
	}

	public function __get($name)
	{
		if (isset($this->config[$name]))
			return $this->config[$name];
	}

	protected function _destroy()
	{
	}	
}
