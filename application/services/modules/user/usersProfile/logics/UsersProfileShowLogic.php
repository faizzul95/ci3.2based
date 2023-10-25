<?php

namespace App\services\modules\user\usersProfile\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersProfile\processors\UsersProfileSearchProcessors;

class UsersProfileShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new UsersProfileSearchProcessors)->execute(
			[
				'fields' => 'id,user_id,role_id,is_main,company_id,profile_status',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}