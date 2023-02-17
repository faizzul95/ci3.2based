<?php

use eftec\bladeone\BladeOne; // reff : https://github.com/EFTEC/BladeOne
use voku\helper\AntiXSS; // reff : https://github.com/voku/anti-xss

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\CodeigniterAdapter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use Dompdf\Dompdf;
// use Dompdf\Options;

use Ramsey\Uuid\Uuid;

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// SECURITY PLUGIN 

if (!function_exists('purify')) {
	function purify($post)
	{
		$antiXss = new AntiXSS();
		$antiXss->removeEvilAttributes(array('style')); // allow style-attributes
		return $antiXss->xss_clean($post);
	}
}

if (!function_exists('antiXss')) {
	function antiXss($data)
	{
		$antiXss = new AntiXSS();
		$antiXss->removeEvilAttributes(array('style')); // allow style-attributes

		$xssFound = false;
		if (isArray($data)) {
			foreach ($data as $post) {
				$antiXss->xss_clean($post);
				if ($antiXss->isXssFound()) {
					$xssFound = true;
				}
			}
		} else {
			$antiXss->xss_clean($data);
			if ($antiXss->isXssFound()) {
				$xssFound = true;
			}
		}

		return $xssFound;
	}
}

if (!function_exists('recaptchav2')) {
	function recaptchav2()
	{
		if (filter_var(env('RECAPTCHA_ENABLE'), FILTER_VALIDATE_BOOLEAN)) {
			library('recaptcha');
			return ci()->recaptcha->is_valid();
		} else {
			return ['success' => TRUE, 'error_message' => 'reCAPTCHA is currently disabled'];
		}
	}
}

// IMPORT EXCEL PLUGIN

if (!function_exists('readExcel')) {
	function readExcel($files, $filesPath, $maxAllowSize = 8388608)
	{
		$name = $files["name"];
		$tmp_name = $files["tmp_name"];
		$error = $files["error"];
		$size = $files["size"];
		$type = $files["type"];

		$allowedFileType = [
			'application/vnd.ms-excel',
			'text/xls',
			'text/xlsx',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		];

		// 1st : check files type, only excel file are accepted
		if (in_array($type, $allowedFileType)) {

			// 2nd : check file size
			if ($size < $maxAllowSize) {
				if (file_exists($filesPath)) {

					/**  Identify the type of $inputFileName  **/
					$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filesPath);
					/**  Create a new Reader of the type that has been identified  **/
					$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
					/**  Load $inputFileName to a Spreadsheet Object  **/
					$spreadsheet = $reader->load($filesPath);
					/**  Convert Spreadsheet Object to an Array for ease of use  **/
					$spreadSheetAry = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

					return ['code' => 201, 'data' => $spreadSheetAry, 'count' => count($spreadSheetAry)];
				} else {
					returnData(['code' => 400, 'message' => 'The files upload was not found.'], 422);
				}
			} else {
				returnData(['code' => 400, 'message' => 'The size is not supported : ' . $size . ' bytes'], 422);
			}
		} else {
			returnData(['code' => 400, 'message' => 'The file type is not supported : ' . $type], 422);
		}
	}
}

// EXPORT PDF PLUGIN

function generate_dompdf($dataToPrint, $option = NULL)
{
	$author = empty($option) ? "CANTHINK SOLUTION" : (isset($option['author']) ? $option['author'] : NULL);
	$title = empty($option) ? "REPORT PDF" : (isset($option['title']) ? $option['title'] : "REPORT PDF");
	$filename = empty($option) ? "report" : (isset($option['filename']) ? $option['filename'] : "report");
	$paper = empty($option) ? "A4" : (isset($option['paper']) ? $option['paper'] : "A4");
	$orientation = empty($option) ? "portrait" : (isset($option['orientation']) ? $option['orientation'] : "portrait");
	$download = empty($option) ? TRUE : (isset($option['download']) ? $option['download'] : TRUE);

	ob_end_clean(); // reset previous buffer
	ini_set('display_errors', '1');
	ini_set('memory_limit', '2048M');
	ini_set('max_execution_time', 0);

	ob_start();

	// instantiate and use the dompdf class
	$dompdf = new Dompdf();
	$dompdf->loadHtml($dataToPrint);

	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper($paper, $orientation);

	// Render the HTML as PDF
	$dompdf->render();

	$dompdf->addInfo('Title', $title);
	$dompdf->addInfo('Author', $author);

	// Output the generated PDF to Browser
	if ($download)
		$result = $dompdf->stream($filename . '.pdf', array('Attachment' => 1));
	else
		$result = $dompdf->stream($filename . '.pdf', array('Attachment' => 0));

	ob_end_clean();
}

// DATATABLE PLUGIN

if (!function_exists('serversideDT')) {
	function serversideDT()
	{
		return new Datatables(new CodeigniterAdapter);
	}
}

// BLADE PLUGIN

if (!function_exists('render')) {
	function render($fileName, $data = NULL)
	{
		$views = APPPATH . 'views';
		$cache = isMobileDevice() ? APPPATH . 'cache' . DIRECTORY_SEPARATOR . 'blade_cache/mobile/' : APPPATH . 'cache' . DIRECTORY_SEPARATOR . 'blade_cache/browser/';

		$fileName = $fileName . '.blade.php';

		if (file_exists($views . DIRECTORY_SEPARATOR . $fileName)) {

			if (!file_exists($cache)) {
				mkdir($cache, 0755, true);
			}

			loadBladeTemplate($views, $cache, $fileName, $data);
		} else {
			log_message('error', $fileName . 'not found');
			errorpage('404');
		}
	}
}

if (!function_exists('loadBladeTemplate')) {
	function loadBladeTemplate($views, $cache = NULL, $fileName = NULL, $data = NULL)
	{
		# Please use this settings :
		# 0 - MODE_AUTO : BladeOne reads if the compiled file has changed. If has changed,then the file is replaced.
		# 1 - MODE_SLOW : Then compiled file is always replaced. It's slow and it's useful for development.
		# 2 - MODE_FAST : The compiled file is never replaced. It's fast and it's useful for production.
		# 5 - MODE_DEBUG :  DEBUG MODE, the file is always compiled and the filename is identifiable.
		try {
			$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
			// $blade->setAuth(currentUserID(), currentUserRoleID(), permission());
			$blade->setBaseUrl(baseURL() . 'public/'); // with or without trail slash
			echo $blade->run($fileName, $data);
		} catch (Exception $e) {
			log_message('error', $e->getMessage());
			echo "<b> ERROR FOUND : </b> <br><br>" . $e->getMessage() . "<br><br><br>" . $e->getTraceAsString();
		}
	}
}

if (!function_exists('errorpage')) {
	function errorpage($code = NULL)
	{
		if ($code == '404')
			redirect('error/404', true);
		else if ($code == '403')
			redirect('error/403', true);
		else
			error($code, ['title' => '500', 'message' => '', 'image' => asset('custom/images/maintenance.png')]);
	}
}

// GENERATE UUIDv4

if (!function_exists('uuid')) {
	function uuid($code = NULL)
	{
		$uuid = Uuid::uuid4();
		return $uuid->toString();
	}
}
