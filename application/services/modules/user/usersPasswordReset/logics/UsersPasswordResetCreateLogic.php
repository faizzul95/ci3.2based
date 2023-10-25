<?php

namespace App\services\modules\user\usersPasswordReset\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\usersPasswordReset\processors\UsersPasswordResetStoreProcessors;

class UsersPasswordResetCreateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new UsersPasswordResetStoreProcessors)->execute($request);
    }
}