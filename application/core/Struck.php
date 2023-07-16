<?php

namespace App\core;

defined('BASEPATH') or exit('No direct script access allowed');

class Struck
{
	public static function call($command)
	{
		$command = strtolower($command);

		switch ($command) {
			case 'down':
				echo shell_exec('php struck maintenance on');
				break;

			case 'up':
				echo shell_exec('php struck maintenance off');
				break;

			case 'optimize':
				echo shell_exec('php struck clear optimize');
				break;

			default:
				// Handle unrecognized command
				echo "Unrecognized command: $command";
				break;
		}
	}
}
