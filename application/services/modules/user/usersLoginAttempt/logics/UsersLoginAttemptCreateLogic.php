<?php

namespace App\services\modules\user\usersLoginAttempt\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersLoginAttempt\processors\UsersLoginAttemptStoreProcessors;

class UsersLoginAttemptCreateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new UsersLoginAttemptStoreProcessors)->execute($request);
    }
}