<?php

namespace App\services\modules\user\usersLoginAttempt\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersLoginAttempt\processors\UsersLoginAttemptDeleteProcessors;

class UsersLoginAttemptDeleteLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new UsersLoginAttemptDeleteProcessors)->execute($request);
    }
}