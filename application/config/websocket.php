<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['websocket'] = array(
	'host' => '0.0.0.0',
	'port' => 8282,
	'timer_enabled' => false,
	'timer_interval' => 1, // 1 means 1 seconds
	'auth' => false,
	'debug' => true
);
