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
				'fields' => 'user_id,role_id,user_fullname,event,table_name,old_values,new_values,url,ip_address,user_agent',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}