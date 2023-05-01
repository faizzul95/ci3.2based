
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
| Throttle Configuration
|--------------------------------------------------------------------------
|
| Define all Rate Limitting configuration
*/
$config['throttle_settings'] = [
	'request' => 200, // set maximum api requests (according to expired below : request per expired)
	'expired' => 60, // set expiration time for cache file store in seconds
	'block' => 45 // set blocked time duration in seconds (60 second = 1 minute)
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
	'auth/logout',
];

/*
|--------------------------------------------------------------------------
| Throttle IP Exclusions
|--------------------------------------------------------------------------
|
| Define all route to exclude for rate limiting
*/
$config['throttle_exclude_ips'] = [];
