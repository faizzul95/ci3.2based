<?php

namespace App\services;

defined('BASEPATH') or exit('No direct script access allowed');

class BackupSystem
{
	protected $CI;

	public function __construct()
	{
		$this->CI = &get_instance();

		// Load the database and Zip libraries
		$this->CI->load->helper('file', 'text', 'form', 'string', 'download');
		$this->CI->load->dbutil();
		$this->CI->load->library('zip');
	}

	public function backup_folder()
	{
		ini_set("memory_limit", "1G");

		// Set the name of the archive file
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$filename = date("d_m_Y_h_i_s_A") . '_system.zip';

		// Set the directory of the archive file
		$backup_dir = 'public' . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR;
		if (!file_exists($backup_dir)) {
			mkdir($backup_dir, 0755, true);
		}

		$directory = $backup_dir . $filename;

		// Get the absolute path to the project folder
		$app_path = realpath(APPPATH);

		// Get the absolute path to the parent directory of the application directory
		$path = realpath($app_path . '/../') . DIRECTORY_SEPARATOR;

		$exclude = array(
			'#bck',
			'blade_cache',
			'ci_sessions',
			'template',
			'backup',
			'node_modules',
			'vendor',
			'README.md',
			'package-lock.json',
			'composer.lock',
			'.git',
			'.editorconfig',
		);

		// Add all files and folders in the project folder to the archive,
		// except for the excluded files and folders
		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($path),
			\RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file) {
			// Check if the file or folder is in the exclusion list
			$exclude_file = false;
			foreach ($exclude as $excluded_item) {
				if (strpos($name, $excluded_item) !== false) {
					$exclude_file = true;
					break;
				}
			}

			if (!$exclude_file) {
				// Add the file or folder to the archive
				$pathName = str_replace($path, '', $name);

				if (!in_array(basename($name), ['.', '..', '...', '....'])) {
					$this->CI->zip->add_data($pathName, file_get_contents($name));
				}
			}
		}

		// Save the archive file to the server
		$this->CI->zip->archive($directory);

		ini_set("memory_limit", "256M");

		return $directory;
	}

	public function backup_database()
	{
		ini_set("memory_limit", "1G");

		// Set the backup filename and directory
		$directory = 'public' . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR;

		if (!file_exists($directory)) {
			mkdir($directory, 0755, true);
		}

		date_default_timezone_set('Asia/Kuala_Lumpur');
		$filename = date("d_m_Y_h_i_s_A") . '_db.zip';

		// Backup the database and store it in a variable
		$prefs = array(
			'ignore' => array('system_backup_db'), // List of tables to omit from the backup
			'format' => 'zip', // gzip, zip, txt
			'filename' => $filename, // File name - NEEDED ONLY WITH ZIP FILES
			'add_drop' => TRUE, // Whether to add DROP TABLE statements to backup file
			'add_insert' => TRUE, // Whether to add INSERT data to backup file
			'newline' => "\n" // Newline character used in backup file
		);

		// Backup your entire database 
		$backup = $this->CI->dbutil->backup($prefs);

		// Write the backup file to the server
		$file_path = $directory . $filename;

		write_file($file_path, $backup);

		ini_set("memory_limit", "256M");

		return $file_path;
	}
}
