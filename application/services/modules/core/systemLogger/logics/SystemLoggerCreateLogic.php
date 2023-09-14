<?php

namespace App\services\modules\core\systemLogger\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemLogger\processors\SystemLoggerStoreProcessors;

class SystemLoggerCreateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new SystemLoggerStoreProcessors)->execute($request);
    }
}