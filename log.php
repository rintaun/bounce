<?
define('L_DEBUG',   0x01);
define('L_WARNING', 0x02);
define('L_NOTICE',  0x04);
define('L_ERROR',   0x08);
define('L_FATAL',   0x10);
define('L_NODEBUG', 0xFE);
define('L_ALL',     0xFF);

function _log($level, $format)
{
	$time = time();
	$args = func_get_args();
	array_shift($args);
	array_shift($args);
	
	if (is_array($args))
		$message = vsprintf($format, $args);
	else	$message = $format;

	$GLOBALS['log'][] = array(
		'time' => $time,
		'level' => $level,
		'message' => $message
	);

	if (!($GLOBALS['config']['loglevel'] & $level)) return;
	if ($GLOBALS['forked'] != true) echo sprintf("[%s] %s", date("H:i", $time), $message)."\n";
	if (is_resource($GLOBALS['logfile'])) fwrite($GLOBALS['logfile'], sprintf("[%s] %s: %s\n", date("H:i",$time), loglevel($level), $message));
        if ($level & L_FATAL) { $GLOBALS['exit'] = true; exit; }
}

function _irclog($level, $target, $format)
{
	if (substr($target,0,1) != "#") return;	

	$time = time();
	$args = func_get_args();
	array_shift($args);
	array_shift($args);
	array_shift($args);

	if (is_array($args))
		$message = vsprintf($format, $args);
	else	$message = $format;

	_log($level, "[%s] %s", $target, $message);
	$logfile = fopen($GLOBALS['config']['logsdir'] . $target . ".log", 'a+');
	fwrite($logfile, sprintf("[%s] %s\n", date("H:i", $time), $message));
	fclose($logfile);
}

function loglevel($level)
{
	if ($level == L_ALL) return "L_ALL";
	if ($level == L_NODEBUG) return "L_NODEBUG";
	if ($level == L_DEBUG) $levels[] = "L_DEBUG";
	if ($level == L_WARNING) $levels[] = "L_WARNING";
	if ($level == L_NOTICE) $levels[] = "L_NOTICE";
	if ($level == L_ERROR) $levels[] = "L_ERROR";
	if ($level == L_FATAL) $levels[] = "L_FATAL";

	if (count($levels) <= 1) return $levels[0];
	return implode(" | ", $levels);
}
?>
