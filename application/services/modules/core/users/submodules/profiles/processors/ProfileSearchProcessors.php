<?php

namespace App\services\modules\core\users\submodules\profiles\processors;

use App\services\generals\traits\QueryTrait;

class ProfileSearchProcessors
{
	use QueryTrait;

	public function execute($filter = NULL, $fetchType = 'get_all')
	{
		$query = $this->newQuery('PROFILE', $filter);

		if (hasData($filter)) {
			if (hasData($filter, 'searchQuery')) {
				$query->where('user_id', 'like', $filter['searchQuery'])                // this will be LIKE $search
					->where('roles_id', 'like', $filter['searchQuery'], true)           // if put true, will be OR LIKE $search. else will be AND LIKE $search
					->where('company_id', 'like', $filter['searchQuery'], true) 		// if put true, will be OR LIKE $search. else will be AND LIKE $search
					->where('department_id', 'like', $filter['searchQuery'], true); 	// if put true, will be OR LIKE $search. else will be AND LIKE $search
			}
		}

		return $query->$fetchType();
	}
}
