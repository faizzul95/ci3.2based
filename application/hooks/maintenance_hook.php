<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Check whether the site is offline or not.
 *
 */
class Maintenance_hook
{
	public function __construct()
	{
		log_message('debug', 'Accessing maintenance hook!');
	}

	public function offline_check()
	{
		$maintenance_mode = isset($_ENV['MAINTENANCE_MODE']) ? filter_var($_ENV['MAINTENANCE_MODE'], FILTER_VALIDATE_BOOLEAN) : false;
		$maintenance_ips = array(
			// '127.0.0.1',
			// '::1',
		);

		if ($maintenance_mode === TRUE) {
			if (!in_array($_SERVER['REMOTE_ADDR'], $maintenance_ips)) {

				header('HTTP/1.1 503 Service Temporarily Unavailable');
				header('Status: 503 Service Temporarily Unavailable');
				header('Retry-After: 7200');

				include(APPPATH . 'views/errors/maintenance.php');
				exit;
				die;
			}
		}
	}
}
