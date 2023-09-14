<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class SystemBackupDB_model extends CT_Model
{
	public $table = 'system_backup_db';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'backup_name',
		'backup_storage_type',
		'backup_location'
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['id'];

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

	public function getSystemBackupDBListDt()
	{
		$serverside = serversideDT();
		$serverside->query("SELECT 
		`backup_name`,
		`backup_storage_type`,
		`backup_location`,
		`created_at`,
		`id`
		FROM {$this->table} 
		ORDER BY {$this->primary_key} {$this->order}");

		$serverside->hide('backup_storage_type'); // hides column from the output

		$serverside->edit("backup_name", function ($data) {
			return purify($data["backup_name"]);
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

		$serverside->edit('id', function ($data) {
			$del = $edit = ''; // set default
			$del = '<button class="btn btn-outline-danger btn-sm waves-effect" onclick="deleteRecord(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="Delete"> <i class="tf-icons ti ti-trash ti-xs"></i> </button>';
			$edit = '<button class="btn btn-outline-info btn-sm waves-effect" onclick="updateRecord(' . $data[$this->primary_key] . ')" title="Update"><i class="fa fa-edit"></i> </button>';

			return "<center> $del $edit </center>";
		});

		echo $serverside->generate();
	}
}
