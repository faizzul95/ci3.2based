<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_master_role extends CI_Migration
{
	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->table_name = 'master_roles';
	}

	public function up()
	{
		$this->dbforge->add_field([
			'role_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
			'role_name' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE, 'comment' => ''],
			'role_code' => ['type' => 'VARCHAR', 'constraint' => '10', 'null' => TRUE, 'comment' => ''],
			'role_status' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'comment' => '0-Inactive, 1-Active'],
			'created_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'updated_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
		]);

		$this->dbforge->add_key('role_id', TRUE);
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
				'role_name'		=> 'SUPER ADMINISTRATOR',
				'role_code'		=> 'PROGRAMMER',
				'role_status'	=> '1',
				'created_at'	=> timestamp(),
			],
			[
				'role_name'		=> 'ADMINISTRATOR',
				'role_code'		=> 'ADMIN',
				'role_status'	=> '1',
				'created_at'	=> timestamp(),
			],
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
