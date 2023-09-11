<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Return data and set HTTP response code.
 *
 * This function logs the provided data and sets the HTTP response code.
 *
 * @param mixed $data The data to return.
 * @param int   $code The HTTP response code (default is 200).
 *
 * @return mixed The provided data.
 */
if (!function_exists('returnData')) {
	function returnData($data = null, $code = 200)
	{
		// Set the HTTP response code.
		http_response_code($code);

		// Return the provided data.
		return $data;
	}
}

/**
 * Get the database name.
 *
 * This function retrieves the database name defined in the DB_NAME constant.
 * If the $debug parameter is set to true, it will use the ddd() function for debugging.
 *
 * @param bool $debug Set to true to enable debugging output using ddd()
 * @return string|null The database name if not in debug mode, null if in debug mode
 */
if (!function_exists('db_name')) {
	function db_name($debug = false)
	{
		// If debugging is enabled, output the database name 
		if ($debug) {
			ddd(DB_NAME);
		}

		// If not in debug mode, return the database name
		return DB_NAME;
	}
}


if (!function_exists('insert')) {
	function insert($table = NULL, $data = NULL, $enableXss = true)
	{
		if (!$enableXss) {

			$data['created_at'] = timestamp();
			$filterData = sanitizeInput($data, $table, $enableXss);

			try {
				$ci = ci();
				$isAuditEnable = $ci->config->item('audit_enable'); // get config audit trail
				$isTrackInsertEnable = $ci->config->item('track_insert'); // get config track insert

				$resultInsert = $ci->db->insert($table, $filterData);
				$code = ($resultInsert['status']) ? 201 : 400;

				return returnData([
					"action" => 'insert',
					"code" => $code,
					"message" =>  message($code, 'insert'),
					"id" => $isAuditEnable && $isTrackInsertEnable ? $resultInsert['lastID'] : $ci->db->insert_id(),
					"data" => $filterData
				], $code);
			} catch (Exception $e) {
				log_message('error', "INSERT ERROR : " . $e->getMessage());
				return returnData([
					'action' => 'insert',
					'code' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else if (antiXss($data) === false) {

			$data['created_at'] = timestamp();
			$filterData = sanitizeInput($data, $table);

			try {
				$ci = ci();
				$isAuditEnable = $ci->config->item('audit_enable'); // get config audit trail
				$isTrackInsertEnable = $ci->config->item('track_insert'); // get config track insert

				$resultInsert = $ci->db->insert($table, $filterData);
				$code = ($resultInsert['status']) ? 201 : 400;

				return returnData([
					"action" => 'insert',
					"code" => $code,
					"message" =>  message($code, 'insert'),
					"id" => $isAuditEnable && $isTrackInsertEnable ? $resultInsert['lastID'] : $ci->db->insert_id(),
					"data" => $filterData
				], $code);
			} catch (Exception $e) {
				log_message('error', "INSERT ERROR : " . $e->getMessage());
				return returnData([
					'action' => 'insert',
					'code' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else {
			return returnData([
				'action' => 'insert',
				'code' => 422,
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
				$ci = ci();
				$code = ($ci->db->update($table, $filterData, [$pkTable => $pkValue])) ? 200 : 400;

				return returnData([
					"action" => 'update',
					"code" => $code,
					"message" =>  message($code, 'update'),
					"id" => $pkValue,
					"data" => $filterData
				], $code);
			} catch (Exception $e) {
				log_message('error', "UPDATE ERROR : " . $e->getMessage());
				return returnData([
					"action" => 'update',
					'code' => 422,
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
				$ci = ci();
				$code = ($ci->db->update($table, $filterData, [$pkTable => $pkValue])) ? 200 : 400;

				return returnData([
					"action" => 'update',
					"code" => $code,
					"message" =>  message($code, 'update'),
					"id" => $pkValue,
					"data" => $filterData
				], $code);
			} catch (Exception $e) {
				log_message('error', "UPDATE ERROR : " . $e->getMessage());
				return returnData([
					"action" => 'update',
					'code' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else {
			return returnData([
				'action' => 'update',
				'code' => 422,
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
				$ci = ci();

				$previous_values = NULL;

				if (!empty($pkValue)) {
					$pkTable = (empty($pkTableCT)) ? primary_field_name($table) : $pkTableCT;
					$previous_values = find($table, [$pkTable => $pkValue], 'row_array');
					$code = ($ci->db->delete($table, [$pkTable => $pkValue])) ? 200 : 400;
					$typeAction = 'delete';
				} else {
					$code = $ci->db->truncate($table) ? 200 : 400;
					$typeAction = 'truncate';
				}

				return returnData([
					'action' => $typeAction,
					"code" => $code,
					"message" =>  message($code, $typeAction),
					"id" => $pkValue,
					"data" => $previous_values
				], $code);
			} catch (Exception $e) {
				log_message('error', "REMOVE ERROR : " . $e->getMessage());
				return returnData([
					'action' => 'delete',
					'code' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else {
			return returnData([
				'action' => 'delete',
				'code' => 422,
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
				$ci = ci();
				$previous_values = findAll($table, $condition);

				foreach ($condition as $key => $value) {
					if (is_numeric($key))
						$ci->db->where($value);
					else
						$ci->db->where($key, $value);
				}

				$code = ($ci->db->delete($table)) ? 200 : 400;

				return returnData([
					"code" => $code,
					"message" =>  message($code, 'delete'),
					"id" => NULL,
					"data" => $previous_values
				], $code);
			} catch (Exception $e) {
				log_message('error', "REMOVE W.C ERROR : " . $e->getMessage());
				return returnData([
					'code' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else {
			return returnData([
				'code' => 422,
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

		$dataInsert = (isAssociative($data)) ?  $data : ((!empty($exist)) ? $data[1] : array_merge($data[0], $data[1]));

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
				$ci = ci();
				$updateResult = $ci->db->update_batch($table, $filterData, $pkColumnName);
				$code = $updateResult > 0 ? 200 : 400; // always return true if has data update

				return returnData([
					"action" => 'update',
					"code" => $code,
					"message" => isSuccess($code) ? $updateResult . ' data has been updated' : 'Please consult the system administrator',
					"totalUpdate" => $updateResult,
					"data" => $filterData
				], $code);
			} catch (Exception $e) {
				log_message('error', "BATCH UPDATE ERROR : " . $e->getMessage());
				return returnData([
					"action" => 'update',
					'code' => 422,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				], 422);
			}
		} else {
			return returnData([
				'action' => 'update',
				'code' => 422,
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

if (!function_exists('createWhereCondition')) {
	function createWhereCondition($condition, $conditional = 'AND')
	{
		$conditionString = [];
		foreach ($condition as $field => $value) {
			if (!empty($value) && $value !== '')
				$conditionString[] = "$field='$value'";
		}

		return !empty($conditionString) ? implode(' ' . $conditional . ' ', $conditionString) : '';
	}
}

if (!function_exists('getClassNameFromFile')) {
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
}
