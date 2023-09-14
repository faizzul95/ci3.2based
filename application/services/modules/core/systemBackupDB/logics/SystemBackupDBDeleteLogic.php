<?php

namespace App\services\modules\core\systemBackupDB\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemBackupDB\processors\SystemBackupDBDeleteProcessors;

class SystemBackupDBDeleteLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new SystemBackupDBDeleteProcessors)->execute($request);
    }
}