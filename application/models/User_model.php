<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class User_model extends CT_Model
{
	public $table = 'user';
	public $primary_key = 'user_id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'name',
		'user_preferred_name',
		'user_nric_visa',
		'email',
		'user_contact_no',
		'user_gender',
		'user_dob',
		'user_username',
		'user_password',
		'user_status',
		'social_id',
		'social_type',
		'two_factor_status',
		'two_factor_type',
		'two_factor_secret',
		'email_verified_at',
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['user_id'];

	// expected created_at & updated_at column
	public $timestamps = TRUE;

	// relationship ONE TO ONE
	public $has_one = [
		'main_profile' => ['UserProfile_model', 'user_id', 'user_id'],
	];

	// relationship ONE TO MANY
	public $has_many = [
		'profile' => ['UserProfile_model', 'user_id', 'user_id']
	];

	public function __construct()
	{
		// returns the result: as 'array' or as 'object'. the default value is 'object'
		$this->return_as = 'array';

		parent::__construct();
		$this->abilities = parent::permission(['user-insert', 'user-update', 'user-delete']);
	}

	###################################################################
	#                                                                 #
	#               Start custom function below                       #
	#                                                                 #
	###################################################################

	public function getSpecificUser($param = NULL)
	{
		$this->db->where('user_id', $param)
			->or_where('email', $param)
			->or_where('user_contact_no', $param)
			->or_where('user_username', $param);

		return $this->db->get($this->table)->row_array();
	}
}
