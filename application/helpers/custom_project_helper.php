<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// DATE & TIME HELPERS SECTION

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

// ENCODE & DECODE HELPERS SECTION

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

if (!function_exists('defaultImage')) {
	function defaultImage($type = 'user')
	{
		$list = [
			'user' => 'upload/default/user.png',
			'system_logo' => 'upload/default/no-img.png',
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