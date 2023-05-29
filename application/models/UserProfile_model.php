<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class UserProfile_model extends CT_Model
{
	public $table = 'users_profile';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'user_id',
		'roles_id',
		'is_main',
		'company_id',
		'department_id',
		'profile_status'
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['id'];

	// expected created_at & updated_at column
	public $timestamps = TRUE;

	// relationship ONE TO ONE
	public $has_one = [
		'company' => ['Company_model', 'id', 'company_id'],
		'roles' => ['CompanyProfileRoles_model', 'id', 'roles_id'],
		'department' => ['CompanyDepartment_model', 'id', 'department_id'],
		'avatar' => ['EntityFiles_model', 'entity_id', 'id'],
		'profileHeader' => ['EntityFiles_model', 'entity_id', 'id'],
		'users' => ['User_model', 'id', 'user_id'],
	];

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

	public function getUserProfileListDt()
	{
		$serverside = serversideDT();
		$serverside->query("SELECT 
		`user_id`,
		`role_id`,
		`is_main`,
		`department_id`,
		`profile_status`,
		`id`
		FROM {$this->table} 
		ORDER BY {$this->primary_key} {$this->order}");

		// $serverside->hide(''); // hides column from the output

		$serverside->edit("user_id", function ($data) {
			return purify($data["user_id"]);
		});

		$serverside->edit("role_id", function ($data) {
			return purify($data["role_id"]);
		});

		$serverside->edit("is_main", function ($data) {
			return purify($data["is_main"]);
		});

		$serverside->edit("department_id", function ($data) {
			return purify($data["department_id"]);
		});

		$serverside->edit("profile_status", function ($data) {
			return purify($data["profile_status"]);
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
