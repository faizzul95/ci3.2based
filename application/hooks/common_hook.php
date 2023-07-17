<?php

include_once FCPATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('value')) {
	/**
	 * Return the default value of the given value.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	function value($value = NULL)
	{
		return $value instanceof Closure ? $value() : $value;
	}
}

if (!function_exists('env')) {
	/**
	 * Gets the value of an environment variable.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	function env($key = NULL, $default = NULL)
	{
		if (file_exists(FCPATH . '/.env')) {
			$dotenv = Dotenv\Dotenv::createUnsafeImmutable(FCPATH);
			$dotenv->load();
		} else {
			die('env file not found');
		}

		$value = getenv($key);

		if ($value === false) {
			return value($default);
		}

		switch (strtolower($value)) {
			case 'true':
			case '(true)':
				return true;
			case 'false':
			case '(false)':
				return false;
			case 'empty':
			case '(empty)':
				return '';
			case 'null':
			case '(null)':
				return;
		}

		if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
			return substr($value, 1, -1);
		}

		return $value;
	}
}

if (!function_exists('ddd')) {
	function ddd()
	{
		array_map(function ($param) {
			echo '<pre>';
			print_r($param);
			echo '</pre>';
		}, func_get_args());
		die;
	}
}
