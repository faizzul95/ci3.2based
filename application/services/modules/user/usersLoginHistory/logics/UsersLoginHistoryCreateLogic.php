<?php

namespace App\services\modules\user\usersLoginHistory\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersLoginHistory\processors\UsersLoginHistoryStoreProcessors;

class UsersLoginHistoryCreateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new UsersLoginHistoryStoreProcessors)->execute($request);
    }
}