<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function autoload()
{
	spl_autoload_register(function ($class) {
		if (substr($class, 0, 3) !== 'CI_') {
			if (file_exists($file = APPPATH . 'core/' . $class . '.php')) {
				require_once $file;
			}
		}
	});
}
