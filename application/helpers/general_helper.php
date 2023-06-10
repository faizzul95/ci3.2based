<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// DATE & TIME HELPERS SECTION

if (!function_exists('currentDate')) {
	function currentDate($format = 'Y-m-d')
	{
		date_default_timezone_set('Asia/Kuala_Lumpur');
		return date($format);
	}
}

if (!function_exists('currentTime')) {
	function currentTime($format = 'H:i:s')
	{
		date_default_timezone_set('Asia/Kuala_Lumpur');
		return date($format);
	}
}

if (!function_exists('formatDate')) {
	function formatDate($date, $format = "d.m.Y")
	{
		return date($format, strtotime($date));
	}
}

if (!function_exists('timestamp')) {
	function timestamp($format = 'Y-m-d H:i:s')
	{
		date_default_timezone_set('Asia/Kuala_Lumpur');
		return date($format);
	}
}

if (!function_exists('dateDiff')) {
	function dateDiff($d1, $d2)
	{
		return round(abs(strtotime($d1) - strtotime($d2)) / 86400);
	}
}

if (!function_exists('timeDiff')) {
	function timeDiff($t1, $t2)
	{
		return round(abs(strtotime($t1) - strtotime($t2)) / 60);
	}
}

if (!function_exists('extendDate')) {
	function extendDate($dateFrom = 'Y-m-d H:i:s', $totalToAdd = '1 minutes', $extendAnotherDate = NULL, $format = 'Y-m-d H:i:s')
	{
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$addExtendDate = date($format, strtotime($dateFrom . ' +' . $totalToAdd));
		return hasData($extendAnotherDate) ? date($format, strtotime($addExtendDate . ' +' . $extendAnotherDate)) : $addExtendDate;
	}
}

if (!function_exists('reduceDate')) {
	function reduceDate($dateFrom = 'Y-m-d H:i:s', $totalToReduce = '1 minutes', $reduceAnotherDate = NULL, $format = 'Y-m-d H:i:s')
	{
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$addExtendDate = date($format, strtotime($dateFrom . ' +' . $totalToReduce));
		return hasData($reduceAnotherDate) ? date($format, strtotime($addExtendDate . ' +' . $reduceAnotherDate)) : $addExtendDate;
	}
}

// CURRENCY & MONEY HELPERS SECTION

if (!function_exists('currency_format')) {
	function currency_format($amount)
	{
		return number_format((float)$amount, 2, '.', ',');
	}
}

// ENCODE & DECODE HELPERS SECTION

if (!function_exists('encode_base64')) {
	function encode_base64($sData = NULL)
	{
		if (!empty($sData)) {
			$sBase64 = base64_encode($sData);
			return strtr($sBase64, '+/', '-_');
		} else {
			return '';
		}
	}
}

if (!function_exists('decode_base64')) {
	function decode_base64($sData = NULL)
	{
		if (!empty($sData)) {
			$sBase64 = strtr($sData, '-_', '+/');
			return base64_decode($sBase64);
		} else {
			return '';
		}
	}
}

if (!function_exists('encodeID')) {
	function encodeID($id = NULL, $count = 25)
	{
		$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$uniqueURL = substr(str_shuffle($permitted_chars), 0, $count) . '' . $id . '' . substr(str_shuffle($permitted_chars), 0, $count);
		return encode_base64($uniqueURL);
	}
}

if (!function_exists('decodeID')) {
	function decodeID($id = NULL, $count = 25)
	{
		$id = decode_base64($id);
		return substr($id, $count, -$count);
	}
}

// GENERAL HELPERS SECTION

if (!function_exists('baseURL')) {
	function baseURL()
	{
		// $host = array_key_exists("HTTP_HOST", $_SERVER) ? $_SERVER['HTTP_HOST'] : '';
		// $baseUrl = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		// $baseUrl .=  '://' . $host;
		// $baseUrl .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
		// return $baseUrl;

		return base_url();
	}
}

if (!function_exists('asset')) {
	function asset($param, $public = TRUE)
	{
		$isPublic = $public ? 'public/' : '';
		return baseURL() . $isPublic . $param;
	}
}

if (!function_exists('redirect')) {
	function redirect($path, $permanent = false)
	{
		header('Location: ' . url($path), true, $permanent ? 301 : 302);
		exit();
	}
}

if (!function_exists('url')) {
	function url($param)
	{
		$param = htmlspecialchars($param, ENT_NOQUOTES, 'UTF-8');
		return baseURL() . filter_var($param, FILTER_SANITIZE_URL);
	}
}

