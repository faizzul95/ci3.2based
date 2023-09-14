<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class SystemAccessTokens_model extends CT_Model
{
	public $table = 'system_access_tokens';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'tokenable_type',
		'tokenable_id',
		'name',
		'token',
		'abilities',
		'last_used_at'
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

	public function getSystemAccessTokensListDt()
	{
		$serverside = serversideDT();
		$serverside->query("SELECT 
		`tokenable_type`,
		`tokenable_id`,
		`name`,
		`token`,
		`abilities`,
		`last_used_at`,
		`id`
		FROM {$this->table} 
		ORDER BY {$this->primary_key} {$this->order}");

		// $serverside->hide(''); // hides column from the output

		$serverside->edit("tokenable_type", function ($data) {
			return purify($data["tokenable_type"]);
		});

		$serverside->edit("tokenable_id", function ($data) {
			return purify($data["tokenable_id"]);
		});

		$serverside->edit("name", function ($data) {
			return purify($data["name"]);
		});

		$serverside->edit("token", function ($data) {
			return purify($data["token"]);
		});

		$serverside->edit("abilities", function ($data) {
			return purify($data["abilities"]);
		});

		$serverside->edit("last_used_at", function ($data) {
			return purify($data["last_used_at"]);
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
