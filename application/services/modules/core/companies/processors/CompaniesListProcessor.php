<?php

namespace App\services\modules\core\companies\processors;

use App\services\modules\core\companies\processors\CompaniesSearchProcessors;

class CompaniesListProcessor
{
	public function execute()
	{
		// 
	}

	public function listSelect($request = NULL)
	{
		$data = app(new CompaniesSearchProcessors)->execute();

		$select = '<option value=""> All Company </option>';
		if (hasData($data)) {
			foreach ($data as $row) {
				$select .= '<option value="' . $row['id'] . '"> ' . purify($row['company_name']) . '</option>';
			}
		}

		return $select;
	}
}
