<?php

/************************************************************
 * Bounce v0.1-alpha                                        *
 * Author: rintaun - Matthew J. Lanigan <rintaun@gmail.com> *
 *                                                          *
 * Copyright 2011 Matthew J. Lanigan.                       *
 * See LICENSE file for licensing details.                  *
 ************************************************************
 * src/Server.php                                           *
 *                                                          *
 * Description: Server that handles Bounce clients          *
 ************************************************************/

if (!defined('_BOUNCE_')) die('This script may not be invoked directly.');

require_once("Client.php");
require_once("ClientParser.php");
require_once("IRCParser.php");

final class Server extends Singleton
{
	private $clients = array();

	protected function __construct()
	{
		$SH = SocketHandler::getInstance();
		$config = Configurator::getInstance();

		$bind = explode(":", $config->bind);		

		$listener = $SH->createListener($bind[0], $bind[1], array($this, 'addClient'));
	}

	public function addClient($sid)
	{
		$this->clients[$sid] = new Client($sid);
		return array($this, 'readData');
	}

	public function readData($sid, $data)
	{
		$d = explode("\n", $data);
		$line = array_shift($d);
		$data = implode("\n", $d);

		echo $line."\n";
		return $data;
	}

	protected function _destroy()
	{
	}
}
