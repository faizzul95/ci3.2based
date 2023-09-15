<?php

/**
 * CLI Routes
 *
 * This routes only will be available under a CLI environment
 */

// To enable Luthier-CI built-in cli commands
// uncomment the followings lines:
use App\core\Struck;

Luthier\Cli::maker();
Luthier\Cli::migrations();

// Backup Database to Google Drive
Route::cli('cron/database/{upload?}', 'BackupController@BackupDatabase');
Route::cli('cron/system/{upload?}', 'BackupController@BackupSystem');

// QUEUE / JOBS
Route::cli('jobs', 'JobController@work');

Route::cli('jobs:listen', 'JobController@listen'); // only macbook & linux
Route::cli('jobs:work', 'JobController@work'); // for windows & linux environment
Route::cli('jobs:launch', 'JobController@launch');
Route::cli('jobs:single', 'JobController@single');

Route::cli('jobs:list', function () {
	$isLinux = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? false : true;

	if (!$isLinux) {
		die(output('error', "Queue Listener requires Linux OS"));
	}

	echo shell_exec('ps aux|grep php');
});

Route::cli('queue:retry/{uuid?}', function ($uuid = NULL) {
	if (hasData($uuid)) {
		$queue = new Struck();
		if ($uuid == 'all') {
			$queue->processAllFailedQueue();
		} else {
			$queue->processFailedQueueByUUID($uuid);
		}
	} else {
		output('error', "Please provide UUID!");
	}
});

// STUB
Route::cli('create/{type}/{fileName}/{tableName?}', function ($type, $name = NULL, $tableName = NULL) {

	// Record the start time
	$startTime = microtime(true);

	$name = isset($name) ? ucfirst(trim($name)) : NULL;
	$tableName = isset($tableName) ? trim($tableName) : NULL;
	$column = '';

	if ($type == 'controller') {

		$fileNameGenerate = $name . 'Controller';
		$controllerStub = APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'controller.stub';

		if (file_exists($controllerStub)) {
			// // Load the stub file into a string
			$stubController = file_get_contents($controllerStub);

			// // Replace placeholders in the stub file with actual values
			// // For example, you might replace "%CLASS_NAME%" with the name of the class you want to generate
			$stubController = str_replace('%CLASS_CONTROLLER_NAME%', $fileNameGenerate, $stubController);
			$stubController = str_replace('%CLASS_NAME%', $name, $stubController);
			$stubController = str_replace('%TABLE_NAME%', $name, $stubController);
			// // Generate the filename for the new controller
			$filename = APPPATH . 'controllers' . DIRECTORY_SEPARATOR . '' . $fileNameGenerate . '.php';
			// // Write the stub file to the new controller file
			file_put_contents($filename, $stubController);
		} else {
			die(output('error', "Stub files '{$controllerStub}' don't exist."));
		}
	} else if ($type == 'model') {

		$fileNameGenerate = $name . '_model';
		$modelStub = APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'model.stub';

		if (file_exists($modelStub)) {
			$table = '';
			$fillable = '';
			$pk = '';
			$dataTableEdit = '';

			if (!empty($tableName)) {
				if (isTableExist($tableName)) {
					$table = $tableName;
					$pk = primary_field_name($tableName);
					$allColumn = allTableColumn($table);

					if (!empty($allColumn)) {

						$removeFillable = [];
						$dataTableEditArr = [];
						array_push($removeFillable, $pk);

						if (in_array('created_at', $allColumn))
							array_push($removeFillable, 'created_at');

						if (in_array('updated_at', $allColumn))
							array_push($removeFillable, 'updated_at');

						if (in_array('deleted_at', $allColumn))
							array_push($removeFillable, 'deleted_at');

						$allColumn = array_diff($allColumn, $removeFillable);

						$fillable = "'" . implode("'," . PHP_EOL . " '", $allColumn) . "'";

						foreach ($allColumn as $key => $columnName) {
							$dataTableEdit .= '
					$serverside->edit("' . $columnName . '", function ($data) {
						return purify($data["' . $columnName . '"]);
					});
					';
						}

						$column = str_replace("'", "`", $fillable);
					}
				}
			}

			// // Load the stub file into a string
			$stubModel = file_get_contents($modelStub);
			$stubModel = str_replace('%CLASS_MODEL_NAME%', $name, $stubModel);
			$stubModel = str_replace('%TABLE%', $table, $stubModel);
			$stubModel = str_replace('%FILLABLE%', $fillable, $stubModel);
			$stubModel = str_replace('%COLUMN%', $column, $stubModel);
			$stubModel = str_replace('%PK%', $pk, $stubModel);
			$stubModel = str_replace('%DATATABLE_EDIT%', $dataTableEdit, $stubModel);
			$filename = APPPATH . 'models' . DIRECTORY_SEPARATOR . '' . $fileNameGenerate . '.php';
			file_put_contents($filename, $stubModel);
		} else {
			die(output('error', "Stub files '{$modelStub}' don't exist."));
		}
	}

	// Record the end time
	$endTime = microtime(true);

	// Calculate the elapsed time in seconds
	$elapsedTimeMs = number_format(($endTime - $startTime) * 1000, 3);

	echo '';
	if (in_array($type, ['controller', 'model']))
		die(output('success', "Create {$fileNameGenerate} successfully"));
	else
		die(output('error', "The type only support for 'controller' or 'model', Your have enter : {$type}"));
});

