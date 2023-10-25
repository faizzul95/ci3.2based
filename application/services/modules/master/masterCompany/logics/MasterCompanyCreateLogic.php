<?php

namespace App\services\modules\master\masterCompany\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\master\masterCompany\processors\MasterCompanyStoreProcessors;

class MasterCompanyCreateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new MasterCompanyStoreProcessors)->execute($request);
    }
}