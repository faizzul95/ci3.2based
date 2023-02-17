<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CT_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	// permission($slug) get permission list
	public static function permission($slug = NULL)
	{
		return permission($slug);
	}

	// all() takes all data in a model. If no matching model exist, it returns null
	public static function all($condition = NULL, $orderBy = NULL, $with = NULL)
	{
		$className = get_called_class();
		$obj = new $className;
		$data = findAll($obj->table, xssClean($condition), $orderBy);

		if (!empty($with)) {
			if (isset($obj->with)) {
				$data = (new self)->withData($obj, $with, $data);
			}
		}

		return (!empty($data)) ? purify($data) : NULL;
	}

	// find($id) takes an id and returns a single model. If no matching model exist, it returns null
	public static function find($id = NULL, $columnName = NULL, $with = NULL)
	{
		$id = xssClean($id);
		$className = get_called_class();
		$obj = new $className;

		$columnName = (!empty($columnName)) ? $columnName : $obj->primary_key;
		$data = find($obj->table, [$columnName => $id], 'row_array');

		if (!empty($with)) {
			if (isset($obj->with)) {
				$data = (new self)->withData($obj, $with, $data);
			}
		}

		return (!empty($data)) ? purify($data) : NULL;
	}

	// whereBy($condition, $fetchType, $orderBy) find data using single or multiple condition. If no matching model exist, it returns null
	public static function whereBy($condition = NULL, $fetchType = 'result_array', $orderBy = NULL, $with = NULL)
	{
		$className = get_called_class();
		$obj = new $className;
		$data = getwhere($obj->table, xssClean($condition), $fetchType, $orderBy);

		if (!empty($with)) {
			if (isset($obj->with)) {
				$data = (new self)->withData($obj, $with, $data);
			}
		}

		return (!empty($data)) ? purify($data) : NULL;
	}

	// first() returns the first record found in the database. If no matching model exist, it returns null
	public static function first()
	{
		$className = get_called_class();
		$obj = new $className;
		$data = rawQuery("SELECT * FROM $obj->table ORDER BY $obj->primary_key ASC LIMIT 1", 'row_array');
		return (!empty($data)) ? purify($data) : NULL;
	}

	// last() returns the last record found in the database. If no matching model exist, it returns null
	public static function last()
	{
		$className = get_called_class();
		$obj = new $className;
		$data = rawQuery("SELECT * FROM $obj->table ORDER BY $obj->primary_key DESC LIMIT 1", 'row_array');
		return (!empty($data)) ? purify($data) : NULL;
	}

	public static function save($data = array(), $enableXss = true)
	{
		$className = get_called_class();
		$obj = new $className;

		if (isset($obj->fillable)) {

			if (isMultidimension($data)) {
				// no need to validate.
			} else {

				$id = (isset($data[$obj->primary_key])) ? $data[$obj->primary_key] : NULL;
				$dataArr = array(); // reset array

				// check if column does'nt exist
				if (isset($obj->fillable) && !empty($obj->fillable)) {
					foreach ($obj->fillable as $columnName) {
						if (array_key_exists($columnName, $data)) {
							$dataArr[$columnName] = $data[$columnName];
						}
					}
				}

				$dataArr[$obj->primary_key] = $id; // add id PK

				$data = $dataArr;
			}
		}

		return save($obj->table, $data, $enableXss);
	}

	public static function remove($id = NULL, $pkTable = NULL)
	{
		$className = get_called_class();
		$obj = new $className;
		return delete($obj->table, $id, $pkTable);
	}

	public static function updateBatch($data = NULL, $pkCustomKey = NULL)
	{
		$className = get_called_class();
		$obj = new $className;

		$pkColumn = (!empty($pkCustomKey)) ? $pkCustomKey : $obj->primary_key;

		if (isset($obj->fillable)) {

			$dataArr = array(); // reset array

			foreach ($data as $columnData) {

				$dataTemp = [];
				foreach ($obj->fillable as $columnName) {

					if (array_key_exists($columnName, $columnData)) {
						$dataTemp[$columnName] = $columnData[$columnName];
					}

					if (!empty($pkCustomKey))
						$id = (isset($columnData[$pkCustomKey])) ? $columnData[$pkCustomKey] : NULL;
					else
						$id = (isset($columnData[$obj->primary_key])) ? $columnData[$obj->primary_key] : NULL;

					$dataTemp[$obj->primary_key] = $id; // add id PK
				}

				array_push($dataArr, $dataTemp);
			}

			$data = $dataArr;
		}

		return updateBatch($obj->table, $data, $pkColumn);
	}

	// countData() count all data by condition array. If no matching model exist, it returns 0
	public static function countData($condition = NULL)
	{
		$className = get_called_class();
		$obj = new $className;
		$data = countData(xssClean($condition), $obj->table);

		return (!empty($data)) ? xssClean($data) : 0;
	}

	public static function minmax($columnName = NULL, $condition = NULL, $type = 'min')
	{
		$className = get_called_class();
		$obj = new $className;

		return minmax($obj->table, $columnName, $condition, $type);
	}

	public function withData($obj, $with, $dataArr = NULL, $callType = 'fetchRow')
	{
		$dataRelation = $objStore = array(); // reset array

		if ($callType == 'get') {
			foreach ($dataArr as $key => $data) {
				foreach ($with as $functionName) {
					if (in_array($functionName, $obj->with)) {
						$functionCall = $functionName . 'Relation';

						// check if function up is exist
						if (method_exists($obj, $functionCall)) {
							$dataRelation = $obj->$functionCall($data);
							$dataStore = [
								$functionName => [
									'data' => $dataRelation['data'],
									'objData' => $dataRelation['obj'],
								]
							];
							array_push($objStore, $dataStore);
							$dataArr[$key][$functionName] = $dataRelation['data'];
						}
					} else {

						$withArr = explode(".", $functionName);

						if (count($withArr) > 1) {
							$previousArr = $withArr[count($withArr) - 2];
							$functionReq = $withArr[count($withArr) - 1];

							foreach ($objStore as $store) {
								if (array_key_exists($previousArr, $store)) {
									$previousData = $store[$previousArr]['data'];
									$previousObj = $store[$previousArr]['objData'];
								}
							}

							if (in_array($functionReq, $previousObj->with)) {
								$functionCall = $functionReq . 'Relation';

								foreach ($previousData as $key => $data) {
									// check if function up is exist
									if (method_exists($previousObj, $functionCall)) {

										if (isAssociative($previousData)) {
											$dataRelation = $previousObj->$functionCall($previousData);
										} else {
											$dataRelation = $previousObj->$functionCall($data[$key]);
										}

										$dataStore = [
											$functionReq => [
												'data' => $dataRelation['data'],
												'objData' => $dataRelation['obj'],
											]
										];

										array_push($objStore, $dataStore);

										if (isset($dataArr[$previousArr])) {
											if (isAssociative($dataArr[$previousArr])) {
												$dataArr[$previousArr][$functionReq] = $dataRelation['data'];
											} else {

												$dataArr[$previousArr][$key][$functionReq] = $dataRelation['data'];
											}
										} else {
											if (isAssociative($dataArr)) {
												$dataArr[$functionReq] = $dataRelation['data'];
											} else {
												foreach ($dataArr as $index => $data) {
													$dataArr[$index][$previousArr][$functionReq] = $dataRelation['data'];
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		} else {
			foreach ($with as $functionName) {
				if (in_array($functionName, $obj->with)) {
					$functionCall = $functionName . 'Relation';

					// check if function up is exist
					if (method_exists($obj, $functionCall)) {
						if (!isMultidimension($dataArr)) {
							$dataRelation = $obj->$functionCall($dataArr);
							$dataStore = [
								$functionName => [
									'data' => (isset($dataRelation['data'])) ? $dataRelation['data'] : NULL,
									'objData' => (isset($dataRelation['obj'])) ? $dataRelation['obj'] : NULL,
								]
							];
							array_push($objStore, $dataStore);
							$dataArr[$functionName] = (isset($dataRelation['data'])) ? $dataRelation['data'] : NULL;
						} else {
							foreach ($dataArr as $key => $subdata) {
								$dataRelation = $obj->$functionCall($subdata);
								$dataStore = [
									$functionName => [
										'data' => (isset($dataRelation['data'])) ? $dataRelation['data'] : NULL,
										'objData' => (isset($dataRelation['obj'])) ? $dataRelation['obj'] : NULL,
									]
								];
								array_push($objStore, $dataStore);
								$dataArr[$key][$functionName] = (isset($dataRelation['data'])) ? $dataRelation['data'] : NULL;
							}
						}
					}
				} else {
					$withArr = explode(".", $functionName);

					if (count($withArr) > 1) {
						$previousArr = $withArr[count($withArr) - 2];
						$functionReq = $withArr[count($withArr) - 1];

						$previousData = $previousObj = array();

						foreach ($objStore as $store) {
							if (array_key_exists($previousArr, $store)) {
								$previousData = $store[$previousArr]['data'];
								$previousObj = $store[$previousArr]['objData'];
							}
						}

						if (!empty($previousData)) {

							if (in_array($functionReq, $previousObj->with)) {
								$functionCall = $functionReq . 'Relation';

								// check if function up is exist
								if (method_exists($previousObj, $functionCall)) {
									if (!isAssociative($dataArr)) {
										foreach ($dataArr as $key => $data) {

											if (isAssociative($data)) {
												$dataRelation = $previousObj->$functionCall($previousData);
											}

											$dataStore = [
												$functionReq => [
													'data' => $dataRelation['data'],
													'objData' => $dataRelation['obj'],
												]
											];
											array_push($objStore, $dataStore);

											if (isAssociative($dataArr[$key][$previousArr])) {
												$dataArr[$key][$previousArr][$functionReq] = $dataRelation['data'];
											} else {
												$dataArr[$previousArr][$functionReq] = $dataRelation['data'];
											}
										}
									} else {
										// OLD CODE
										foreach ($previousData as $key => $data) {
											// check if function up is exist
											if (method_exists($previousObj, $functionCall)) {

												if (isAssociative($previousData)) {
													$dataRelation = $previousObj->$functionCall($previousData);
												} else {
													$dataRelation = $previousObj->$functionCall($previousData[$key]);
												}

												$dataStore = [
													$functionReq => [
														'data' => $dataRelation['data'],
														'objData' => $dataRelation['obj'],
													]
												];
												array_push($objStore, $dataStore);

												if (isAssociative($dataArr[$previousArr])) {
													$dataArr[$previousArr][$functionReq] = $dataRelation['data'];
												} else {
													$dataArr[$previousArr][$key][$functionReq] = $dataRelation['data'];
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		return $dataArr;
	}
}
