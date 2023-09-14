<?php

namespace App\services\modules\core\systemLogger\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemLogger\processors\SystemLoggerStoreProcessors;

class SystemLoggerUpdateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new SystemLoggerStoreProcessors)->execute($request);
    }
}