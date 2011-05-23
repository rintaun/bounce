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

require_once("IRC.php");

class Client
{
	private $sid = "";
	private $myServer = NULL;

	private $inCAP = false;


	private $nick = "";
	private $user = "";
	private $realname = "";

	private $serverName = "asgard.projectxero.net";

	private $authenticated = FALSE;

	private $features = array(
		'multi-prefix' 		=> FALSE,
		'sasl' 			=> FALSE,
		'extended-join' 	=> FALSE,
		'account-notify' 	=> FALSE,
		'away-notify' 		=> FALSE,
		'uhnames' 		=> FALSE,
	);

	public function __construct($sid)
	{
		$this->sid = $sid;
	}

	public function parse($data)
	{
		$data = IRC::Parse($data);
		// we can ignore the origin.
		// $origin = $data['origin'];
		$command = $data['command'];
		$params = $data['params'];

		// now we need to find out if there's a method in $this for handling $command
		// if there is, we pass off the rest of $data - the parameters - to it
		// for processing
		if (method_exists($this, "cmd_" . $command))
			call_user_func(array($this, "cmd_" . $command), $params);

		// if there ISN'T a method for handling $command...
		// first we should check if they're registered. We intercept CAP entirely
		// and NICK at least initially, so if the user isn't registered, yell at them
		// like a small child that has just tried to pick up the cat by the ears.
		else if (!$this->isRegistered())
			$this->sendNumeric(451, ":Register first");

		// but if we ARE registered we should probably just be sending the data straight
		// along to the server, but only if we have one!
		else if (is_a($this->myServer, "IRCServer"))
		{
			// pull the last param off if it's freeform
			if ($data['freeform'] === TRUE) $freeform = array_pop($params);
			
			// then we need to rebuild our command
			$data = strtoupper($command) . " " . implode($params);

			// and if we have freeform, stick in on there.
			if (isset($freeform)) $data .= " :" . $freeform;

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
			$this->sendNumeric(421, "%s :Unknown command", strtoupper($command));
	}

	private function isRegistered()
	{
		if ($this->inCAP === true) return false;
		if ((!empty($this->user)) && ($this->authenticated === true)) return true;
		return false;
	}
	
	public function sendNumeric($numeric, $format)
	{
		$args = func_get_args();
		array_shift($args);
		array_shift($args);
		if (!empty($args))
			$message = vsprintf($format, $args);
		else
			$message = $format;

		$this->send(":%s %d %s %s", $this->serverName, $numeric, (!empty($this->nick)) ? $this->nick : "*", $message);
	}
	public function send($format)
	{
		$args = func_get_args();
		array_shift($args);
		if (!empty($args))
			$message = vsprintf($format, $args);
		else
			$message = $format;

		$SH = SocketHandler::getInstance();
		$SH->send($this->sid, "%s\n", $message);
		echo $message."\n";
	}

	protected function _destroy()
	{
		$SH = SocketHandler::getInstance();
		$SH->close($this->sid);
		unset($this);
	}

	// and now begins the endless list of command processing functions!
	private function cmd_cap($data)
	{
		// first param in cap data is subcommand
		$subcmd = strtolower(array_shift($data));
		switch ($subcmd)
		{
			case 'ls':
				// according to the spec, we need to suspend registration
				// if the client issues this command during registration.
				// so YEAH.
				if (!$this->isRegistered()) $this->inCAP = true;

				// anyway, respond with the capabilities we PLAN to support.
				$this->sendCAP("LS", ":%s", implode(" ", array_keys($this->features)));
				break;
			case 'list':
			case 'req':
				if (!$this->isRegistered()) $this->inCAP = true;

				if (substr($data[0],0,1) == ":") $data[0] = substr($data[0],1);

				// the remaining parameters should be requested features. turn them on.
				// we either have to accept the entire set or none of it... so if there's a feature
				// that we don't understand, we have to reject.
				// so start by copying the features array
				$features = $this->features;

				foreach ($data AS $feat)
				{
					// we support this feature, so turn it on.
					if (isset($features[$feat])) $features[$feat] = TRUE;
					// otherwise, signal that the client is ASKING FOR TOO MUCH. smack that "$)"%!
					else { $features = FALSE; break; }
				}
				if ($features === FALSE)
				{
					$this->sendCAP("NAK", ":%s", implode(" ",$data));
				}
				else
				{
					$this->sendCAP("ACK", ":%s", implode(" ",$data));
				}
				break;
			case 'ack': break; // we can probably safely ignore this...
			case 'nak': break; // we only send this; we shouldn't ever receive it.
			case 'clear':
				$turnoff = array();

				foreach ($this->features AS $key => $value)
				{
					if ($value == TRUE)
					{
						$this->features[$key] = FALSE;
						$turnoff[] = $key;
					}
				}
				$this->sendCAP("ACK", ":%s", implode(" ", $turnoff));
			case 'end':
				// all done!
				$this->inCAP = false;
				break;
			default:
				$this->sendNum(410, "%s :Invalid CAP command", strtoupper($subcmd));
		}
	}
	private function sendCAP($subcmd, $format)
	{
		$args = func_get_args();
		array_shift($args);
		array_shift($args);
		if (!empty($args))
			$message = vsprintf($format, $args);
		else
		$message = $format;	
		$this->send(":%s CAP %s %s %s", $this->serverName, (!empty($this->nick)) ? $this->nick : "*", $subcmd, $message);
	}

	private function cmd_nick($data)
	{
		$this->nick = substr(array_shift($data),1);
	}

	private function cmd_user($data)
	{
		$this->username = array_shift($data);
		array_shift($data);
		array_shift($data);
		$this->realname = substr(implode(" ", $data),1);
	}

	private function cmd_pass($data)
	{
		// the format for the param to PASS is username:network:password
		// but there are some circumstances under which one or more will be omitted.
		// password is always required.
		// SP = space
		// COLON = : (with whitespace around it removed)
		// syntax: PASS SP [ username COLON ] [ network COLON ] password
		
	}

	private function cmd_quit($data)
	{
		$this->_destroy();
	}
}
