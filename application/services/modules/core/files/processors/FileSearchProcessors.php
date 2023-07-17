<?php

namespace App\services\modules\core\files\processors;

use App\services\generals\traits\QueryTrait;

class FileSearchProcessors
{
	use QueryTrait;

	public function execute($filter = NULL, $fetchType = 'get_all')
	{
		$query = $this->newQuery('EntityFiles_model', $filter);

		if (hasData($filter)) {

			if (hasData($filter, 'searchQuery')) {
				$query->where('files_name', 'like', $filter['searchQuery'])              // this will be LIKE $search
					->where('entity_id', 'like', $filter['searchQuery'], true)    		 // if put true, will be OR LIKE $search. else will be AND LIKE $search
					->where('entity_file_type', 'like', $filter['searchQuery'], true);    // if put true, will be OR LIKE $search. else will be AND LIKE $search
			}
		}

		return $query->$fetchType();
	}
}
