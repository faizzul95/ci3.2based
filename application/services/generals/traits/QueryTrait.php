<?php

namespace App\services\generals\traits;

trait QueryTrait
{
	public function newQuery($model, $filter = NULL)
	{
		model($model);

		$query = ci()->$model;

		if (hasData($filter)) {
			if (hasData($filter, 'fields')) {
				$query->fields($filter['fields']);
			}

			if (hasData($filter, 'with')) {
				$query->scopeWithQuery($query, $filter['with']);
			}

			if (hasData($filter, 'conditions')) {
				$query->scopeConditionQuery($query, $filter['conditions']);
			}

			if (hasData($filter, 'limit')) {
				$query->limit($filter['limit'], 0);
			}

			if (hasData($filter, 'hidden')) {
				// if set to true. will return all data include hidden field
				// else set to false. the hidden field will be exclude in result query
				$query->with_hidden($filter['hidden']);
			}
		}

		return $query;
	}
}