Route::cli('generate/services/{module}/{fileName}/{modelName?}/{tableName?}', function ($module = 'default', $name = NULL, $modelName = NULL, $tableName = NULL) {

	$tableField = '';
	$pkField = 'id';
	if (!empty($tableName)) {
		if (isTableExist($tableName)) {
			$table = $tableName;
			$pkField = primary_field_name($table);
			$allColumn = allTableColumn($table);
			if (!empty($allColumn)) {
				$keysToRemove = ["created_at", "updated_at", "deleted_at"];
				foreach ($keysToRemove as $element) {
					$key = array_search($element, $allColumn);
					unset($allColumn[$key]);
				}
				$tableField = implode(',', $allColumn);
			}
		}
	}

	$fileName = isset($name) ? ucfirst(trim($name)) : 'Default';
	$modelName = isset($modelName) ? ucfirst(trim($modelName . '_model')) : NULL;
	$filePath = '';
	$folder = $name;

	$directoryPath = [
		'logics' => APPPATH . 'services' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'logics' . DIRECTORY_SEPARATOR,
		'processors' => APPPATH . 'services' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'processors' . DIRECTORY_SEPARATOR
	];

	if (!is_dir($directoryPath['logics'])) {
		mkdir($directoryPath['logics'], 0755, true); // The 'true' parameter recursively creates directories if they don't exist
		chmod($directoryPath['logics'], 0755); // Set the file permissions to 755
	}

	if (!is_dir($directoryPath['processors'])) {
		mkdir($directoryPath['processors'], 0755, true); // The 'true' parameter recursively creates directories if they don't exist
		chmod($directoryPath['processors'], 0755); // Set the file permissions to 755
	}

	$stubPath = [
		'logics' => [
			'show' => APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'services' .  DIRECTORY_SEPARATOR . 'logics' . DIRECTORY_SEPARATOR  . 'show.stub',
			'create' => APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'services' .  DIRECTORY_SEPARATOR . 'logics' . DIRECTORY_SEPARATOR  . 'create_update_delete.stub',
			'update' => APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'services' .  DIRECTORY_SEPARATOR . 'logics' . DIRECTORY_SEPARATOR  . 'create_update_delete.stub',
			'delete' => APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'services' .  DIRECTORY_SEPARATOR . 'logics' . DIRECTORY_SEPARATOR  . 'create_update_delete.stub',
		],
		'processors' => [
			'search' => APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'services' .  DIRECTORY_SEPARATOR . 'processors' . DIRECTORY_SEPARATOR  . 'search.stub',
			'store' => APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'services' .  DIRECTORY_SEPARATOR . 'processors' . DIRECTORY_SEPARATOR  . 'store.stub',
			'delete' => APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'services' .  DIRECTORY_SEPARATOR . 'processors' . DIRECTORY_SEPARATOR  . 'delete.stub',
		]
	];

	$fileArr = [
		'logics' => [
			'show' => 'ShowLogic',
			'create' => 'CreateLogic',
			'update' => 'UpdateLogic',
			'delete' => 'DeleteLogic',
		],
		'processors' => [
			'search' => 'SearchProcessors',
			'store' => 'StoreProcessors',
			'delete' => 'DeleteProcessors',
		]
	];

	foreach ($stubPath as $category => $dataServicesArr) {

		// Record the start time
		$startTime = microtime(true);

		$filesCreated = [];

		foreach ($dataServicesArr as $fileStub => $sPath) {
			$dataServices = $className = NULL; // reset

			if (file_exists($sPath)) {
				// Load the stub file into a string
				$dataServices = file_get_contents($sPath);

				$className = $fileName . $fileArr[$category][$fileStub];

				// Replace placeholders in the stub file with actual values
				$dataServices = str_replace('%MODULE%', $module, $dataServices);
				if ($category == 'logics') {
					$processorMapping = [
						'show' => 'SearchProcessors',
						'create' => 'StoreProcessors',
						'update' => 'StoreProcessors',
						'delete' => 'DeleteProcessors'
					];

					if ($fileStub == 'show') {
						$getFieldData = '';
						$dataServices = str_replace('%FIELD%', $tableField, $dataServices);
						$dataServices = str_replace('%PRIMARY_KEY%', $pkField, $dataServices);
					}

					$dataServices = str_replace('%CLASS_PROCESSOR_NAME%', $fileName . $processorMapping[$fileStub], $dataServices);
				}
				$dataServices = str_replace('%FOLDER%', $folder, $dataServices);
				$dataServices = str_replace('%CLASS_NAME%', $className, $dataServices);
				$dataServices = str_replace('%MODEL_NAME%', $modelName, $dataServices);

				// Generate the filename for the new service
				$filePath = $directoryPath[$category] . $className . '.php';

				// Write the stub file to the new service file
				file_put_contents($filePath, $dataServices);

				// push array success
				array_push($filesCreated, $fileStub);
			}
		}

		// Record the end time
		$endTime = microtime(true);

		// Calculate the elapsed time in seconds
		$elapsedTimeMs = number_format(($endTime - $startTime) * 1000, 3);

		$files_create = implode(', ', $filesCreated);
		if (count($filesCreated) > 0)
			output('success', "Create service for {$category} {$files_create} at {$directoryPath[$category]}");
		else
			output('error', "Failed to create services for {$category}");
	}
});

