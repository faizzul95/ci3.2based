<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\middleware\core\traits\SecurityHeadersTrait;
use App\services\modules\authentication\logics\LoginLogic;
use App\services\modules\authentication\logics\SocialliteLogic;
use App\services\modules\authentication\logics\ForgotPasswordLogic;
use App\services\modules\authentication\logics\TwoFactorAuthenticateLogic;
use App\services\modules\core\users\logics\UserProfileSwitchLogic;

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
		jsonResponse(app(new LoginLogic)->logic([
			'username'  => input('username'),
			'password'  => input('password'),
			'rememberme'  => input('remember') ? true : false
		]));
	}

	public function socialite()
	{
		jsonResponse(app(new SocialliteLogic)->logic([
			'email'  => input('email'),
			'rememberme'  => input('remember') ? true : false
		]));
	}

	public function resetPasswordLink()
	{
		jsonResponse(app(new ForgotPasswordLogic)->sent([
			'email'  => input('email'),
		]));
	}

	public function resetPasswordPage($token = NULL)
	{
		// check if token has data
		if (hasData($token)) {
			$response = app(new ForgotPasswordLogic)->form(input('token'));

			// check if token is valid, render page to reset
			if (isSuccess($response)) {
				render('auth/reset',  [
					'title' => 'Reset Password',
					'currentSidebar' => 'auth',
					'currentSubSidebar' => 'reset',
					'data' => $response['data']
				]);
			} else {
				jsonResponse($response);
			}
		} else {
			redirect('', true);
		}
	}

	public function verify2FA()
	{
		jsonResponse(app(new TwoFactorAuthenticateLogic)->logic([
			'username'  =>  input('username'),
			'code'  => input('code_2fa'),
			'rememberme'  => input('remember') ? true : false
		]));
	}

	public function switchProfileUser()
	{
		jsonResponse(app(new UserProfileSwitchLogic)->logic([
			'profile_id'  =>  input('profile_id'),
			'user_id'  => input('user_id')
		]));
	}

	public function logout()
	{
		destroySession(true, '');
	}
}