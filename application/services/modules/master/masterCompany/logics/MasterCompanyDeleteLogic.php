<?php

namespace App\services\modules\master\masterCompany\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\master\masterCompany\processors\MasterCompanyDeleteProcessors;

class MasterCompanyDeleteLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new MasterCompanyDeleteProcessors)->execute($request);
    }
}