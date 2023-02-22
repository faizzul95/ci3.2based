<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// CanThink Solution (Fahmy)

if (!function_exists('db_name')) {
	function db_name($debug = false)
	{
		if ($debug)
			dd(DB_NAME);
		else
			return DB_NAME;
	}
}

if (!function_exists('returnData')) {
	function returnData($data = NULL, $code = 200)
	{
		logDebug($data, isSuccess($code) ? 'info' : 'error');
		http_response_code($code);
		return $data;
	}
}

if (!function_exists('insert')) {
	function insert($table = NULL, $data = NULL, $enableXss = true)
	{
		if (!$enableXss) {

			$data['created_at'] = timestamp();
			$filterData = sanitizeInput($data, $table, $enableXss);

			try {
				$ci = get_instance();
				$resultInsert = $ci->db->insert($table, $filterData);
				$resCode = ($resultInsert['status']) ? 201 : 400;

				return returnData([
					"action" => 'insert',
					"resCode" => $resCode,
					"message" =>  message($resCode, 'insert'),
					"id" => $resultInsert['lastID'],
					"data" => $filterData
				], $resCode);
			} catch (Exception $e) {
				log_message('error', "INSERT ERROR : " . $e->getMessage());
				return returnData([
					'action' => 'insert',
					'resCode' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else if (antiXss($data) === false) {

			$data['created_at'] = timestamp();
			$filterData = sanitizeInput($data, $table);

			try {
				$ci = get_instance();
				$resultInsert = $ci->db->insert($table, $filterData);
				$resCode = ($resultInsert['status']) ? 201 : 400;

				return returnData([
					"action" => 'insert',
					"resCode" => $resCode,
					"message" =>  message($resCode, 'insert'),
					"id" => $resultInsert['lastID'],
					"data" => $filterData
				], $resCode);
			} catch (Exception $e) {
				log_message('error', "INSERT ERROR : " . $e->getMessage());
				return returnData([
					'action' => 'insert',
					'resCode' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else {
			return returnData([
				'action' => 'insert',
				'resCode' => 422,
				'message' => 'Protection against <b><i> Cross-site scripting (XSS) </i></b> activated!',
				'id' => NULL,
				'data' => $data,
			], 422);
		}
	}
}

if (!function_exists('update')) {
	function update($table = NULL, $data = NULL, $pkValue = NULL, $pkTableCT = NULL, $enableXss = true)
	{
		if (!$enableXss) {
			$pkTable = (empty($pkTableCT)) ? primary_field_name($table) : $pkTableCT;

			$data['updated_at'] = timestamp();
			$filterData = sanitizeInput($data, $table, $enableXss);

			if (isset($filterData[$pkTable])) {
				unset($filterData[$pkTable]); // auto increment, no need to update or insert
			}

			try {
				$ci = get_instance();
				$resCode = ($ci->db->update($table, $filterData, [$pkTable => $pkValue])) ? 200 : 400;

				return returnData([
					"action" => 'update',
					"resCode" => $resCode,
					"message" =>  message($resCode, 'update'),
					"id" => $pkValue,
					"data" => $filterData
				], $resCode);
			} catch (Exception $e) {
				log_message('error', "UPDATE ERROR : " . $e->getMessage());
				return returnData([
					"action" => 'update',
					'resCode' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else if (antiXss($data) === false) {
			$pkTable = (empty($pkTableCT)) ? primary_field_name($table) : $pkTableCT;

			$data['updated_at'] = timestamp();
			$filterData = sanitizeInput($data, $table);

			if (isset($filterData[$pkTable])) {
				unset($filterData[$pkTable]); // auto increment, no need to update or insert
			}

			try {
				$ci = get_instance();
				$resCode = ($ci->db->update($table, $filterData, [$pkTable => $pkValue])) ? 200 : 400;

				return returnData([
					"action" => 'update',
					"resCode" => $resCode,
					"message" =>  message($resCode, 'update'),
					"id" => $pkValue,
					"data" => $filterData
				], $resCode);
			} catch (Exception $e) {
				log_message('error', "UPDATE ERROR : " . $e->getMessage());
				return returnData([
					"action" => 'update',
					'resCode' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else {
			return returnData([
				'action' => 'update',
				'resCode' => 422,
				'message' => 'Protection against <b><i> Cross-site scripting (XSS) </i></b> activated!',
				'id' => NULL,
				'data' => $data,
			], 422);
		}
	}
}

if (!function_exists('delete')) {
	function delete($table, $pkValue = NULL, $pkTableCT = NULL)
	{
		if (antiXss($pkValue) === false) {

			try {
				$ci = get_instance();

				$previous_values = NULL;

				if (!empty($pkValue)) {
					$pkTable = (empty($pkTableCT)) ? primary_field_name($table) : $pkTableCT;
					$previous_values = find($table, [$pkTable => $pkValue], 'row_array');
					$resCode = ($ci->db->delete($table, [$pkTable => $pkValue])) ? 200 : 400;
					$typeAction = 'delete';
				} else {
					$resCode = $ci->db->truncate($table) ? 200 : 400;
					$typeAction = 'truncate';
				}

				return returnData([
					'action' => $typeAction,
					"resCode" => $resCode,
					"message" =>  message($resCode, $typeAction),
					"id" => $pkValue,
					"data" => $previous_values
				], $resCode);
			} catch (Exception $e) {
				log_message('error', "REMOVE ERROR : " . $e->getMessage());
				return returnData([
					'action' => 'delete',
					'resCode' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else {
			return returnData([
				'action' => 'delete',
				'resCode' => 422,
				'message' => 'Protection against <b><i> Cross-site scripting (XSS) </i></b> activated!',
				'id' => NULL,
				'data' => [],
			], 422);
		}
	}
}

if (!function_exists('deleteWithCondition')) {
	function deleteWithCondition($table, $condition = NULL)
	{
		if (antiXss($condition) === false) {

			try {
				$ci = get_instance();
				$previous_values = findAll($table, $condition);

				foreach ($condition as $key => $value) {
					if (is_numeric($key))
						$ci->db->where($value);
					else
						$ci->db->where($key, $value);
				}

				$resCode = ($ci->db->delete($table)) ? 200 : 400;

				return returnData([
					"resCode" => $resCode,
					"message" =>  message($resCode, 'delete'),
					"id" => NULL,
					"data" => $previous_values
				], $resCode);
			} catch (Exception $e) {
				log_message('error', "REMOVE W.C ERROR : " . $e->getMessage());
				return returnData([
					'resCode' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else {
			return returnData([
				'resCode' => 422,
				'message' => 'Protection against <b><i> Cross-site scripting (XSS) </i></b> activated!',
				'id' => NULL,
				'data' => $condition,
			], 422);
		}
	}
}

if (!function_exists('save')) {
	function save($table, $data, $enableXss = true)
	{
		$dataInsert = [];
		$pkColumnName = NULL;

		// get primary key
		$pkColumnName = primary_field_name($table);

		// search if data exist using PK
		$exist = (isset($data[$pkColumnName])) ? find($table, [$pkColumnName => $data[$pkColumnName]], 'row_array') : NULL;

		$dataInsert = (isAssociative($data)) ?  $data : ((!empty($exist)) ? $data[1] : merge($data[0], $data[1]));

		if (isset($dataInsert[$pkColumnName])) {
			unset($dataInsert[$pkColumnName]); // auto increment, no need to update or insert
		}

		if (!empty($exist)) {
			$id = $exist[$pkColumnName]; // get pk from table
			return update($table, $dataInsert, $id, $pkColumnName, $enableXss);
		} else {
			return insert($table, $dataInsert, $enableXss);
		}
	}
}

if (!function_exists('updateBatch')) {
	function updateBatch($table, $data, $pkColumnName)
	{
		if (antiXss($data) === false) {
			$pkColumnName = (empty($pkColumnName)) ? primary_field_name($table) : $pkColumnName;

			$filterData = [];
			foreach ($data as $columnData) {
				$sanitize = [];
				foreach ($columnData as $columnName => $value) {
					if (isColumnExist($table, $columnName)) {
						// check if empty field
						if ($value == '' || empty($value)) {
							$sanitize[$columnName] = $value != 0 || $value != '0' ? null : $value;
						} else {
							$sanitize[$columnName] = xssClean($value);
						}
					} else {
						unset($columnName);
					}
				}

				$sanitize['updated_at'] = timestamp();
				array_push($filterData, $sanitize);
			}

			try {;
				$ci = get_instance();
				$updateResult = $ci->db->update_batch($table, $filterData, $pkColumnName);
				$resCode = $updateResult > 0 ? 200 : 400; // always return true if has data update

				return returnData([
					"action" => 'update',
					"resCode" => $resCode,
					"message" => isSuccess($resCode) ? $updateResult . ' data has been updated' : 'Please consult the system administrator',
					"totalUpdate" => $updateResult,
					"data" => $filterData
				], $resCode);
			} catch (Exception $e) {
				log_message('error', "BATCH UPDATE ERROR : " . $e->getMessage());
				return returnData([
					"action" => 'update',
					'resCode' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else {
			return returnData([
				'action' => 'update',
				'resCode' => 422,
				'message' => 'Protection against <b><i> Cross-site scripting (XSS) </i></b> activated!',
				'id' => NULL,
				'data' => [],
			], 422);
		}
	}
}

if (!function_exists('sanitizeInput')) {
	function sanitizeInput($dataArr, $table = NULL, $enableXss = true)
	{
		$sanitize = [];

		if (isAssociative($dataArr)) {
			foreach ($dataArr as $columnName => $value) {
				if (isColumnExist($table, $columnName)) {
					// check if empty field
					if ($value == '' || empty($value)) {
						$sanitize[$columnName] = $value != 0 || $value != '0' ? null : $value;
					} else {
						$sanitize[$columnName] = $enableXss ? xssClean($value) : $value;
					}
				} else {
					// remove all column field that does't exist in db
					unset($columnName);
				}
			}
		} else {
			foreach ($dataArr[0] as $columnName => $value) {
				if (isColumnExist($table, $columnName)) {
					// check if empty field
					if ($value == '' || empty($value)) {
						$sanitize[$columnName] = $value != 0 || $value != '0' ? null : $value;
					} else {
						$sanitize[$columnName] = $enableXss ? xssClean($value) : $value;
					}
				} else {
					unset($columnName);
				}
			}
		}

		return $sanitize;
	}
}

if (!function_exists('isTableExist')) {
	function isTableExist($table)
	{
		if (ci()->db->table_exists($table))
			return true;
		else
			return false;
	}
}

if (!function_exists('isColumnExist')) {
	function isColumnExist($table, $columnName)
	{
		if (ci()->db->field_exists($columnName, $table))
			return true;
		else
			return false;
	}
}

if (!function_exists('allTableColumn')) {
	function allTableColumn($table)
	{
		if (isTableExist($table))
			return ci()->db->list_fields($table);
		else
			return [];
	}
}

if (!function_exists('primary_field_name')) {
	function primary_field_name($table)
	{
		$getPKTable = rawQuery("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
		return $getPKTable[0]['Column_name'];
	}
}

if (!function_exists('hasMany')) {
	function hasMany($modelRef, $columnRef, $condition, $option = NULL, $with = NULL)
	{
		$dataArr = $obj = $tableRef = '';

		if (!empty($condition)) {
			$fileName = APPPATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $modelRef . '.php';
			if (file_exists($fileName)) {
				require_once $fileName;
				$className = getClassNameFromFile($fileName);
				$obj = new $className;
				$tableRef = $obj->table;
				$tableRefPK = $obj->id;

				// check table
				if (isTableExist($tableRef)) {

					$whereCon = NULL;
					if (!empty($option)) {
						foreach ($option as $key => $value) {
							$whereCon .= "AND {$key}='$value'";
						}
					}

					$dataArr = rawQuery("SELECT * FROM $tableRef WHERE {$columnRef}='$condition' $whereCon");

					if (!empty($with)) {
						$dataRelation = $objStore = array(); // reset array
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
							}
						}
					}
				}
			}
		}

		return [
			'obj' => $obj,
			'table' => $tableRef,
			'column' => $columnRef,
			'id' => $condition,
			'data' => (!empty($dataArr)) ? xssClean($dataArr) : NULL,
		];
	}
}

if (!function_exists('hasOne')) {
	function hasOne($modelRef, $columnRef, $condition, $option = NULL, $with = NULL)
	{
		$dataArr = $obj = $tableRef = '';

		if (!empty($condition)) {
			$fileName = APPPATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $modelRef . '.php';
			if (file_exists($fileName)) {
				require_once $fileName;
				$className = getClassNameFromFile($fileName);
				$obj = new $className;

				$tableRef = $obj->table;
				$tableRefPK = $obj->id;

				// check table
				if (isTableExist($tableRef)) {

					$whereCon = NULL;
					if (!empty($option)) {
						foreach ($option as $key => $value) {
							$whereCon .= "AND {$key}='$value'";
						}
					}

					$dataArr = rawQuery("SELECT * FROM $tableRef WHERE {$columnRef}='$condition' $whereCon LIMIT 1", 'row_array');

					if (!empty($with)) {
						$dataRelation = $objStore = array(); // reset array
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
							}
						}
					}
				}
			}
		}

		return [
			'obj' => $obj,
			'table' => $tableRef,
			'column' => $columnRef,
			'id' => $condition,
			'data' => (!empty($dataArr)) ? xssClean($dataArr[0]) : NULL,
		];
	}
}

if (!function_exists('isUpdateData')) {
	function isUpdateData($typeAction)
	{
		$action = (isArray($typeAction)) ? $typeAction['action'] : $typeAction;
		if ($action == 'update') {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('isInsertData')) {
	function isInsertData($typeAction)
	{
		$action = (isArray($typeAction)) ? $typeAction['action'] : $typeAction;
		if ($action == 'insert') {
			return true;
		} else {
			return false;
		}
	}
}

function getClassNameFromFile($filePathName)
{
	$php_code = file_get_contents($filePathName);

	$classes = array();
	$tokens = token_get_all($php_code);
	$count = count($tokens);
	for ($i = 2; $i < $count; $i++) {
		if (
			$tokens[$i - 2][0] == T_CLASS
			&& $tokens[$i - 1][0] == T_WHITESPACE
			&& $tokens[$i][0] == T_STRING
		) {

			$class_name = $tokens[$i][1];
			$classes[] = $class_name;
		}
	}

	return $classes[0];
}
