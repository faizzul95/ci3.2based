<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_user_profile extends CI_Migration
{
	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->table_name = 'user_profile';
	}

	public function up()
	{
		$this->dbforge->add_field([
			'profile_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
			'user_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'null' => TRUE, 'comment' => 'Refer table user'],
			'role_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'null' => TRUE, 'comment' => 'Refer table master_role'],
			'is_main' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => 0, 'comment' => '0-NO, 1-YES'],
			'profile_status' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => 1, 'comment' => '0-Inactive, 1-Active'],
			'created_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'updated_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
		]);

		$this->dbforge->add_key('profile_id', TRUE);
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
				'user_id'	  => '1',
				'role_id'	  => '1',
				'is_main'	  => '1',
				'created_at'  => timestamp(),
			],
			[
				'user_id'	  => '1',
				'role_id'	  => '2',
				'is_main'	  => '0',
				'created_at'  => timestamp(),
			],
		];

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
