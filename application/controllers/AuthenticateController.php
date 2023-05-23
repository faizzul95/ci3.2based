<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\middleware\core\traits\SecurityHeadersTrait;
use App\services\modules\authentication\logics\LoginLogic;
use App\services\modules\authentication\logics\SocialliteLogic;
use App\services\modules\authentication\logics\ForgotPasswordLogic;
use App\services\modules\authentication\logics\TwoFactorAuthenticateLogic;

class AuthenticateController extends CI_Controller
{
	use SecurityHeadersTrait;

	public function __construct()
	{
		parent::__construct();
		$this->set_security_headers();
	}

	public function authorize()
	{
		json(app(new LoginLogic)->logic([
			'username'  => input('username'),
			'password'  => input('password'),
			'rememberme'  => input('remember') ? true : false
		]));
	}

	public function socialite()
	{
		json(app(new SocialliteLogic)->logic([
			'email'  => input('email'),
			'rememberme'  => input('remember') ? true : false
		]));
	}

	public function resetPasswordLink()
	{
		json(app(new ForgotPasswordLogic)->sent([
			'email'  => input('email'),
		]));
	}

	public function resetPasswordPage($token = NULL)
	{
		// check if token has data
		if (hasData($token)) {
			app(new ForgotPasswordLogic)->form(input('token'));
		} else {
			redirect('', true);
		}
	}

	public function verify2FA()
	{
		json(app(new TwoFactorAuthenticateLogic)->execute([
			'username'  =>  input('username'),
			'code'  => input('code_2fa'),
			'rememberme'  => input('remember') ? true : false
		]));
	}

	public function logout()
	{
		delete_cookie('remember_me_token_cipmo');
		destroySession(true, '');
	}
}
