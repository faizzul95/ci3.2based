<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class UserAuthAttempt_model extends CT_Model
{
	public $table = 'user_login_attempt';
	public $primary_key = 'attempt_id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'user_id',
		'ip_address',
		'time',
		'user_agent'
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['attempt_id'];

	// expected created_at & updated_at column
	public $timestamps = TRUE;

	private $allow_attempt_count = 5;

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

	public function login_attempt_exceeded($userid)
	{
		$query = $this->db->where('user_id', $userid)
			->where('ip_address', $this->input->ip_address())
			->where('time > NOW() - INTERVAL 10 MINUTE')
			->get($this->table);

		return [
			'isExceed' => !($this->allow_attempt_count <= $query->num_rows()),
			'count' => $query->num_rows()
		];
	}

	public function clear_login_attempts($userid)
	{
		return $this->db->where('user_id', $userid)->where('ip_address', $this->input->ip_address())->delete($this->table);
	}
}
