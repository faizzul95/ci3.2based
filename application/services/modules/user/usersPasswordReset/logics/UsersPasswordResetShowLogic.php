<?php

namespace App\services\modules\user\usersPasswordReset\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersPasswordReset\processors\UsersPasswordResetSearchProcessors;

class UsersPasswordResetShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new UsersPasswordResetSearchProcessors)->execute(
			[
				'fields' => 'user_id,email,reset_token,reset_token_expired',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}