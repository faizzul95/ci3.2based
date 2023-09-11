<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// CI3 HELPERS SECTION

if (!function_exists('ciObj')) {
	function ciObj()
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
	function model($modelName, $assignName = NULL)
	{
		if (hasData($assignName))
			return ci()->load->model($modelName, $assignName);

		return ci()->load->model($modelName);
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
	function input($fieldName = NULL, $xss = TRUE)
	{
		return ci()->input->post_get($fieldName, $xss); // return with XSS Clean
	}
}

if (!function_exists('files')) {
	function files($fieldName = NULL, $relative_path  = false)
	{
		return ci()->security->sanitize_filename(input($fieldName), $relative_path);
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

		if (isCookieRememberExists()) {
			delete_cookie(env('REMEMBER_COOKIE_NAME'));
		}

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