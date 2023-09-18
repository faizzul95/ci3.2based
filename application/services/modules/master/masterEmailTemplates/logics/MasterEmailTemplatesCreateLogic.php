<?php

namespace App\services\modules\master\masterEmailTemplates\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\master\masterEmailTemplates\processors\MasterEmailTemplatesStoreProcessors;

class MasterEmailTemplatesCreateLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new MasterEmailTemplatesStoreProcessors)->execute($request);
    }
}