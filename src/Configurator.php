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

if (!defined('_BOUNCE_')) die('This script may not be invoked directly.');

require_once("Singleton.php");

final class Configurator extends Singleton
{
	private $configfile = "";
	private $config = array( // define defaults
		'logfile' => 'bounce.log',
		'loglevel' => L_ALL,
	);

	private $fd = NULL;

	protected function __construct()
	{
		if (isset($GLOBALS['confoverride'])) $this->configfile = $GLOBALS['confoverride'];
		else $this->configfile = 'etc/bounce.conf';

		$this->parse();
	}

	public function parse()
	{
		$this->fd = fopen($this->configfile, 'r');

		rewind($this->fd);
		while (!feof($this->fd))
		{
			$char = fgetc($this->fd);
			echo $char;
			switch ($char)
			{
				
			}
		}		
		
		fclose($this->fd);
	}

	public function rehash()
	{
	}

	private function save()
	{
	}

	public function __get($name)
	{
		if (isset($this->config[$name]))
			return $this->config[$name];
	}

	protected function _destroy()
	{
		$this->save();
	}	
}