Route::cli('structure/{name}/{tableName?}', function ($name, $tableName = NULL) {
	$controllerStub = APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'controller.stub';
	$modelStub = APPPATH . 'helpers' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'model.stub';

	$checkController = file_exists($controllerStub);
	$checkModel = file_exists($modelStub);

	if ($checkController && $checkModel) {

		$name = isset($name) ? trim($name) : NULL;
		$name = isset($name) ? ucfirst($name) : NULL;
		$tableName = isset($tableName) ? trim($tableName) : NULL;
		$column = NULL;

		// // Load the stub file into a string
		$stubController = file_get_contents($controllerStub);

		// // Replace placeholders in the stub file with actual values
		// // For example, you might replace "%CLASS_NAME%" with the name of the class you want to generate
		$stubController = str_replace('%CLASS_CONTROLLER_NAME%', $name . 'Controller', $stubController);
		$stubController = str_replace('%CLASS_NAME%', $name, $stubController);
		// // Generate the filename for the new controller
		$filename = APPPATH . 'controllers' . DIRECTORY_SEPARATOR . '' . $name . 'Controller.php';
		// // Write the stub file to the new controller file
		file_put_contents($filename, $stubController);

		// ===================================================================

		$table = '';
		$fillable = '';
		$pk = '';
		$dataTableEdit = '';

		if (!empty($tableName)) {
			if (isTableExist($tableName)) {
				$table = $tableName;
				$pk = primary_field_name($tableName);
				$allColumn = allTableColumn($table);

				if (!empty($allColumn)) {

					$removeFillable = [];
					$dataTableEditArr = [];
					array_push($removeFillable, $pk);

					if (in_array('created_at', $allColumn))
						array_push($removeFillable, 'created_at');

					if (in_array('updated_at', $allColumn))
						array_push($removeFillable, 'updated_at');

					if (in_array('deleted_at', $allColumn))
						array_push($removeFillable, 'deleted_at');

					$allColumn = array_diff($allColumn, $removeFillable);

					$fillable = "'" . implode("'," . PHP_EOL . " '", $allColumn) . "'";

					foreach ($allColumn as $key => $columnName) {
						$dataTableEdit .= '
					$serverside->edit("' . $columnName . '", function ($data) {
						return purify($data["' . $columnName . '"]);
					});
					';
					}

					$column = str_replace("'", "`", $fillable);
				}
			}
		}

		// // Load the stub file into a string
		$stubModel = file_get_contents($modelStub);
		$stubModel = str_replace('%CLASS_MODEL_NAME%', $name, $stubModel);
		$stubModel = str_replace('%TABLE%', $table, $stubModel);
		$stubModel = str_replace('%FILLABLE%', $fillable, $stubModel);
		$stubModel = str_replace('%COLUMN%', $column, $stubModel);
		$stubModel = str_replace('%PK%', $pk, $stubModel);
		$stubModel = str_replace('%DATATABLE_EDIT%', $dataTableEdit, $stubModel);

		// // Generate the filename for the new model
		$filename = APPPATH . 'models' . DIRECTORY_SEPARATOR . '' . $name . '_model.php';
		// // Write the stub file to the new model file
		file_put_contents($filename, $stubModel);

		die(output('success', "Create controller & models {$name} successfully\n"));
	} else {
		$message = !$checkController ? $controllerStub : $modelStub;
		die(output('error', "Stub files '{$message}' don't exist."));
	}
});

