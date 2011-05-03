<?php

/************************************************************
 * Bounce v0.1-alpha                                        *
 * Author: rintaun - Matthew J. Lanigan <rintaun@gmail.com> *
 *                                                          *
 * Copyright 2011 Matthew J. Lanigan.                       *
 * See LICENSE file for licensing details.                  *
 ************************************************************
 * src/Client.php                                           *
 *                                                          *
 * Description: Client class                                *
 ************************************************************/

if (!defined('_BOUNCE_')) die('This script may not be invoked directly.');

class Client
{
	private $sid = "";
	private $myServer = NULL;

	protected function __construct($sid)
	{
		$this->sid = $sid;
	}

	public function parse($data)
	{
		// irc is a space-delimited protocol, so first let's BLOW IT UP!
		$data = explode(" ", $data);

		// technically, it's valid irc protocol for the client to send an origin
		// before its command beginning with a colon. in theory, a server should
		// check to see if this is valid and use it if it is or rewrite it if it
		// isn't. in practice, it's more practical to just throw it away. Plus, we
		// don't use that crap anyway, because not all servers support it
		// (i.e. they are broken).
		if (substr($data[0],0,1) == ":") array_shift($data); // in the disposal!

		// I'm in a comment-y mood tonight :)

		// the next token is the command.
		$command = strtolower(array_shift($data));

		// now we need to find out if there's a method in $this for handling $command
		// if there is, we pass off the rest of $data - the parameters - to it
		// for processing
		if (method_exists($this, "cmd_" . $command")) call_user_func(array($this, "cmd_" . $command), $data);

		// if there ISN'T a method for handling $command...
		// first we should check if they're registered. We intercept CAP entirely
		// and NICK at least initially, so if the user isn't registered, yell at them
		// like a small child that has just tried to pick up the cat by the ears.
		else if (!$this->isRegistered())
		{
			$command = strtoupper($command);
			$this->send("451 " . $command . " :Register first");
		}

		// but if we ARE registered we should probably just be sending the data straight
		// along to the server, but only if we have one!
		else if (is_a($this->myServer, "IRCServer"))
		{
			// first we need to rebuild our command
			$data = strtoupper($command) . " " . implode($data);
			// and then send it!
			$this->myServer->send($data);
		}

		// if we get a command we don't recognize, and AREN'T attached to a server,
		// then we have a problem. I see a couple options:
		//	1. ERR_UNKNOWNCOMMAND (421)
		//	2. Some server message about attaching
		// I'm not fully decided either way yet, but I am partial to #1 as it seems
		// more in line with the spirit of the IRC specification.
		else
		{
			$command = strtoupper($command);
			$this->send("421 " . $command . " :Unknown command");
		}
	}

	private function isRegistered()
	{
		if ((!empty($this->user)) && ($this->authenticated === true)) return true;
		return false;
	}

	public function send($data)
	{
		$SH = SocketHandler::getInstance();
		$SH->send($this->sid, $data . "\n");
	}

	protected function _destroy()
	{
	}
}
