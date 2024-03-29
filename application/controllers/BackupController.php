<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\generals\helpers\BackupSystem as BackupSystem;
use App\services\generals\helpers\google\GoogleDrive as GD;
use App\services\generals\helpers\google\GoogleAnalytic as GA;

use App\services\modules\core\systemBackupDB\processors\SystemBackupDBStoreProcessors;

use App\middleware\core\traits\SecurityHeadersTrait;

class BackupController extends CI_Controller
{
	use SecurityHeadersTrait;

	public function __construct()
	{
		parent::__construct();
		$this->set_security_headers();
	}

	public function index()
	{
		show_404();
	}

	public function BackupSystem($uploadDrive = NULL)
	{
		$backup = new BackupSystem();
		$response = $backup->backup_folder();

		if (isSuccess($response['code'])) {
			if ($uploadDrive) {
				$drive = $this->BackupDrive($response['path'], 'system');
				if (isSuccess($drive['code'])) {
					$response['data'] = [
						'backup_storage_type' => 'google drive',
						'backup_location' =>  $drive['data']['webViewLink']
					];
				}
			}
		}

		dd($response);
	}

	public function BackupDatabase($uploadDrive = NULL)
	{
		$backup = new BackupSystem();
		$response = $backup->backup_database();

		if (isSuccess($response['code'])) {

			$res = [
				'backup_name' => $response['filename'],
				'backup_storage_type' => $response['storage'],
				'backup_location' => $response['path'],
			];

			if ($uploadDrive) {
				$drive = $this->BackupDrive($response['path'], 'database');
				if (isSuccess($drive['code'])) {
					$res['backup_storage_type'] = 'google drive';
					$res['backup_location'] = $drive['data']['webViewLink'];
				}
			}

			$response = app(new SystemBackupDBStoreProcessors)->execute($res);
		}

		dd($response);
	}

	public function BackupDrive($path = NULL, $folderType = NULL)
	{
		if (!empty($path)) {
			$drive = new GD($folderType);

			if (file_exists($path)) {
				return $drive->uploadFile($path);
			} else {
				dd('File upload does not exist!');
			}
		} else {
			dd('Please specify the path to upload!');
		}
	}
}
