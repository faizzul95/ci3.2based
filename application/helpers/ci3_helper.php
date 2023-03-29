<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// CI3 HELPERS SECTION

if (!function_exists('ci')) {
	function ci()
	{
		return get_instance();
	}
}

if (!function_exists('view')) {
	function view($page, $data = NULL, $blade = true)
	{
		$fileName = $blade ? $page . '.blade.php' : $page . '.php';

		if (file_exists(APPPATH . 'views' . DIRECTORY_SEPARATOR . $fileName)) {
			return ci()->load->view($fileName, $data);
		} else {
			error('404');
		}
	}
}

if (!function_exists('model')) {
	function model($modelName, $assignName)
	{
		return ci()->load->model($modelName, $assignName);
	}
}

if (!function_exists('library')) {
	function library($libName)
	{
		return ci()->load->library($libName);
	}
}

if (!function_exists('helper')) {
	function helper($helperName)
	{
		return ci()->load->helper($helperName);
	}
}

if (!function_exists('error')) {
	function error($code = NULL, $data = NULL)
	{
		// ci()->load->view('errors/custom/error_' . $code, $data);
		if (empty($data))
			$data = ['title' => $code, 'message' => '', 'image' => asset('custom/images/nodata/404.png')];

		ci()->load->view('errors/custom/error_general', $data);
	}
}

// CI3 SECURITY HELPERS SECTION

if (!function_exists('input')) {
	function input($fieldName, $typeInput = 'post')
	{
		return ci()->input->$typeInput($fieldName, TRUE); // return with XSS Clean
	}
}

if (!function_exists('xssClean')) {
	function xssClean($data)
	{
		return ci()->security->xss_clean($data);
	}
}

// CI3 URL HELPERS SECTION

if (!function_exists('segment')) {
	function segment($segmentNo = 1)
	{
		return ci()->uri->segment($segmentNo);
	}
}

// Ci3 SESSION HELPERS SECTION

if (!function_exists('setSession')) {
	function setSession($param = NULL)
	{
		library('session');
		return ci()->session->set_userdata($param);
	}
}

if (!function_exists('getSession')) {
	function getSession($param = NULL)
	{
		library('session');
		return ci()->session->userdata($param);
	}
}

if (!function_exists('getAllSession')) {
	function getAllSession()
	{
		library('session');
		$allSession = ci()->session->userdata();
		unset($allSession['__ci_last_regenerate']);
		unset($allSession['PHPDEBUGBAR_STACK_DATA']);
		return $allSession;
	}
}

if (!function_exists('destroySession')) {
	function destroySession($redirect = TRUE, $redirectUrl = 'auth')
	{
		library('session');
		ci()->session->sess_destroy();

		if ($redirect) {
			redirect($redirectUrl);
		}
	}
}

if (!function_exists('hasSession')) {
	function hasSession($param = NULL)
	{
		$getSession = !empty($param) ? getSession($param) : NULL;
		return !empty($getSession) ? true : false;
	}
}

// CI3 DATABASE HELPERS SECTION

if (!function_exists('escape')) {
	function escape($params =  NULL)
	{
		if (!empty($params))
			return is_numeric($params) || isArray($params) ? $params : ci()->db->escape($params);
		else
			return $params;
	}
}

if (!function_exists('rawQuery')) {
	function rawQuery($statement = NULL, $dataArr = NULL, $fetchType = 'result_array')
	{
		if (!empty($statement))
			return empty($dataArr) ? ci()->db->query($statement)->$fetchType() : ci()->db->query($statement, $dataArr)->$fetchType();
		else
			return NULL;
	}
}

if (!function_exists('find')) {
	function find($tableName =  NULL, $condition = NULL, $fetchType = 'result_array')
	{
		if (!empty($tableName))
			return ci()->db->get_where($tableName, $condition, 1)->$fetchType();
	}
}

if (!function_exists('findAll')) {
	function findAll($tableName =  NULL, $condition = NULL, $orderBy = NULL, $limit = NULL)
	{
		$ci = ci();
		if (!empty($condition)) {
			foreach ($condition as $key => $value) {
				if (is_numeric($key))
					$ci->db->where($value);
				else
					$ci->db->where($key, $value);
			}
		}

		return $ci->db->order_by($orderBy)->get($tableName, $limit)->result_array();
	}
}

