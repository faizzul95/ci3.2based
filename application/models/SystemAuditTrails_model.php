<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class SystemAuditTrails_model extends CT_Model
{
	public $table = 'system_audit_trails';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'user_id',
		'role_id',
		'user_fullname',
		'event',
		'table_name',
		'old_values',
		'new_values',
		'url',
		'ip_address',
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
	}

	###################################################################
	#                                                                 #
	#               Start custom function below                       #
	#                                                                 #
	###################################################################

	public function getSystemAuditTrailsListDt()
	{
		$serverside = serversideDT();
		$serverside->query("SELECT 
		`user_id`,
		`role_id`,
		`user_fullname`,
		`event`,
		`table_name`,
		`old_values`,
		`new_values`,
		`url`,
		`ip_address`,
		`user_agent`,
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

		$serverside->edit("user_fullname", function ($data) {
			return purify($data["user_fullname"]);
		});

		$serverside->edit("event", function ($data) {
			return purify($data["event"]);
		});

		$serverside->edit("table_name", function ($data) {
			return purify($data["table_name"]);
		});

		$serverside->edit("old_values", function ($data) {
			return purify($data["old_values"]);
		});

		$serverside->edit("new_values", function ($data) {
			return purify($data["new_values"]);
		});

		$serverside->edit("url", function ($data) {
			return purify($data["url"]);
		});

		$serverside->edit("ip_address", function ($data) {
			return purify($data["ip_address"]);
		});

		$serverside->edit("user_agent", function ($data) {
			return purify($data["user_agent"]);
		});

		$serverside->edit('id', function ($data) {
			$del = $edit = ''; // set default
			$del = '<button class="btn btn-outline-danger btn-sm waves-effect" onclick="deleteRecord(' . $data[$this->primary_key] . ')" data-id="' . $data[$this->primary_key] . '" title="Delete"> <i class="tf-icons ti ti-trash ti-xs"></i> </button>';
			$edit = '<button class="btn btn-outline-info btn-sm waves-effect" onclick="updateRecord(' . $data[$this->primary_key] . ')" title="Update"><i class="fa fa-edit"></i> </button>';

			return "<center> $del $edit </center>";
		});

		echo $serverside->generate();
	}
}
