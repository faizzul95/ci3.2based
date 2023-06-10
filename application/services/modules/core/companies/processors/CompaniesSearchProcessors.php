<?php

namespace App\services\modules\core\companies\processors;

use App\services\generals\traits\QueryTrait;

class CompaniesSearchProcessors
{
	use QueryTrait;

	public function execute($filter = NULL, $fetchType = 'get_all')
	{
		$query = $this->newQuery('COMPANY', $filter);

		if (hasData($filter)) {
			if (hasData($filter, 'searchQuery')) {
				$query->where('company_name', 'like', $filter['searchQuery'])           // this will be LIKE $search
					->where('company_nickname', 'like', $filter['searchQuery'], true) 	// if put true, will be OR LIKE $search. else will be AND LIKE $search
					->where('company_no', 'like', $filter['searchQuery'], true); 		// if put true, will be OR LIKE $search. else will be AND LIKE $search
			}
		}

		return $query->$fetchType();
	}
}
