<?php

namespace App\services\modules\core\users\processors;

use App\services\generals\traits\QueryTrait;

class UserSearchProcessors
{
	use QueryTrait;

	public function execute($filter = NULL, $fetchType = 'get_all')
	{
		$query = $this->newQuery('USER', $filter);

		if (hasData($filter)) {
			// use for login only
			if (hasData($filter, 'whereQuery')) {
				$query->where('id', $filter['whereQuery'])             // this will be WHERE $search
					->where('email', $filter['whereQuery'], NULL, true)      // if put true, will be OR WHERE $search. else will be AND WHERE $search
					->where('username', $filter['whereQuery'], NULL, true);  // if put true, will be OR WHERE $search. else will be AND WHERE $search
			}

			if (hasData($filter, 'searchQuery')) {
				$query->where('name', 'like', $filter['searchQuery'])               // this will be LIKE $search
					->where('email', 'like', $filter['searchQuery'], true)          // if put true, will be OR LIKE $search. else will be AND LIKE $search
					->where('user_nric_visa', 'like', $filter['searchQuery'], true) // if put true, will be OR LIKE $search. else will be AND LIKE $search
					->where('user_staff_no', 'like', $filter['searchQuery'], true); // if put true, will be OR LIKE $search. else will be AND LIKE $search
			}
		}

		if ($fetchType == 'toSql')
			return $query->toSql($query);
		else
			return $query->$fetchType();
	}
}
