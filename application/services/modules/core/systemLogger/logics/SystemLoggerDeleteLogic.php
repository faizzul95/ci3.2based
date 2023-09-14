<?php

namespace App\services\modules\core\systemLogger\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemLogger\processors\SystemLoggerDeleteProcessors;

class SystemLoggerDeleteLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new SystemLoggerDeleteProcessors)->execute($request);
    }
}