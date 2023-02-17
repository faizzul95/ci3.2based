<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_master_email_templates extends CI_Migration
{
	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->table_name = 'master_email_templates';
	}

	public function up()
	{
		$this->dbforge->add_field([
			'email_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
			'email_type' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'email_subject' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'email_body' => ['type' => 'LONGTEXT', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'email_footer' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'email_cc' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'email_bcc' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'email_status' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => '1', 'comment' => '0-Inactive, 1-Active'],
			'created_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'updated_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
		]);

		$this->dbforge->add_key('email_id', TRUE);
		if ($this->dbforge->create_table($this->table_name, FALSE, ['ENGINE' => 'InnoDB', 'COLLATE' => 'utf8mb4_general_ci'])) {
			if (!isAjax()) {
				$this->seeder();
				echo "<p> <span style='color: blue;'><b> {$this->table_name} </b></span> successfully created! </p>";
			} else {
				echo "<b> {$this->table_name} </b></span> created";
			}
		}
	}

	public function down()
	{
		$this->dbforge->drop_table($this->table_name, TRUE);
		if (isAjax()) {
			echo "<b> {$this->table_name} </b></span> drop";
		}
	}

	public function seeder()
	{
		$data = [
			[
				'email_type'	=> 'FORGOT_PASSWORD',
				'email_subject'	=> env('APP_NAME') . ': Forgot Password',
				'email_body'	=> '<p>Dear %to%,</p><p> Please click this link to change your password :<p> URL : <a href="%url%"> CLICK HERE TO RESET </a> </p> <p><span style="color: rgb(0, 0, 0);">Thank you.</span></p>',
				'email_footer'	=> NULL,
				'email_cc'		=> NULL,
				'email_bcc'		=> NULL,
				'email_status'	=> 1,
				'created_at'  	=> timestamp(),
			]
		];

		if (!empty($data))
			$this->db->insert_batch($this->table_name, $data);

		if (isAjax()) {
			echo "<b> {$this->table_name} </b></span> seed";
		}
	}

	public function truncate()
	{
		if (isAjax()) {
			ci()->db->truncate($this->table_name);
			echo "<b> {$this->table_name} </b></span> truncate";
		}
	}
}
