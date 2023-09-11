<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\middleware\core\traits\SecurityHeadersTrait;

class ApiController extends yidas\rest\Controller
{
	use SecurityHeadersTrait;

	function __construct() 
    {
        parent::__construct();
		$this->set_security_headers();

		// isLogin();
		library('form_validation');
    }
}
