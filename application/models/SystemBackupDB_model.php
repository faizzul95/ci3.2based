<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class SystemBackupDB_model extends CT_Model
{
	public $table = 'system_backup_db';
	public $primary_key = 'backup_id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'backup_name',
		'backup_storage_type',
		'backup_location',
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['backup_id'];

	// expected created_at & updated_at column
	public $timestamps = TRUE;

	public function __construct()
	{
		// returns the result: as 'array' or as 'object'. the default value is 'object'
		$this->return_as = 'array';

		parent::__construct();
	}

	###################################################################
	#                                                                 #
	#               Start custom function below                       #
	#                                                                 #
	###################################################################

	public function getListBackupDbDt()
	{
		$serverside = serversideDT();
		$serverside->query("SELECT backup_name, backup_location, backup_storage_type, created_at, backup_id FROM {$this->table} ORDER BY {$this->primary_key} {$this->order}");

		$serverside->edit('backup_name', function ($data) {
			return purify($data['backup_name']);
		});

		$serverside->edit('backup_location', function ($data) {
			$loc = purify($data['backup_location']);
			$type = purify($data['backup_storage_type']);
			if ($type != 'local') {
				$loc = '<a href="' . $loc . '" target="_blank"> Preview Link </a>';
			}
			return $loc;
		});

		$serverside->edit('created_at', function ($data) {
			return formatDate($data['created_at'], 'd.m.Y h:i A');
		});

		$serverside->edit('backup_id', function ($data) {

			$del = '<button class="btn btn-outline-danger btn-sm waves-effect" onclick="deleteRecord(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="Delete"> <i class="tf-icons ti ti-trash ti-xs"></i> </button>';
			$email = '<button class="btn btn-outline-info btn-sm waves-effect" onclick="emailBackup(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="Email"> <i class="tf-icons ti ti-mail-forward ti-xs"></i> </button>';

			return "<center> $del $email </center>";
		});

		echo $serverside->generate();
	}
}
