<?php

namespace App\services\modules\core\systemBackupDB\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemBackupDB\processors\SystemBackupDBStoreProcessors;

class SystemBackupDBCreateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new SystemBackupDBStoreProcessors)->execute($request);
    }
}