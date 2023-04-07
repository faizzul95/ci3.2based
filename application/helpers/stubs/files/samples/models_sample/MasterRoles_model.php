<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class MasterRoles_model extends CT_Model
{
	public $table = 'master_roles';
	public $primary_key = 'role_id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'role_name',
		'role_code',
		'role_scope',
		'role_status',
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['role_id'];

	// expected created_at & updated_at column
	public $timestamps = TRUE;

	public function __construct()
	{
		// returns the result: as 'array' or as 'object'. the default value is 'object'
		$this->return_as = 'array';

		parent::__construct();
		$this->abilities = parent::permission(['roles-insert', 'roles-update', 'roles-delete']);
	}

	###################################################################
	#                                                                 #
	#               Start custom function below                       #
	#                                                                 #
	###################################################################
}
