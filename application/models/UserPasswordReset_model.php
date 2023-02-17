<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class UserPasswordReset_model extends CT_Model
{
	public $table = 'user_password_reset';
	public $primary_key = 'reset_id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'user_id',
		'email',
		'reset_token',
		'reset_token_expired'
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['reset_id'];

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
}
