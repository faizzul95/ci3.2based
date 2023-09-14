<?php

namespace App\services\modules\core\systemQueueJob\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemQueueJob\processors\SystemQueueJobDeleteProcessors;

class SystemQueueJobDeleteLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new SystemQueueJobDeleteProcessors)->execute($request);
    }
}