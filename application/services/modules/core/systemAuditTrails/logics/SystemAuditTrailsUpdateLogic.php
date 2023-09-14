<?php

namespace App\services\modules\core\systemAuditTrails\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemAuditTrails\processors\SystemAuditTrailsStoreProcessors;

class SystemAuditTrailsUpdateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new SystemAuditTrailsStoreProcessors)->execute($request);
    }
}