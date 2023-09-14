<?php

namespace App\services\modules\core\systemAuditTrails\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\core\systemAuditTrails\processors\SystemAuditTrailsSearchProcessors;

class SystemAuditTrailsShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new SystemAuditTrailsSearchProcessors)->execute(
			[
				'fields' => '',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}