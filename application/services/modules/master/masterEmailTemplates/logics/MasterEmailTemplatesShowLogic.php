<?php

namespace App\services\modules\master\masterEmailTemplates\logics;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\modules\master\masterEmailTemplates\processors\MasterEmailTemplatesSearchProcessors;

class MasterEmailTemplatesShowLogic
{
    protected $request = [];

    public function __construct()
    {
    }

    public function logic($request)
    {
        return app(new MasterEmailTemplatesSearchProcessors)->execute(
			[
				'fields' => 'email_type,email_subject,email_body,email_footer,email_cc,email_bcc,email_status,company_id',
				'conditions' => array_merge($this->request, ['id' => $request['id']]),
			],
			'get'
		);
    }
}