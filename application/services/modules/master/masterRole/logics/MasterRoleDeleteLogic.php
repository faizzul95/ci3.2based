<?php

namespace App\services\modules\master\masterRole\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\master\masterRole\processors\MasterRoleDeleteProcessors;

class MasterRoleDeleteLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new MasterRoleDeleteProcessors)->execute($request);
    }
}