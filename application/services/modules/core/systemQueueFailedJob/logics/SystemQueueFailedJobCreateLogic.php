<?php

namespace App\services\modules\core\systemQueueFailedJob\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemQueueFailedJob\processors\SystemQueueFailedJobStoreProcessors;

class SystemQueueFailedJobCreateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new SystemQueueFailedJobStoreProcessors)->execute($request);
    }
}
