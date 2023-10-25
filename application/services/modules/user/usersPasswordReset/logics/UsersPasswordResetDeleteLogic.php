<?php

namespace App\services\modules\user\usersPasswordReset\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersPasswordReset\processors\UsersPasswordResetDeleteProcessors;

class UsersPasswordResetDeleteLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new UsersPasswordResetDeleteProcessors)->execute($request);
    }
}