<?php

defined('BASEPATH') or exit('No direct script access allowed');

// use App\middleware\core\traits\SecurityHeadersTrait;

class Controller extends CI_Controller
{
	// use SecurityHeadersTrait;

	public function __construct()
	{
		parent::__construct();
		// $this->set_security_headers();

		// isLogin();
		library('form_validation');
	}
}