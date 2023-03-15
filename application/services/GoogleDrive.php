<?php

namespace App\services;

defined('BASEPATH') or exit('No direct script access allowed');

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Exception;

class GoogleDrive
{
	protected $CI;
	private $client;
	private $service;

	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->config('google_drive');

		// Set up the Google API client
		$this->client = new Client();
		$this->client->setClientId($this->CI->config->item('client_id'));
		$this->client->setClientSecret($this->CI->config->item('client_secret'));
		$this->client->setRedirectUri($this->CI->config->item('redirect_uri'));
		$this->client->setAccessType('offline');
		// $this->client->setApprovalPrompt('force');
		// $this->client->setPrompt('select_account consent');
		$this->client->setIncludeGrantedScopes(true);
		$this->client->addScope(Drive::DRIVE_FILE);

		// Get authorization code from Google
		$code = $this->CI->input->get('code');
		if (!empty($code)) {
			$this->client->fetchAccessTokenWithAuthCode($code);
			$this->saveAccessToken($this->client->getAccessToken());
			header('Location: ' . filter_var($this->CI->config->item('redirect_uri'), FILTER_SANITIZE_URL), TRUE, 301);
			exit;
		}

		// Authenticate the user and get an access token
		$this->getAccessToken();

		// Check if token expired
		if ($this->client->isAccessTokenExpired()) {
			// save refresh token to some variable
			$refreshTokenSaved = $this->client->getRefreshToken();

			// update access token
			$this->client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);

			// pass access token to some variable
			$accessTokenUpdated = $this->client->getAccessToken();

			// append refresh token
			$accessTokenUpdated['refresh_token'] = $refreshTokenSaved;

			// Set the new acces token
			$accessToken = $refreshTokenSaved;
			$this->client->setAccessToken($accessToken);

			// save to file
			$this->saveAccessToken($accessTokenUpdated);

			// set new token
			$this->getAccessToken();
		}

		// Set up the Google Drive service
		$this->service = new Drive($this->client);
	}

	public function getAccessToken()
	{
		// Check if an access token already exists in the database or file
		// If yes, return the access token
		// If not, authenticate the user and get a new access token

		$accessToken = '';

		// Code to get the access token from the database or file goes here
		$credentials_file_path = $this->CI->config->item('credentials_file_path');

		if (file_exists($credentials_file_path)) {
			$accessToken = file_get_contents($credentials_file_path);
		} else {
			// Authenticate the user and get a new access token
			$authUrl = $this->client->createAuthUrl();
			header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
		}

		// Authenticate the user and get an access token
		$this->client->setAccessToken($accessToken);

		return $accessToken;
	}

	public function saveAccessToken($accessToken)
	{
		// Code to save the access token to the database or file goes here
		$credentials_file_path = $this->CI->config->item('credentials_file_path');

		if (file_exists($credentials_file_path))
			unlink($credentials_file_path);

		$json = json_encode($accessToken);
		file_put_contents($credentials_file_path, $json);
	}

	public function uploadFile($backup_file)
	{
		if (!empty($this->getAccessToken())) {
			if (file_exists($backup_file)) {

				// Check if backup folder exists, create if necessary
				date_default_timezone_set('Asia/Kuala_Lumpur');
				$backup_folder_name = date('Y-m-d');
				$folderID = $this->checkFolder($backup_folder_name);

				try {

					// Upload the backup file to Google Drive
					$file_metadata = new DriveFile(array(
						'name' => basename($backup_file),
						'parents' => [$folderID]
					));

					$file = $this->service->files->create($file_metadata, array(
						'data' => file_get_contents($backup_file),
						'mimeType' => 'application/zip',
						'uploadType' => 'multipart',
						'fields' => 'id, webViewLink'
					));

					// Delete the backup file from the server
					unlink($backup_file);

					$res = ['resCode' => 200, 'message' => NULL, 'data' => $file];
				} catch (\Exception $e) {
					$res = ['resCode' => 400, 'message' => $e->getMessage(), 'data' => NULL];
				}

				return $res;
			} else {
				return ['resCode' => 400, 'message' => 'File to upload is does not exist', 'data' => NULL];
			}
		}
	}

	private function checkFolder($folder_name)
	{
		$folder_id = '';
		$results = $this->service->files->listFiles(array(
			'q' => "mimeType='application/vnd.google-apps.folder' and trashed=false",
			'fields' => 'nextPageToken, files(id, name)'
		));

		date_default_timezone_set('Asia/Kuala_Lumpur');
		foreach ($results->getFiles() as $file) {
			$folderDriveName = $file->getName();

			// format folder name
			$dateFolder = date('Y-m-d', strtotime($folderDriveName));

			// Check if the date is exactly one month ago
			if (date('Y-m-d', strtotime('-1 month')) == $dateFolder) {
				$this->service->files->delete($file->getId()); // Delete the folder
			}

			if ($folderDriveName == $folder_name) {
				$folder_id = $file->getId();
			}
		}

		if (empty($folder_id)) {
			$folder_id = $this->createDriveFolder($folder_name);
		}

		return $folder_id;
	}

	public function createDriveFolder($backup_folder_name)
	{
		$backup_folder_metadata = new DriveFile(array(
			'name' => $backup_folder_name,
			'parents' => [$this->CI->config->item('folder_id')],
			'mimeType' => 'application/vnd.google-apps.folder'
		));

		$backup_folder = $this->service->files->create($backup_folder_metadata, array(
			'fields' => 'id'
		));

		return $backup_folder->id;
	}
}