if (!function_exists('getwhere')) {
	function getwhere($tableName =  NULL, $condition = NULL, $fetchType = 'result_array', $orderBy = NULL)
	{
		if (!empty($tableName))
			return ci()->db->order_by($orderBy)->get_where($tableName, $condition)->$fetchType();
	}
}

if (!function_exists('minmax')) {
	function minmax($tableName =  NULL, $columnName = NULL, $condition = NULL, $type = 'min')
	{
		$ci = ci();

		if ($type == 'min') {
			$ci->db->select_min($columnName);
		} else {
			$ci->db->select_max($columnName);
		}

		if (!empty($tableName))
			return $ci->db->get_where($tableName, $condition)->row_array();
	}
}

if (!function_exists('countData')) {
	function countData($condition =  NULL, $dbName = NULL)
	{
		if (!empty($dbName))
			return ci()->db->where($condition)->from($dbName)->count_all_results();
	}
}

if (!function_exists('backupDb')) {
	function backupDb()
	{
		$CI = get_instance();
		$CI->load->helper('file', 'text', 'form', 'string', 'download');
		$CI->load->dbutil();
		library('email');
		library('zip');

		date_default_timezone_set('Asia/Kuala_Lumpur');
		$file_name2 = '_db';
		$file_name1 = date("d_m_Y_h_i_s_A");

		$db_backup_path = 'public' . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;

		if (!file_exists($db_backup_path)) {
			mkdir($db_backup_path, 0755, true);
		}

		$day_bkp = 1; // days of backup interval; 0 = at each login o visit 
		$n_db = 10; // max number of files backup to store 

		$date_ref = date("Y-m-d H:i:s", strtotime("-" . $day_bkp . " day"));
		$CI->db->where('created_at >', $date_ref);
		$CI->db->order_by('created_at', 'DESC');
		$CI->db->limit(1);
		$row = $CI->db->get('system_backup_db')->row();

		if (!($row)) {

			$file_name = $file_name1 . $file_name2 . '.zip';
			$prefs = array(
				'ignore' => array('system_backup_db'), // List of tables to omit from the backup
				'format' => 'zip', // gzip, zip, txt
				'filename' => $file_name, // File name - NEEDED ONLY WITH ZIP FILES
				'add_drop' => TRUE, // Whether to add DROP TABLE statements to backup file
				'add_insert' => TRUE, // Whether to add INSERT data to backup file
				'newline' => "\n" // Newline character used in backup file
			);

			//Backup your entire database 
			$backup = $CI->dbutil->backup($prefs);
			$file = $db_backup_path . $file_name;

			try {
				if (write_file($file, $backup)) {

					$data = array(
						'backup_name' => $file_name,
						'backup_location' => $db_backup_path,
						'created_at' => timestamp()
					);

					insert('system_backup_db', $data);

					$send_to_mail = FALSE; //enable sending by email . If TRUE remeber to set email parameter

					// send backup via email to administrator 
					if ($send_to_mail) {
						// backupDbMailer($file);
					}
					// end send email

					// delete file and row of backup table
					$n_row = $CI->db->count_all('system_backup_db');

					if ($n_row > $n_db) {
						$CI->db->limit($n_row - $n_db);
						$CI->db->order_by('created_at', 'ASC');
						$todelete = $CI->db->get('system_backup_db')->result();

						foreach ($todelete as $to_delete) {

							//delete row from db table
							$CI->db->where('backup_id', $to_delete->backup_id);
							$CI->db->delete('system_backup_db');

							// delete file from backup directory
							$file_del = $db_backup_path . $to_delete->backup_name;
							if (file_exists($file_del)) {
								unlink($file_del);
							}
						} // end foreach delete
					} // end if

					return [
						'resCode' => 200,
						'message' => 'Database backup ',
						'id' => NULL,
						'data' => $data,
					];
				} else {
					return [
						'resCode' => 400,
						'message' => 'Database backup unsuccessfully',
						'id' => NULL,
						'data' => [],
					];
				}
			} catch (Exception $e) {
				return [
					'resCode' => 400,
					'message' => 'Something when wrong.',
					'id' => NULL,
					'data' => $e->getMessage(),
				];
			}
		} else {
			return [
				'resCode' => 400,
				'message' => 'Database backup unsuccessfully',
				'id' => NULL,
				'data' => [],
			];
		}
	}
}
