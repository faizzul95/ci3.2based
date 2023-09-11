<?php

/**
 * Author: takielias
 * Github Repo : https://github.com/takielias/codeigniter-websocket
 * Date: 04/05/2019
 * Time: 09:04 PM
 */

/**
 * Inspired By
 * Ratchet Websocket Library: helper file
 * @author Romain GALLIEN <romaingallien.rg@gmail.com>
 */

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('valid_json')) {

	/**
	 * Check JSON validity
	 * @method valid_json
	 * @param mixed $var Variable to check
	 * @return bool
	 */
	function valid_json($var)
	{
		return (is_string($var)) && (is_array(json_decode(
			$var,
			true
		))) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
}

if (!function_exists('valid_jwt')) {

	/**
	 * Check JWT validity
	 * @method valid_jwt
	 * @param mixed $token Variable to check
	 * @return Object/false
	 */
	function valid_jwt($token)
	{
		return App\libraries\CI_WebSocket\Helpers\AUTHORIZATION::validateToken($token);
	}
}

/**
 * Codeigniter Websocket Library: helper file
 */
if (!function_exists('output')) {

	/**
	 * Output valid or invalid logs
	 * @method output
	 * @param string $type Log type
	 * @param string $var String
	 * @return string
	 */
	function output($type = 'success', $message = null)
	{
		// Define color codes for text and background
		$colors = [
			'success' => ['text' => "\e[97m", 'bg' => "\e[42m"], // White text on green background for SUCCESS
			'info'    => ['text' => "\e[97m", 'bg' => "\e[44m"], // White text on blue background for INFO
			'error'   => ['text' => "\e[97m", 'bg' => "\e[41m"], // White text on red background for ERROR
			'warning' => ['text' => "\e[30m", 'bg' => "\e[43m"], // Black text on yellow background for WARNING
		];

		// Check if the specified type is valid, otherwise use default colors
		if (!isset($colors[$type])) {
			$type = 'default';
		}

		// ANSI color code for text reset
		$colorReset = "\e[0m";

		// Format and display the badge and message
		$badge = strtoupper($type);
		$textColor = $colors[$type]['text'];
		$bgColor = $colors[$type]['bg'];

		echo "{$bgColor}{$textColor} {$badge} {$colorReset} {$message}\n" . PHP_EOL;
	}
}
