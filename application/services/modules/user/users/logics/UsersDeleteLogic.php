<?php

namespace App\services\modules\user\users\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\user\users\processors\UsersDeleteProcessors;

class UsersDeleteLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new UsersDeleteProcessors)->execute($request);
    }
}