<?php

use eftec\bladeone\BladeOne; // reff : https://github.com/EFTEC/BladeOne
use voku\helper\AntiXSS; // reff : https://github.com/voku/anti-xss
use voku\helper\HtmlMin; // reff : https://github.com/voku/HtmlMin
use GO\Scheduler; // reff : https://github.com/peppeocchi/php-cron-scheduler

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\CodeigniterAdapter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

// use Dompdf\Dompdf;
// use Dompdf\Options;

use Luthier\Debug;
use Ramsey\Uuid\Uuid;

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// ROUTING PLUGIN

if (!function_exists('logDebug')) {
	function logDebug($logMessage = NULL, $logType = 'info', $type = 'log')
	{
		Debug::$type($logMessage, $logType);
	}
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

if (!function_exists('recaptchaDiv')) {
	function recaptchaDiv($size = 'invisible', $callback = 'setResponse')
	{
		if (filter_var(env('RECAPTCHA_ENABLE'), FILTER_VALIDATE_BOOLEAN)) {
			$sitekey = env('RECAPTCHA_KEY');
			return '<div class="g-recaptcha" data-sitekey="' . $sitekey . '" data-size="' . $size . '" data-callback="' . $callback . '"></div>
					<input type="hidden" id="captcha-response" name="g-recaptcha-response" class="form-control" />';
		} else {
			return NULL;
		}
	}
}

if (!function_exists('gapiConfig')) {
	function gapiConfig()
	{
		$ci = ci();
		$ci->load->config('google');

		return json_encode([
			'client_id' => $ci->config->item('client_id_auth'),
			'cookiepolicy' => $ci->config->item('cookie_policy'),
			'fetch_basic_profile' => true,
			'redirect_uri' => $ci->config->item('redirect_uri_auth'),
		]);
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

					return ['resCode' => 201, 'data' => $spreadSheetAry, 'count' => count($spreadSheetAry)];
				} else {
					return returnData(['resCode' => 422, 'message' => 'The files upload was not found.'], 422);
				}
			} else {
				return returnData(['resCode' => 422, 'message' => 'The size is not supported : ' . $size . ' bytes'], 422);
			}
		} else {
			return returnData(['resCode' => 422, 'message' => 'The file type is not supported : ' . $type], 422);
		}
	}
}

// EXPORT EXCEL PLUGIN

if (!function_exists('exportToExcel')) {
	function exportToExcel($data, $filename = "data.xlsx")
	{
		ini_set('display_errors', '1');
		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 0);

		try {
			// reset previous buffer
			ob_end_clean();

			// start output buffering
			ob_start();

			// Create new Spreadsheet object
			$spreadsheet = new Spreadsheet();

			// set properties
			$title = empty($option) ? "My Excel Data" : (isset($option['title']) ? $option['title'] : "My Excel Data");
			$spreadsheet->getProperties()
				->setTitle($title)
				->setKeywords('data,export,excel')
				->setCreator(env('APP_NAME'))
				->setLastModifiedBy(currentUserFullName())
				->setCompany(currentCompanyName())
				->setCategory('Data Export')
				->setCreated(timestamp());

			// Add data to the first sheet
			$sheet = $spreadsheet->getActiveSheet();

			// Set data in the worksheet
			$sheet->fromArray($data);

			// Set the headers to force a download
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');

			// Create a new Xlsx writer and save the file
			$writer = new Xlsx($spreadsheet);

			// Check if the writer object is valid
			if ($writer === null) {
				return ['resCode' => 400, 'message' => 'Error creating Xlsx writer object'];
			}

			// end output buffering and flush the output
			ob_end_clean();

			$directory = 'public' . DIRECTORY_SEPARATOR . '_temp' . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR;
			if (!file_exists($directory)) {
				mkdir($directory, 0755, true);
			}

			$tempFile = $directory . 'export_excel.xls';
			if (file_exists($directory)) {
				unlink($tempFile);
			}

			$result = $writer->save($tempFile);

			// Save to computer.
			// $result = $writer->save('php://output');

			// Check if the file was saved successfully
			// if ($result === null) {
			// 	return ['resCode' => 400, 'message' => 'Error saving Excel file'];
			// 	exit;
			// }

			// Return success message
			return ['resCode' => 200, 'message' => 'File exported', 'filename' => $filename, 'path' => url($tempFile)];
		} catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
			return ['resCode' => 400, 'message' => 'Error writing to file: ', $e->getMessage()];
		} catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
			return ['resCode' => 400, 'message' => 'Error: ', $e->getMessage()];
		} catch (Exception $e) {
			// Return error message
			return ['resCode' => 400, 'message' => 'Error exporting file: ' . $e->getMessage()];
		}
	}
}

