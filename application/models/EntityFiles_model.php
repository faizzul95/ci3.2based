<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class EntityFiles_model extends CT_Model
{
	public $table = 'entity_files';
	public $primary_key = 'id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'files_name',
		'files_original_name',
		'files_type',
		'files_mime',
		'files_extension',
		'files_size',
		'files_compression',
		'files_folder',
		'files_path',
		'files_path_is_url',
		'files_description',
		'entity_type',
		'entity_id',
		'entity_file_type',
		'user_id',
		'company_id'
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

	public function getEntityFilesListDt()
	{
		$serverside = serversideDT();
		$serverside->query("SELECT 
		`files_name`,
		`files_original_name`,
		`files_type`,
		`files_mime`,
		`files_extension`,
		`files_size`,
		`files_compression`,
		`files_folder`,
		`files_path`,
		`files_path_is_url`,
		`files_description`,
		`entity_type`,
		`entity_id`,
		`entity_file_type`,
		`user_id`,
		`company_id`,
		`id`
		FROM {$this->table} 
		ORDER BY {$this->primary_key} {$this->order}");

		// $serverside->hide(''); // hides column from the output

		$serverside->edit("files_name", function ($data) {
			return purify($data["files_name"]);
		});

		$serverside->edit("files_original_name", function ($data) {
			return purify($data["files_original_name"]);
		});

		$serverside->edit("files_type", function ($data) {
			return purify($data["files_type"]);
		});

		$serverside->edit("files_mime", function ($data) {
			return purify($data["files_mime"]);
		});

		$serverside->edit("files_extension", function ($data) {
			return purify($data["files_extension"]);
		});

		$serverside->edit("files_size", function ($data) {
			return purify($data["files_size"]);
		});

		$serverside->edit("files_compression", function ($data) {
			return purify($data["files_compression"]);
		});

		$serverside->edit("files_folder", function ($data) {
			return purify($data["files_folder"]);
		});

		$serverside->edit("files_path", function ($data) {
			return purify($data["files_path"]);
		});

		$serverside->edit("files_path_is_url", function ($data) {
			return purify($data["files_path_is_url"]);
		});

		$serverside->edit("files_description", function ($data) {
			return purify($data["files_description"]);
		});

		$serverside->edit("entity_type", function ($data) {
			return purify($data["entity_type"]);
		});

		$serverside->edit("entity_id", function ($data) {
			return purify($data["entity_id"]);
		});

		$serverside->edit("entity_file_type", function ($data) {
			return purify($data["entity_file_type"]);
		});

		$serverside->edit("user_id", function ($data) {
			return purify($data["user_id"]);
		});

		$serverside->edit("company_id", function ($data) {
			return purify($data["company_id"]);
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
