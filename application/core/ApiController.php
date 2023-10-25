<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\libraries\AuthToken;
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

		// // Load your Auth library for verification
		AuthToken::verify('read');

		// // Set each action for own permission verification

		// $this->_setBehavior('store', function () {
		// 	$this->auth->verify('create');
		// });

		// $this->_setBehavior('update', function () {
		// 	$this->auth->verify('update');
		// });

		// $this->_setBehavior('delete', function () {
		// 	$this->auth->verify('delete');
		// });
	}
}
