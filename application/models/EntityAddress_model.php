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
}
