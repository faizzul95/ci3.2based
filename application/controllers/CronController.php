<?php

defined('BASEPATH') or exit('No direct script access allowed');

use App\services\BackupSystem as BackupSystem;
use App\services\GoogleDrive as GD;

class CronController extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		show_404();
	}

	public function BackupSystem($uploadDrive = NULL)
	{
		$backup = new BackupSystem();
		$path = $backup->backup_folder();

		if ($uploadDrive) {
			$drive = $this->BackupDrive($path);
			dd($drive);
		}

		dd($path);
	}

	public function BackupDatabase($uploadDrive = NULL)
	{
		$backup = new BackupSystem();
		$path = $backup->backup_database();

		if ($uploadDrive) {
			$drive = $this->BackupDrive($path);
			dd($drive);
		}

		dd($path);
	}

	public function BackupDrive($path = NULL)
	{
		if (!empty($path)) {
			$drive = new GD();

			$file_id = $drive->uploadFile($path);
			$file_url = 'https://drive.google.com/file/d/' . $file_id . '/view'; // Get the URL of the uploaded file
			return ['file_id' => $file_id, 'file_url' => $file_url];
		} else {
			dd('Please specify the path to upload!');
		}
	}
}
