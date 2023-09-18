<?php

namespace App\services\modules\master\masterEmailTemplates\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\master\masterEmailTemplates\processors\MasterEmailTemplatesDeleteProcessors;

class MasterEmailTemplatesDeleteLogic
{
    public function __construct()
    {
    }

    public function logic($request)
    {
       	return app(new MasterEmailTemplatesDeleteProcessors)->execute($request);
    }
}