<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['client_id'] = 'ENTER_YOUR_ID';
$config['client_secret'] = 'ENTER_YOUR_SECRET';
$config['redirect_uri'] = url('cron/backup');
$config['credentials_file_path'] = APPPATH . 'credentials.json';
$config['folder_id'] = [
	'database' => 'ENTER_YOUR_FOLDER_ID',
	'system' => 'ENTER_YOUR_FOLDER_ID',
];
