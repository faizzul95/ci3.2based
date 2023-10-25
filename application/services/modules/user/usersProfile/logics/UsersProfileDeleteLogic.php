<?php

namespace App\services\modules\user\usersProfile\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersProfile\processors\UsersProfileDeleteProcessors;

class UsersProfileDeleteLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new UsersProfileDeleteProcessors)->execute($request);
    }
}