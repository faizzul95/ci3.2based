<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// file: application/libraries/Env.php
class Env
{
	public function __construct()
	{
		if (file_exists(FCPATH . '/.env')) {
			$dotenv = Dotenv\Dotenv::createUnsafeImmutable(FCPATH);
			$dotenv->load();
		} else {
			die('env file not found');
		}
	}
}
