<?php

namespace App\services\modules\master\masterCompany\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\master\masterCompany\processors\MasterCompanySearchProcessors;

class MasterCompanyShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new MasterCompanySearchProcessors)->execute(
			[
				'fields' => 'company_name,company_code,company_email,company_fax_no,company_pic_name,company_pic_office_no,company_subdomain,company_status',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}