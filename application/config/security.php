
<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Throttle Status Enable
|--------------------------------------------------------------------------
|
| Set [TRUE/FALSE] to set Rate Limitting.
|
*/
$config['throttle_enable'] = TRUE;

/*
|--------------------------------------------------------------------------
| Throttle Override Settings
|--------------------------------------------------------------------------
|
| Set TRUE to use override the throttle settings below or set FALSE to use default
|
*/
$config['throttle_override'] = FALSE;

/*
|--------------------------------------------------------------------------
| Throttle Configuration
|--------------------------------------------------------------------------
|
| Define all Rate Limitting configuration
*/
$config['throttle_settings'] = [
	'request' => 60, // set default limit request for client
	'interval' => 60, // set the interval before request count will be reset in seconds (eg : 5 minute = 300)
	'warning' => 20, // set maximum message "Too many requests" before temporary blocked the IP
	'blocked' => 15, // set maximum "Temporary blocked" count before permanently block IP (Maximum is 15)
	'limit_increase' => 15, // set the increase request (will extend then default) per temporary blocked. Formula : request + (limit_increase x blocked)
];

/*
|--------------------------------------------------------------------------
| Throttle URL Exclusions
|--------------------------------------------------------------------------
|
| Define all route to exclude for rate limiting
*/
$config['throttle_exclude_url'] = [
	// 'auth/sign-in',
	// 'auth/logout',
];

/*
|--------------------------------------------------------------------------
| Throttle IP Exclusions
|--------------------------------------------------------------------------
|
| Define all IP to exclude for rate limiting
*/
$config['throttle_exclude_ips'] = [
	// '127.0.0.1',
	// '::1'
];
