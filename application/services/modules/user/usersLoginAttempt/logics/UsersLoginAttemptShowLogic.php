<?php

namespace App\services\modules\user\usersLoginAttempt\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersLoginAttempt\processors\UsersLoginAttemptSearchProcessors;

class UsersLoginAttemptShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new UsersLoginAttemptSearchProcessors)->execute(
			[
				'fields' => 'user_id,ip_address,time,user_agent',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}