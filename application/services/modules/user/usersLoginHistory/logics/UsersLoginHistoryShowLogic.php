<?php

namespace App\services\modules\user\usersLoginHistory\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersLoginHistory\processors\UsersLoginHistorySearchProcessors;

class UsersLoginHistoryShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new UsersLoginHistorySearchProcessors)->execute(
			[
				'fields' => 'user_id,ip_address,login_type,operating_system,browsers,time,user_agent',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}