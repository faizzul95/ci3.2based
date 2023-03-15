<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\BackupSystem as BackupSystem;
use App\services\GoogleDrive as GD;

class CronController extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		model('SystemQueueJob_model', 'queueM');
		model('SystemBackupDB_model', 'databaseM');
	}

	public function index()
	{
		show_404();
	}

	public function BackupSystem($uploadDrive = NULL)
	{
		$backup = new BackupSystem();
		$response = $backup->backup_folder();

		if (isSuccess($response['resCode'])) {
			if ($uploadDrive) {
				$drive = $this->BackupDrive($response['path']);
				if (isSuccess($drive['resCode'])) {
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

		if (isSuccess($response['resCode'])) {

			$res = [
				'backup_name' => $response['filename'],
				'backup_storage_type' => $response['storage'],
				'backup_location' => $response['path'],
			];

			if ($uploadDrive) {
				$drive = $this->BackupDrive($response['path']);
				if (isSuccess($drive['resCode'])) {
					$res['backup_storage_type'] = 'google drive';
					$res['backup_location'] = $drive['data']['webViewLink'];
				}
			}

			$response = $this->databaseM::save($res);
		}

		dd($response);
	}

	public function BackupDrive($path = NULL)
	{
		if (!empty($path)) {
			$drive = new GD();

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
