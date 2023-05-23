<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class UserAuthHistory_model extends CT_Model
{
	public $table = 'users_login_history';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'user_id',
		'ip_address',
		'login_type',
		'operating_system',
		'browsers',
		'time',
		'user_agent'
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
		$this->abilities = permission([]);
	}

	###################################################################
	#                                                                 #
	#               Start custom function below                       #
	#                                                                 #
	###################################################################

	public function getUserAuthHistoryListDt()
	{
		$serverside = serversideDT();
		$serverside->query("SELECT 
		`user_id`,
 `ip_address`,
 `log_type`,
 `operating_system`,
 `browsers`,
 `time`,
 `user_agent`,
		`id`
		FROM {$this->table} 
		ORDER BY {$this->primary_key} {$this->order}");

		// $serverside->hide(''); // hides column from the output

		$serverside->edit("user_id", function ($data) {
			return purify($data["user_id"]);
		});

		$serverside->edit("ip_address", function ($data) {
			return purify($data["ip_address"]);
		});

		$serverside->edit("log_type", function ($data) {
			return purify($data["log_type"]);
		});

		$serverside->edit("operating_system", function ($data) {
			return purify($data["operating_system"]);
		});

		$serverside->edit("browsers", function ($data) {
			return purify($data["browsers"]);
		});

		$serverside->edit("time", function ($data) {
			return purify($data["time"]);
		});

		$serverside->edit("user_agent", function ($data) {
			return purify($data["user_agent"]);
		});

		$serverside->edit('id', function ($data) {
			$del = $edit = '';

			if ($this->abilities[''])
				$del = '<button class="btn btn-outline-danger btn-sm waves-effect" onclick="deleteRecord(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="Delete"> <i class="tf-icons ti ti-trash ti-xs"></i> </button>';

			if ($this->abilities[''])
				$edit = '<button class="btn btn-outline-info btn-sm waves-effect" onclick="updateRecord(' . $data[$this->primary_key] . ')" title="Update"><i class="fa fa-edit"></i> </button>';

			return "<center> $del $edit </center>";
		});

		echo $serverside->generate();
	}
}