Route::cli('clear/{type}', function ($type) {

	// Record the start time
	$startTime = microtime(true);

	$folderCache = APPPATH . 'cache' . DIRECTORY_SEPARATOR . 'ci_sessions';
	$folderViewCache = APPPATH . 'cache' . DIRECTORY_SEPARATOR . 'blade_cache';
	$folderLogs = APPPATH . 'logs';
	$typeMessage = 'success';

	if (in_array($type, ['all', 'cache', 'view', 'views', 'log', 'logs', 'optimize'])) {

		if ($type == 'cache') {
			if (is_dir($folderCache))
				deleteFolder($folderCache);
		} else if (in_array($type, ['view', 'views'])) {
			if (is_dir($folderViewCache))
				deleteFolder($folderViewCache);
		} else if (in_array($type, ['log', 'logs'])) {
			if (is_dir($folderLogs))
				deleteFolder($folderLogs);
		} else if ($type == 'all') {
			$folders = [];
			array_push($folders, $folderCache, $folderViewCache, $folderLogs);
			foreach ($folders as $key => $path) {
				if (is_dir($path))
					deleteFolder($path);
			}
			$type = 'Cache, views, logs';
		} else if ($type == 'optimize') {
			$folders = [];
			array_push($folders, $folderViewCache, $folderLogs);
			foreach ($folders as $key => $path) {
				if (is_dir($path))
					deleteFolder($path);
			}
			$type = 'Views cache & logs';
		}

		$message = $type . ' folder clear successfully';
	} else {
		$message = "Error : The type only support for 'cache','view' or 'log' : Your enter " . $type;
		$typeMessage = 'error';
	}

	// Record the end time
	$endTime = microtime(true);

	// Calculate the elapsed time in seconds
	$elapsedTimeMs = number_format(($endTime - $startTime) * 1000, 3);

	output($typeMessage, $message);

	echo "cache clear .................................................................................................. {$elapsedTimeMs}ms\n" . PHP_EOL;
});

// SCHEDULER
Route::cli('schedule:run', function () {
	$CI = ci();
	$CI->load->config('scheduler');
	$allNamespace = $CI->config->item('commands');

	if (hasData($allNamespace)) {
		$scheduler = cronScheduler();

		$scheduler->clearJobs(); // clear previous jobs
		foreach ($allNamespace as $namspaces) {
			app($namspaces)->handle($scheduler);
		}

		output('success', "Task Scheduling is running..");

		// Reset the scheduler after a previous run
		$scheduler->resetRun()->run(); // now we can run it again
	} else {
		die(output('error', "No task/command to execute"));
	}
});

Route::cli('schedule:list', function () {
	dd(cronScheduler()->getExecutedJobs());
});

Route::cli('schedule:fail', function () {
	$scheduler = cronScheduler();

	// get all failed jobs and select first
	$failedJob = $scheduler->getFailedJobs()[0];

	// exception that occurred during job
	$exception = $failedJob->getException();

	// job that failed
	$job = $failedJob->getJob();

	dd($failedJob, $exception, $job);
});

Route::cli('schedule:work', function () {
	$scheduler = cronScheduler();

	$CI = ci();
	$CI->load->config('scheduler');
	$allNamespace = $CI->config->item('commands');

	if (hasData($allNamespace)) {
		$scheduler = cronScheduler();

		$scheduler->clearJobs(); // clear previous jobs
		foreach ($allNamespace as $namspaces) {
			app($namspaces)->handle($scheduler);
		}

		output('success', "Task Scheduling is running..");

		$scheduler->work();
	} else {
		output('info', "No task/command to execute");
	}
});


//  GENERAL
Route::cli('optimize', function () {
	Struck::call('optimize');
});

Route::cli('maintenance/{type}', function ($type = 'on') {
	if (in_array($type, ['on', 'off'])) {
		$filename = 'maintenance.flag';

		if ($type == 'on') {
			if (!file_exists($filename)) {
				fopen($filename, 'w');
			};
			output('info', "[" . timestamp('d/m/Y h:i A') . "]: System is currently offline!");
		} else if ($type == 'off') {
			if (file_exists($filename)) {
				unlink($filename);
			}
			output('info', "[" . timestamp('d/m/Y h:i A') . "]: System is currently online!");
		}
	}
});

Route::cli('websocket', function () {
	output('success', "Websocket is running..");
	app('App\services\generals\helpers\WebSocketRunner')->init();
});


// SECTION FOR TESTING GLOBAL FUNCTION

Route::cli('jwt', function () {
	$dataEncode = generate_jwt_token(['user_id' => 3000, 'profile_id' => 3]);
	$dataDecode = validate_jwt_token($dataEncode);
	output('info', $dataEncode);
	output('info', print_r($dataDecode));
});