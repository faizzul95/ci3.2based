<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class User_model extends CT_Model
{
	public $table = 'users';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'name',
		'user_preferred_name',
		'user_staff_no',
		'user_nric_visa',
		'email',
		'user_contact_no',
		'user_gender',
		'user_dob',
		'username',
		'password',
		'user_marital_status',
		'user_status',
		'social_id',
		'social_type',
		'two_factor_status',
		'two_factor_type',
		'two_factor_secret',
		'two_factor_recovery_codes',
		'remember_token',
		'joined_date',
		'resigned_date',
		'resigned_reason',
		'email_verified_at',
		'company_id'
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['id'];

	// expected created_at & updated_at column
	public $timestamps = TRUE;

	// relationship ONE TO ONE
	public $has_one = [
		'company' => ['Company_model', 'id', 'company_id'],
		'main_profile' => ['UserProfile_model', 'user_id', 'id'],
		'current_profile' => ['UserProfile_model', 'user_id', 'id'],
	];

	// relationship ONE TO MANY
	public $has_many = [
		'profile' => ['UserProfile_model', 'user_id', 'id']
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

	public function getListUserDt($status = NULL, $roleID = NULL)
	{
		$roleQuery = (hasData($roleID)) ? 'LEFT JOIN `users_profile` `profile` ON `user`.`user_id`=`profile`.`user_id`
                    WHERE `profile`.`role_id` = ' . escape($roleID) : '';

		$filterQuery = NULL;

		if (hasData($status) || $status === '0') {
			if (hasData($roleQuery)) {
				$filterQuery = $roleQuery . ' AND `user`.`user_status` = ' . escape($status);
			} else {
				$filterQuery = 'WHERE `user`.`user_status` = ' . escape($status);
			}
		} else if (hasData($roleQuery)) {
			$filterQuery = $roleQuery . ' AND `user`.`user_status` != 3';
		} else {
			$filterQuery = 'WHERE `user`.`user_status` != 3'; // dont show deleted data
		}

		$serverside = serversideDT();
		$serverside->query("SELECT 
		`name`,
		`user_preferred_name`,
		`user_staff_no`,
		`user_nric_visa`,
		`email`,
		`user_contact_no`,
		`user_gender`,
		`user_dob`,
		`username`,
		`password`,
		`user_marital_status`,
		`user_status`,
		`social_id`,
		`social_type`,
		`two_factor_status`,
		`two_factor_type`,
		`two_factor_secret`,
		`two_factor_recovery_codes`,
		`remember_token`,
		`joined_date`,
		`email_verified_at`,
		`company_id`,
		`id`
		FROM {$this->table} 
		ORDER BY {$this->primary_key} {$this->order}");

		// $serverside->hide(''); // hides column from the output

		$serverside->edit("name", function ($data) {
			return purify($data["name"]);
		});

		$serverside->edit("user_preferred_name", function ($data) {
			return purify($data["user_preferred_name"]);
		});

		$serverside->edit("user_staff_no", function ($data) {
			return purify($data["user_staff_no"]);
		});

		$serverside->edit("user_nric_visa", function ($data) {
			return purify($data["user_nric_visa"]);
		});

		$serverside->edit("email", function ($data) {
			return purify($data["email"]);
		});

		$serverside->edit("user_contact_no", function ($data) {
			return purify($data["user_contact_no"]);
		});

		$serverside->edit("user_gender", function ($data) {
			return purify($data["user_gender"]);
		});

		$serverside->edit("user_dob", function ($data) {
			return purify($data["user_dob"]);
		});

		$serverside->edit("username", function ($data) {
			return purify($data["username"]);
		});

		$serverside->edit("password", function ($data) {
			return purify($data["password"]);
		});

		$serverside->edit("user_marital_status", function ($data) {
			return purify($data["user_marital_status"]);
		});

		$serverside->edit("user_status", function ($data) {
			return purify($data["user_status"]);
		});

		$serverside->edit("social_id", function ($data) {
			return purify($data["social_id"]);
		});

		$serverside->edit("social_type", function ($data) {
			return purify($data["social_type"]);
		});

		$serverside->edit("two_factor_status", function ($data) {
			return purify($data["two_factor_status"]);
		});

		$serverside->edit("two_factor_type", function ($data) {
			return purify($data["two_factor_type"]);
		});

		$serverside->edit("two_factor_secret", function ($data) {
			return purify($data["two_factor_secret"]);
		});

		$serverside->edit("two_factor_recovery_codes", function ($data) {
			return purify($data["two_factor_recovery_codes"]);
		});

		$serverside->edit("remember_token", function ($data) {
			return purify($data["remember_token"]);
		});

		$serverside->edit("joined_date", function ($data) {
			return purify($data["joined_date"]);
		});

		$serverside->edit("email_verified_at", function ($data) {
			return purify($data["email_verified_at"]);
		});

		$serverside->edit("company_id", function ($data) {
			return purify($data["company_id"]);
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

	public function getSpecificUser($param = NULL)
	{
		return $this->db->where('id', $param)->or_where('email', $param)->or_where('username', $param)->get($this->table)->row_array();
	}
}
