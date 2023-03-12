<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class EntityFiles_model extends CT_Model
{
	public $table = 'entity_files';
	public $primary_key = 'files_id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'files_name',
		'files_original_name',
		'files_folder',
		'files_type',
		'files_mime',
		'files_extension',
		'files_size',
		'files_compression',
		'files_description',
		'files_path',
		'files_path_is_url',
		'entity_type',
		'entity_id',
		'entity_file_type',
		'user_id',
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['files_id'];

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
