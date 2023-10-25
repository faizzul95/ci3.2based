<?php

namespace App\services\modules\user\users\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\users\processors\UsersSearchProcessors;

class UsersShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new UsersSearchProcessors)->execute(
			[
				'fields' => 'id,name,user_preferred_name,user_nric,email,user_contact_no,user_gender,user_dob,user_status,user_join_date,username,password,social_id,social_type,two_factor_status,two_factor_type,two_factor_secret,two_factor_recovery_codes,remember_token,is_deleted',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}