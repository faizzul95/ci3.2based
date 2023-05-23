<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class UserPasswordReset_model extends CT_Model
{
	public $table = 'users_password_reset';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'user_id',
		'email',
		'reset_token',
		'reset_token_expired'
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

	public function getUserPasswordResetListDt()
	{
		$serverside = serversideDT();
		$serverside->query("SELECT 
		`user_id`,
		`email`,
		`reset_token`,
		`reset_token_expired`,
		`id`
		FROM {$this->table} 
		ORDER BY {$this->primary_key} {$this->order}");

		// $serverside->hide(''); // hides column from the output

		$serverside->edit("user_id", function ($data) {
			return purify($data["user_id"]);
		});

		$serverside->edit("email", function ($data) {
			return purify($data["email"]);
		});

		$serverside->edit("reset_token", function ($data) {
			return purify($data["reset_token"]);
		});

		$serverside->edit("reset_token_expired", function ($data) {
			return purify($data["reset_token_expired"]);
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
