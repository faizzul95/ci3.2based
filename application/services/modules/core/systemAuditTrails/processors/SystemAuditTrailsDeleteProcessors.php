<?php

namespace App\services\modules\core\systemAuditTrails\processors;

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\generals\traits\QueryTrait;

class SystemAuditTrailsDeleteProcessors
{
    use QueryTrait;

    protected $model;

	public function __construct()
	{
		$this->model = 'SystemAuditTrails_model';
	}

	public function execute($request = NULL)
	{
		$query = $this->newQuery($this->model, ['conditions' => $request]);
		$count = $query->count_rows();

		if ($count > 0) {
			$getType = $count == 1 ? 'get' : 'get_all';
			$data = $this->newQuery($this->model, ['conditions' => $request])->$getType();
			$code = $this->newQuery($this->model, ['conditions' => $request])->delete() ? 200 : 400;
			return returnData([
				"action" => 'delete',
				"code" => $code,
				"message" =>  message($code, 'delete successfully'),
				"id" => $count == 1 ? $data[$query->primary_key] : NULL,
				"data" => $data
			], $code);
		}

		return returnData(["action" => 'delete', "code" => 400, "message" =>  message(400, 'remove unsuccessful')], 400);
	}
}
