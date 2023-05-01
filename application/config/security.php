
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
	'request' => 850, // set maximum requests per minute
	'expired' => 60, // set expiration time in seconds
	'reminder' => 5, //set maximum reminder before block
	'block' => 86400 // set block time in seconds (86400 second = 24 hours)
];

/*
|--------------------------------------------------------------------------
| Throttle Exclusions
|--------------------------------------------------------------------------
|
| Define all route to exclude for rate limitting
*/
$config['throttle_exclude_url'] = [
	// 'auth/logout', sample
];
