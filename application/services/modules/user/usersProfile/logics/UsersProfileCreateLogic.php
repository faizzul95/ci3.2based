<?php

namespace App\services\modules\user\usersProfile\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersProfile\processors\UsersProfileStoreProcessors;

class UsersProfileCreateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new UsersProfileStoreProcessors)->execute($request);
    }
}