// EXPORT PDF PLUGIN

if (!function_exists('generate_dompdf')) {
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
			error('404');
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
			// echo $blade->run($fileName, $data);
			echo minifyHtml($blade->run($fileName, $data));
		} catch (Exception $e) {
			log_message('error', $e->getMessage());
			echo "<b> ERROR FOUND : </b> <br><br>" . $e->getMessage() . "<br><br><br>" . $e->getTraceAsString();
		}
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

// MINIFY PLUGIN 

if (!function_exists('minifyHtml')) {
	function minifyHtml($htmlTag)
	{
		$htmlMin = new HtmlMin();

		$htmlMin->doOptimizeViaHtmlDomParser(true);               // optimize html via "HtmlDomParser()"
		$htmlMin->doRemoveComments();                     			// remove default HTML comments (depends on "doOptimizeViaHtmlDomParser(true)")
		$htmlMin->doSumUpWhitespace();                    			// sum-up extra whitespace from the Dom (depends on "doOptimizeViaHtmlDomParser(true)")
		$htmlMin->doRemoveWhitespaceAroundTags();         			// remove whitespace around tags (depends on "doOptimizeViaHtmlDomParser(true)")
		$htmlMin->doOptimizeAttributes();                 		// optimize html attributes (depends on "doOptimizeViaHtmlDomParser(true)")
		$htmlMin->doRemoveHttpPrefixFromAttributes();         // remove optional "http:"-prefix from attributes (depends on "doOptimizeAttributes(true)")
		$htmlMin->doRemoveHttpsPrefixFromAttributes();        // remove optional "https:"-prefix from attributes (depends on "doOptimizeAttributes(true)")
		$htmlMin->doKeepHttpAndHttpsPrefixOnExternalAttributes(); // keep "http:"- and "https:"-prefix for all external links 
		$htmlMin->doMakeSameDomainsLinksRelative(['example.com']); // make some links relative, by removing the domain from attributes
		$htmlMin->doRemoveDefaultAttributes();                // remove defaults (depends on "doOptimizeAttributes(true)" | disabled by default)
		$htmlMin->doRemoveDeprecatedAnchorName();             // remove deprecated anchor-jump (depends on "doOptimizeAttributes(true)")
		$htmlMin->doRemoveDeprecatedScriptCharsetAttribute(); // remove deprecated charset-attribute - the browser will use the charset from the HTTP-Header, anyway (depends on "doOptimizeAttributes(true)")
		$htmlMin->doRemoveDeprecatedTypeFromScriptTag();      // remove deprecated script-mime-types (depends on "doOptimizeAttributes(true)")
		$htmlMin->doRemoveDeprecatedTypeFromStylesheetLink(); // remove "type=text/css" for css links (depends on "doOptimizeAttributes(true)")
		$htmlMin->doRemoveDeprecatedTypeFromStyleAndLinkTag(); // remove "type=text/css" from all links and styles
		$htmlMin->doRemoveDefaultMediaTypeFromStyleAndLinkTag(); // remove "media="all" from all links and styles
		$htmlMin->doRemoveEmptyAttributes();                  // remove some empty attributes (depends on "doOptimizeAttributes(true)")
		$htmlMin->doRemoveValueFromEmptyInput();              // remove 'value=""' from empty <input> (depends on "doOptimizeAttributes(true)")
		$htmlMin->doSortCssClassNames();                      // sort css-class-names, for better gzip results (depends on "doOptimizeAttributes(true)")
		$htmlMin->doSortHtmlAttributes();                     // sort html-attributes, for better gzip results (depends on "doOptimizeAttributes(true)")
		$htmlMin->doRemoveSpacesBetweenTags();                // remove more (aggressive) spaces in the dom (disabled by default)
		$htmlMin->doRemoveOmittedHtmlTags();

		return $htmlMin->minify($htmlTag);
	}
}

// SCHEDULE PLUGIN

if (!function_exists('cronScheduler')) {
	function cronScheduler()
	{
		return new Scheduler(); // Create a new scheduler
	}
}
