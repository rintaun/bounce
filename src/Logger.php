<?php

/************************************************************
 * Bounce v0.1-alpha                                        *
 * Author: rintaun - Matthew J. Lanigan <rintaun@gmail.com> *
 *                                                          *
 * Copyright 2011 Matthew J. Lanigan.                       *
 * See LICENSE file for licensing details.                  *
 ************************************************************
 * src/inc/Logger.php                                       *
 * Last Modified: 4/28/2011                                 *
 *                                                          *
 * Description: Log manager                                 *
 ************************************************************/

require_once("Singleton.php");

define('L_DEBUG',   0x01);
define('L_WARNING', 0x02);
define('L_NOTICE',  0x04);
define('L_INFO',    0x08);
define('L_ERROR',   0x10);
define('L_FATAL',   0x20);
define('L_NODEBUG', 0xFE);
define('L_ALL',     0xFF);

final class Logger extends Singleton
{

	private $log = array();
	private $fd = NULL;
	private $logfile = "";
	private $loglevel = L_ALL;

        protected function __construct()
        {
		$config = Configurator::getInstance();

		if (isset($GLOBALS['logfile']))
			$this->logfile = $GLOBALS['logfile'];
		else
		{
 			$this->logfile = $config->logfile;
		}
		
		$this->fd = fopen($this->logfile, 'a+');
        }
	
	public function __get($name)
	{
		if ($name == "log")
		{
			$logger = Logger::getInstance();
			return $logger->log;
		}
	}

	public function log($level, $format, $args=NULL)
	{
		$time = time();

		if ((is_array($args)) && (!empty($args)))
			$message = vsprintf($format, $args);
		else
			$message = $format;

		$this->log[] = array(
			'time' => $time,
			'level' => $level,
			'message' => $message
		);

		$logentry = sprintf("[%s] %s", date("H:i", $time), $message)."\n";

		if ($this->loglevel & $level)
		{
			if ($GLOBALS['forked'] != true)
				echo $logentry;
			if (is_resource($this->fd))
				fwrite($this->fd, $logentry);
		}
		if ($level & L_FATAL)
		{
			_exit();
		}
	}

        protected function _destroy()
        {
		fwrite($this->fd,"");
		fclose($this->fd);
        }
}


function _log($level, $format)
{
	$logger = Logger::getInstance();
	$args = func_get_args();
	array_shift($args);
	array_shift($args);

	$logger->log($level, $format, $args);
}