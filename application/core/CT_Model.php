<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CT_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
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
}
