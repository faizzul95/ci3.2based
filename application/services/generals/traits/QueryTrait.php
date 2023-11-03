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

			if (hasData($filter, 'hidden')) {
				// if set to true. will return all data include hidden field
				// else set to false. the hidden field will be exclude in result query
				$query->with_hidden($filter['hidden']);
			}

			if (hasData($filter, 'sum')) {
				if (is_array($filter['sum'])) {
					if (isMultidimension($filter['sum'])) {
						foreach ($filter['sum'] as $sum) {
							if (is_array($sum)) {
								$query->select_sum($sum[0], $sum[1]);
							} else {
								$query->select_sum($sum);
							}
						}
					} else {
						$query->select_sum($filter['sum'][0], $filter['sum'][1]);
					}
				} else {
					$query->select_sum($filter['sum']);
				}
			}

			if (hasData($filter, 'min')) {
				if (is_array($filter['min'])) {
					if (isMultidimension($filter['min'])) {
						foreach ($filter['min'] as $min) {
							if (is_array($min)) {
								$query->select_min($min[0], $min[1]);
							} else {
								$query->select_min($min);
							}
						}
					} else {
						$query->select_min($filter['min'][0], $filter['min'][1]);
					}
				} else {
					$query->select_min($filter['min']);
				}
			}

			if (hasData($filter, 'max')) {
				if (is_array($filter['max'])) {
					if (isMultidimension($filter['max'])) {
						foreach ($filter['max'] as $max) {
							if (is_array($max)) {
								$query->select_max($max[0], $max[1]);
							} else {
								$query->select_max($max);
							}
						}
					} else {
						$query->select_max($filter['max'][0], $filter['max'][1]);
					}
				} else {
					$query->select_max($filter['max']);
				}
			}

			if (hasData($filter, 'limit')) {
				$query->limit($filter['limit'], 0);
			}

			if (hasData($filter, 'order')) {
				if(is_array($filter['order']))
					$query->order_by($filter['order'][0], $filter['order'][1]);
				else
					$query->order_by($filter['order']);
			}

			if (hasData($filter, 'order')) {
				if (is_array($filter['order'])) {
					if (isMultidimension($filter['order'])) {
						foreach ($filter['order'] as $order) {
							if (is_array($order)) {
								$query->order_by($order[0], $order[1]);
							} else {
								$query->order_by($order);
							}
						}
					} else {
						$query->order_by($filter['order'][0], $filter['order'][1]);
					}
				} else {
					$query->order_by($filter['order']);
				}
			}
			
		}

		return $query;
	}

	public function collectionRecord($query, $filter, $fetchType, $cache_files_name = NULL)
	{
		if (hasData($filter)) {
			if (hasData($filter, 'locked')) {
				$methodName = $filter['locked'];
				if (method_exists($query, $methodName)) {
					$query->$methodName($query->$fetchType($query));
				}
			}
		}

		if (hasData($cache_files_name)) {
			if ($fetchType != 'toSql')
				$query->set_cache($cache_files_name);
		}

		return $fetchType == 'toSql' ? $query->$fetchType($query) : $query->$fetchType();;
	}
}
