<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_system_access_tokens extends CI_Migration
{
	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->table_name = 'system_access_tokens';
	}

	public function up()
	{
		$this->dbforge->add_field([
			'id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
			'tokenable_type' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'null' => TRUE, 'comment' => ''],
			'tokenable_id' => ['type' => 'VARCHAR', 'constraint' => '250', 'null' => TRUE, 'comment' => ''],
			'name' => ['type' => 'VARCHAR', 'constraint' => '250', 'null' => TRUE, 'comment' => ''],
			'token' => ['type' => 'VARCHAR', 'constraint' => '64', 'null' => TRUE, 'comment' => ''],
			'abilities' => ['type' => 'LONGTEXT', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'last_used_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
		]);

		$this->dbforge->add_key('id', TRUE);
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
		$data = [];

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
