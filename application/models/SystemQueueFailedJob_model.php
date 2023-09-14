<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class SystemQueueFailedJob_model extends CT_Model
{
	public $table = 'system_queue_failed_job';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'uuid',
		'type',
		'payload',
		'company_id',
		'exception',
		'failed_at'
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

	public function getSystemQueueFailedJobListDt()
	{
		$serverside = serversideDT();
		$serverside->query("SELECT 
		`uuid`,
		`type`,
		`payload`,
		`company_id`,
		`exception`,
		`failed_at`,
		`id`
		FROM {$this->table} 
		ORDER BY {$this->primary_key} {$this->order}");

		// $serverside->hide(''); // hides column from the output

		$serverside->edit("uuid", function ($data) {
			return purify($data["uuid"]);
		});

		$serverside->edit("type", function ($data) {
			return purify($data["type"]);
		});

		$serverside->edit("payload", function ($data) {
			$dataDecode = json_decode($data['payload'], true);

			if (hasData($dataDecode)) {
				return 'Logic replace here';
			} else {
				return 'No payload detected';
			}
		});

		$serverside->edit("company_id", function ($data) {
			return purify($data["company_id"]);
		});

		$serverside->edit("exception", function ($data) {
			return purify($data["exception"]);
		});

		$serverside->edit("failed_at", function ($data) {
			return purify($data["failed_at"]);
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
