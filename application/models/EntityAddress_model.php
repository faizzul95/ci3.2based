<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class EntityAddress_model extends CT_Model
{
	public $table = 'entity_address';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'entity_type',
		'entity_id',
		'entity_address_type',
		'address_1',
		'address_2',
		'city_name',
		'state_name',
		'postcode',
		'country_name'
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

	public function getEntityAddressListDt()
	{
		$serverside = serversideDT();
		$serverside->query("SELECT 
		`entity_type`,
		`entity_id`,
		`entity_address_type`,
		`address_1`,
		`address_2`,
		`city_name`,
		`state_name`,
		`postcode`,
		`country_name`,
		`id`
		FROM {$this->table} 
		ORDER BY {$this->primary_key} {$this->order}");

		// $serverside->hide(''); // hides column from the output

		$serverside->edit("entity_type", function ($data) {
			return purify($data["entity_type"]);
		});

		$serverside->edit("entity_id", function ($data) {
			return purify($data["entity_id"]);
		});

		$serverside->edit("entity_address_type", function ($data) {
			return purify($data["entity_address_type"]);
		});

		$serverside->edit("address_1", function ($data) {
			return purify($data["address_1"]);
		});

		$serverside->edit("address_2", function ($data) {
			return purify($data["address_2"]);
		});

		$serverside->edit("city_name", function ($data) {
			return purify($data["city_name"]);
		});

		$serverside->edit("state_name", function ($data) {
			return purify($data["state_name"]);
		});

		$serverside->edit("postcode", function ($data) {
			return purify($data["postcode"]);
		});

		$serverside->edit("country_name", function ($data) {
			return purify($data["country_name"]);
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
