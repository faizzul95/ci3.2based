<?php

/**
 * CLI Routes
 *
 * This routes only will be available under a CLI environment
 */

// To enable Luthier-CI built-in cli commands
// uncomment the followings lines:

Luthier\Cli::maker();
Luthier\Cli::migrations();

Route::cli('jobs', 'JobController@work');

Route::cli('jobs/listen', 'JobController@listen');
Route::cli('jobs/launch', 'JobController@launch');
Route::cli('jobs/work', 'JobController@work');
Route::cli('jobs/single', 'JobController@single');

Route::cli('create/{type}/{fileName}/{tableName?}', function ($type, $name = NULL, $tableName = NULL) {

	$name = isset($name) ? trim($name) : NULL;
	$name = isset($name) ? ucfirst($name) : NULL;
	$tableName = isset($tableName) ? trim($tableName) : NULL;

	if ($type == 'controller') {

		$fileNameGenerate = $name . 'Controller';
		$controllerStub = APPPATH . '\helpers\stubs\controller.stub';

		// // Load the stub file into a string
		$stubController = file_get_contents($controllerStub);

		// // Replace placeholders in the stub file with actual values
		// // For example, you might replace "%CLASS_NAME%" with the name of the class you want to generate
		$stubController = str_replace('%CLASS_CONTROLLER_NAME%', $fileNameGenerate, $stubController);
		$stubController = str_replace('%CLASS_NAME%', $name, $stubController);
		// // Generate the filename for the new controller
		$filename = APPPATH . 'controllers/' . $fileNameGenerate . '.php';
		// // Write the stub file to the new controller file
		file_put_contents($filename, $stubController);
	} else if ($type == 'model') {

		$fileNameGenerate = $name . '_model';
		$modelStub = APPPATH . '\helpers\stubs\model.stub';
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

					$fillable = "'" . implode("', '", $allColumn) . "'";

					foreach ($allColumn as $key => $columnName) {
						$dataTableEdit .= '
					$serverside->edit("' . $columnName . '", function ($data) {
						return purify($data["' . $columnName . '"]);
					});
					';
					}
				}
			}
		}

		// // Load the stub file into a string
		$stubModel = file_get_contents($modelStub);
		$stubModel = str_replace('%CLASS_MODEL_NAME%', $name, $stubModel);
		$stubModel = str_replace('%TABLE%', $table, $stubModel);
		$stubModel = str_replace('%FILLABLE%', $fillable, $stubModel);
		$stubModel = str_replace('%PK%', $pk, $stubModel);
		$stubModel = str_replace('%DATATABLE_EDIT%', $dataTableEdit, $stubModel);
		$filename = APPPATH . 'models/' . $fileNameGenerate . '.php';
		file_put_contents($filename, $stubModel);
	}

	echo '';
	if (in_array($type, ['controller', 'model']))
		echo "Create $fileNameGenerate successfully\n\n";
	else
		echo "Error : The type only support for 'controller' or 'model', Your have enter : $type\n\n";
});

Route::cli('structure/{name}/{tableName?}', function ($name, $tableName = NULL) {

	$name = isset($name) ? trim($name) : NULL;
	$name = isset($name) ? ucfirst($name) : NULL;
	$tableName = isset($tableName) ? trim($tableName) : NULL;

	$controllerStub = APPPATH . '\helpers\stubs\controller.stub';

	// // Load the stub file into a string
	$stubController = file_get_contents($controllerStub);

	// // Replace placeholders in the stub file with actual values
	// // For example, you might replace "%CLASS_NAME%" with the name of the class you want to generate
	$stubController = str_replace('%CLASS_CONTROLLER_NAME%', $name . 'Controller', $stubController);
	$stubController = str_replace('%CLASS_NAME%', $name, $stubController);
	// // Generate the filename for the new controller
	$filename = APPPATH . 'controllers/' . $name . 'Controller.php';
	// // Write the stub file to the new controller file
	file_put_contents($filename, $stubController);

	// ===================================================================

	$modelStub = APPPATH . '\helpers\stubs\model.stub';
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

				$fillable = "'" . implode("', '", $allColumn) . "'";

				foreach ($allColumn as $key => $columnName) {
					$dataTableEdit .= '
					$serverside->edit("' . $columnName . '", function ($data) {
						return purify($data["' . $columnName . '"]);
					});
					';
				}
			}
		}
	}

	// // Load the stub file into a string
	$stubModel = file_get_contents($modelStub);
	$stubModel = str_replace('%CLASS_MODEL_NAME%', $name, $stubModel);
	$stubModel = str_replace('%TABLE%', $table, $stubModel);
	$stubModel = str_replace('%FILLABLE%', $fillable, $stubModel);
	$stubModel = str_replace('%PK%', $pk, $stubModel);
	$stubModel = str_replace('%DATATABLE_EDIT%', $dataTableEdit, $stubModel);

	// // Generate the filename for the new model
	$filename = APPPATH . 'models/' . $name . '_model.php';
	// // Write the stub file to the new model file
	file_put_contents($filename, $stubModel);

	echo "Create controller & models $name successfully\n\n";
});

Route::cli('clear/{type}', function ($type) {

	$folderCache = APPPATH . '\cache\ci_session';
	$folderViewCache = APPPATH . '\cache\blade_cache';
	$folderLogs = APPPATH . '\logs';

	if (in_array($type, ['cache', 'view', 'views', 'log', 'logs'])) {
		if ($type == 'cache') {
			if (is_dir($folderCache))
				deleteFolder($folderCache);
		} else if (in_array($type, ['view', 'views'])) {
			if (is_dir($folderViewCache))
				deleteFolder($folderViewCache);
		} else if (in_array($type, ['log', 'logs'])) {
			if (is_dir($folderLogs))
				clearFolder($folderLogs, ['index.php']);
		}

		$message = $type . ' folder clear successfully';
	} else {
		$message = "Error : The type only support for 'cache','view' or 'log' : Your enter " . $type;
	}

	echo $message . "\n\n";
});

function deleteFolder($folder)
{
	if (is_dir($folder)) {
		$files = scandir($folder);
		foreach ($files as $file) {
			if ($file != '.' && $file != '..') {
				$path = $folder . '/' . $file;
				if (is_dir($path)) {
					deleteFolder($path); // Recursively delete subdirectories
				} else {
					unlink($path); // Delete files
				}
			}
		}
		rmdir($folder); // Delete the main directory after its contents have been removed
	}
}

function clearFolder($folder, $excludedItems = array())
{
	$files = glob($folder . '/*');
	foreach ($files as $file) {
		if (!in_array(basename($file), $excludedItems)) {
			if (is_dir($file)) {
				clearFolder($file, $excludedItems); // Delete subdirectories recursively
			} else {
				unlink($file); // Delete files
			}
		}
	}
}
