<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class MasterEmailTemplates_model extends CT_Model
{
	public $table = 'master_email_templates';
	public $primary_key = 'email_id';
	public $order = 'DESC';

	// the fields that can be filled by insert/update
	public $fillable = [
		'email_type',
		'email_subject',
		'email_body',
		'email_footer',
		'email_cc',
		'email_bcc',
		'email_status',
	];

	// the fields that cannot be filled by insert/update
	public $protected = ['email_id'];

	// expected created_at & updated_at column
	public $timestamps = TRUE;

	public function __construct()
	{
		// returns the result: as 'array' or as 'object'. the default value is 'object'
		$this->return_as = 'array';

		parent::__construct();
		$this->abilities = parent::permission(['email-template-insert', 'email-template-update', 'email-template-delete']);
	}

	###################################################################
	#                                                                 #
	#               Start custom function below                       #
	#                                                                 #
	###################################################################
}
