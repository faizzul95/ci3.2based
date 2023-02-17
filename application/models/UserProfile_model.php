<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class UserProfile_model extends CT_Model
{
	public $table = 'user_profile';
	public $primary_key = 'profile_id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'user_id',
		'user_code_no',
		'role_id',
		'is_main',
		'store_id',
		'profile_status',
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['profile_id'];

	// expected created_at & updated_at column
	public $timestamps = TRUE;

	// relationship ONE TO ONE
	public $has_one = [
		'roles' => ['MasterRoles_model', 'role_id', 'role_id'],
		'avatar' => ['EntityFiles_model', 'entity_id', 'profile_id']
	];

	public function __construct()
	{
		// returns the result: as 'array' or as 'object'. the default value is 'object'
		$this->return_as = 'array';

		parent::__construct();
		$this->abilities = parent::permission(['profile-insert', 'profile-update', 'profile-delete']);
	}

	###################################################################
	#                                                                 #
	#               Start custom function below                       #
	#                                                                 #
	###################################################################

	public function getAllProfileByUserID($userID = NULL)
	{
		$this->db->join('master_roles role', 'role.role_id=up.role_id', 'left')
			->where('up.user_id', $userID)
			->order_by('up.role_id', "asc");

		return $this->db->get($this->table . ' up')->result_array();
	}

	public function getMainProfileByUserID($userID = NULL, $status = NULL)
	{
		$this->db->join('master_roles role', 'role.role_id=up.role_id', 'left')
			->where('up.user_id', $userID)
			->where('up.is_main', 1);

		if (!empty($status)) {
			$this->db->where('up.profile_status', $status);
		}

		return $this->db->get($this->table . ' up')->row_array();
	}

	public function getProfileByProfileID($profileID = NULL)
	{
		return $this->db->join('master_role role', 'role.role_id=up.role_id', 'left')
			->where('up.profile_id', $profileID)
			->get($this->table . ' up')
			->row_array();
	}
}
