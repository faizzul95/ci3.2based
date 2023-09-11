<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class SystemQueueJob_model extends CT_Model
{
	public $table = 'system_queue_job';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'uuid',
		'type',
		'payload',
		'attempt',
		'status',
		'message',
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

	public function getJob()
	{
		return $this->db->where_in('status', [1, 2, 4])->get($this->table)->row_array();
	}
}
