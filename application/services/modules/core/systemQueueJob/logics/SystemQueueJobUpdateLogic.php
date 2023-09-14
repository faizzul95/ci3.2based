<?php

namespace App\services\modules\core\systemQueueJob\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemQueueJob\processors\SystemQueueJobStoreProcessors;

class SystemQueueJobUpdateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new SystemQueueJobStoreProcessors)->execute($request);
    }
}