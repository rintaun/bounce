<?php

/************************************************************
 * Bounce v0.1-alpha                                        *
 * Author: rintaun - Matthew J. Lanigan <rintaun@gmail.com> *
 *                                                          *
 * Copyright 2011 Matthew J. Lanigan.                       *
 * See LICENSE file for licensing details.                  *
 ************************************************************
 * src/inc/Singleton.php                                    *
 * Last Modified: 4/28/2011                                 *
 *                                                          *
 * Description: Singletons are awesome. :D                  *
 ************************************************************/

class Singleton
{
	private static $_instance;

	protected function __construct() {}
	
	public static function singleton()
	{
		$i = get_called_class();
		if (!isset(self::$_instance[$i]))
		{
			self::$_instance[$i] = new $i;
		}
		return self::$_instance[$i];
	}

	public function __clone()
	{
		trigger_error('You may not clone a singleton object.', E_USER_ERROR);
	}

	public function destroy()
	{
		unset(self::$_instance[get_class($this)]);
	}

	public function __destruct()
	{
		unset(self::$instance);
	}
}