if (!function_exists('isAjax')) {
	function isAjax()
	{
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('message')) {
	function message($code, $text = 'save')
	{
		if (isSuccess($code)) {
			return ucfirst($text) . ' successfully';
		} else {
			return 'Please consult the system administrator';
		}
	}
}

if (!function_exists('isSuccess')) {
	function isSuccess($response = 200)
	{
		$successStatus = [200, 201, 302];

		// check if array
		if (is_array($response))
			$response = array_key_exists('resCode', $response) ? $response['resCode'] : (array_key_exists('code', $response) ? $response['code'] : 200);

		$code = (is_string($response)) ? (int)$response : $response;

		if (in_array($code, $successStatus)) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('isError')) {
	function isError($response = 400)
	{
		$errorStatus = [400, 403, 404, 422, 500];

		// check if array
		if (is_array($response))
			$response = array_key_exists('resCode', $response) ? $response['resCode'] : (array_key_exists('code', $response) ? $response['code'] : 400);

		$code = (is_string($response)) ? (int)$response : $response;

		if (in_array($code, $errorStatus)) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('hasData')) {
	function hasData($data = NULL, $arrKey = NULL)
	{
		if (isset($data)) {
			if (($data !== '' || $data !== NULL || $data !== 'null') && (!empty($data) && !is_null($data))) {
				if (!empty($arrKey) && array_key_exists($arrKey, $data))
					return !empty($data[$arrKey]) ? true : false;
				else if (empty($arrKey))
					return true;
				else
					return false;
			}
		}

		return false;
	}
}

if (!function_exists('fileExist')) {
	function fileExist($path = NULL)
	{
		if (hasData($path)) {
			return file_exists($path) ? true : false;
		} else {
			return false;
		}
	}
}

if (!function_exists('json')) {
	function json($data = NULL, $code = 200)
	{
		if (isArray($data) && array_key_exists("resCode", $data)) {
			$code = $data['resCode'];
		}

		http_response_code($code);
		header('Content-Type: application/json');
		echo json_encode($data, JSON_PRETTY_PRINT);
		exit;
	}
}

if (!function_exists('validationErrMessage')) {
	function validationErrMessage()
	{
		http_response_code(422);
		header('Content-Type: application/json');
		echo json_encode([
			'resCode' => 422,
			'message' => validation_errors(),
			'id' => NULL,
			'data' => [],
		], JSON_PRETTY_PRINT);
		exit;
	}
}

if (!function_exists('isMobileDevice')) {
	function isMobileDevice()
	{
		if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			return preg_match("/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|boost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
		};

		return false;
	}
}

if (!function_exists('Logs')) {
	function Logs($type = 'view', $message = NULL, $model_name = NULL, $function_name = NULL)
	{
		Crud_Logs::$type($message, $model_name, $function_name);
	}
}

if (!function_exists('genRunningNo')) {
	function genRunningNo($currentNo, $prefix = NULL, $suffix = NULL, $separator = NULL, $leadingZero = 5)
	{
		$nextNo = $currentNo + 1;

		$pref = empty($separator) ? $prefix : $prefix . $separator;
		$suf = !empty($suffix) ? (empty($separator) ? $suffix : $separator . $suffix) : NULL;

		return [
			'code' => $pref . str_pad($nextNo, $leadingZero, 0, STR_PAD_LEFT) . $suf,
			'next' => $nextNo
		];
	}
}

// PAGE ERROR (NODATA) HELPER

if (!function_exists('nodata')) {
	function nodata($showText = true, $filesName = '5.png')
	{
		echo "<div id='nodata' class='col-lg-12 mb-4 mt-2'>
          <center>
            <img src='" . url('public/custom/images/nodata/' . $filesName) . "' class='img-fluid mb-3' width='38%'>
            <h4 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;margin-bottom:15px'> 
             <strong> NO INFORMATION FOUND </strong>
            </h4>";
		if ($showText) {
			echo "<h6 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;font-size: 13px;'> 
                Here are some action suggestions for you to try :- 
            </h6>";
		}
		echo "</center>";
		if ($showText) {
			echo "<div class='row d-flex justify-content-center w-100'>
            <div class='col-lg m-1 text-left' style='max-width: 350px !important;letter-spacing :1px; font-family: Quicksand, sans-serif !important;font-size: 12px;'>
              1. Try the registrar function (if any).<br>
              2. Change your word or search selection.<br>
              3. Contact the system support immediately.<br>
            </div>
          </div>";
		}
		echo "</div>";
	}
}

if (!function_exists('nodataAccess')) {
	function nodataAccess($filesName = '403.png')
	{
		echo "<div id='nodata' class='col-lg-12 mb-4 mt-2'>
          <center>
            <img src='" . url('public/custom/images/nodata/' . $filesName) . "' class='img-fluid mb-2' width='30%'>
            <h3 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;margin-bottom:15px'> 
             <strong> NO ACCESS TO THIS INFORMATION </strong>
            </h3>";
		echo "</center>";
		echo "</div>";
	}
}

if (!function_exists('genCode')) {
	function genCode($name, $codeList = array(), $codeType = 'S', $codeLength = 4, $numLength = 4, $counter = 1)
	{
		$code = '';

		$nameArr = explode(' ', strtoupper($name));
		$wordIdx = array();
		$word = 0;
		while ($codeLength != strlen($code)) {
			if ($word >= count($nameArr)) {
				$word = 0;
			}
			if (!isset($wordIdx[$word])) {
				$wordIdx[$word] = 0;
			}
			if ($wordIdx[$word] >= strlen($nameArr[$word])) {
				$wordIdx[$word] = 0;
			}

			$code .= $nameArr[$word][$wordIdx[$word]];
			$wordIdx[$word]++;
			$word++;
		}

		$found = false;
		while (!$found) {
			$tempcode = $codeType . $code . str_pad($counter, $numLength, '0', STR_PAD_LEFT);

			if (!in_array($tempcode, $codeList)) {
				$code = $tempcode;
				$found = true;
			}
			$counter++;
		}

		return $code;
	}
}

if (!function_exists('defaultImage')) {
	function defaultImage($type = 'user')
	{
		$list = [
			'user' => 'upload/default/user.png',
			'company_logo' => 'upload/default/no-img.png',
		];

		return array_key_exists($type, $list) ? asset($list[$type]) : asset('upload/default/no-img.png');
	}
}

if (!function_exists('fileImage')) {
	function fileImage($dataImage = NULL, $typeDefault = 'user')
	{
		if (hasData($dataImage)) {

			$type = $dataImage['files_type'];
			$path = $dataImage['files_path'];
			$folder = $dataImage['files_folder'];
			$compress = $dataImage['files_compression'];

			// check if files type is image
			if ($type == 'image') {
				$filename_without_extension = pathinfo($path, PATHINFO_FILENAME);
				$file_extension = pathinfo($path, PATHINFO_EXTENSION);

				$compressPath = [
					1 => $path,
					2 => $folder . '/' . $filename_without_extension . '_compress.' . $file_extension,
					3 => $folder . '/' . $filename_without_extension . '_thumbnail.' . $file_extension,
				];

				$imagePath = $compressPath[$compress];

				// check if file is exist
				if (fileExist($imagePath)) {
					return asset($imagePath, false);
				} else {
					// return default image if not exist
					return defaultImage($typeDefault);
				}
			} else {
				return defaultImage($typeDefault);
			}
		} else {
			return defaultImage($typeDefault);
		}
	}
}

if (!function_exists('deleteFolder')) {
	function deleteFolder($folder, $excludedFiles = [])
	{
		$excFile = array_merge(['index.html', '.htaccess'], $excludedFiles);

		if (is_dir($folder)) {
			$files = scandir($folder);
			foreach ($files as $file) {
				if ($file != '.' && $file != '..' && !in_array($file, $excFile)) {
					$filePath = $folder . DIRECTORY_SEPARATOR . $file;
					if (is_dir($filePath)) {
						deleteFolder($filePath, $excFile);
					} else {
						unlink($filePath);
					}
				}
			}

			// check if folder is empty then remove
			if (count(glob("$folder/*")) === 0) {
				rmdir($folder);
			}
		}
	}
}

if (!function_exists('truncateText')) {
	function truncateText($string, $length, $suffix = '...')
	{
		// If the string is shorter than or equal to the maximum length, return the string as is
		if (strlen($string) <= $length) {
			return $string;
		}

		// Truncate the string to the specified length
		$truncated = substr($string, 0, $length);

		// If the truncated string ends with a space, remove the space
		if (substr($truncated, -1) == ' ') {
			$truncated = substr($truncated, 0, -1);
		}

		// Append the suffix to the truncated string
		$truncated .= $suffix;

		return $truncated;
	}
}

if (!function_exists('mix')) {
	function mix($path = NULL, $public = TRUE)
	{
		$isPublic = $public ? 'public/' : '';

		$extension = pathinfo($path, PATHINFO_EXTENSION);
		$directory = $isPublic . dirname($path);
		$files = scandir($directory);
		$last_updated_file = '';
		$last_updated_timestamp = 0;

		foreach ($files as $file) {
			if ($file !== "." && $file !== "..") {
				$file_path = $directory . "/" . $file;
				if (is_file($file_path) && pathinfo($file_path, PATHINFO_EXTENSION) === $extension) {
					$timestamp = filemtime($file_path);
					if ($timestamp > $last_updated_timestamp) {
						$last_updated_file = $file;
						$last_updated_timestamp = $timestamp;
					}
				}
			}
		}

		return asset(dirname($path) . '/' . $last_updated_file, $public);
	}
}

if (!function_exists('app')) {
	function app($namespace)
	{
		return new class($namespace)
		{
			private $namespace;

			public function __construct($namespace)
			{
				$this->namespace = $namespace;
			}

			public function __call($method, $args)
			{
				$class = $this->namespace;
				$obj = new $class();

				try {
					if (method_exists($obj, $method)) {
						return call_user_func_array(array($obj, $method), $args);
					} else {
						throw new Exception("Method $method does not exist");
					}
				} catch (Exception $e) {
					// handle the error
					return $e->getMessage();
				}
			}
		};
	}
}
