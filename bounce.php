<?php

/************************************************************
 * Bounce v0.1-alpha                                        *
 * Author: rintaun - Matthew J. Lanigan <rintaun@gmail.com> *
 *                                                          *
 * Copyright 2011 Matthew J. Lanigan.                       *
 * See LICENSE file for licensing details.                  *
 ************************************************************
 * bounce.php                                               *
 *                                                          *
 * Description: Bounce program wrapper                      *
 ************************************************************/

require_once('src/Bounce.php');

$GLOBALS['version'] = "0.1-alpha";
$GLOBALS['fork'] = false;
$GLOBALS['forked'] = false;

_log(L_INFO, 'Bounce v%s starting...', $GLOBALS['version']);

$ARGS = getopt('dc:l:f');
if (is_array($ARGS))
{
        foreach($ARGS AS $arg => $value)
        {
                switch($arg)
                {
			case 'd':
				_log(L_INFO, 'Starting in debug mode.');
				$GLOBALS['debug'] = true;
				break;
			case 'c':
				_log(L_INFO, 'Using configuration file %s.', $value);
				$GLOBALS['configfile'] = $value;
				break;
			case 'l':
				_log(L_INFO, 'Using log file %s.', $value);
				$GLOBALS['logfile'] = $value;
				break;
                        case 'f':
				$GLOBALS['fork'] = true;
				break;
                }
	}
}

if ($GLOBALS['fork'] === true)
{
	_log(L_DEBUG, 'Forking into the background...');
	$pid = pcntl_fork();
	if ($pid == -1)
		_log(L_FATAL, 'Failed to fork into the background. Exiting.');
	else if ($pid)
	{
		_log(L_DEBUG, 'Forked successfully. Exiting parent.');
		exit;
	}
	else
		$GLOBALS['forked'] = true;
}

$bounce = Bounce::getInstance();
$bounce->start();
