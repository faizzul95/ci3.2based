<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_menu_abilities extends CI_Migration
{
	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->table_name = 'menu_abilities';
	}

	public function up()
	{
		$this->dbforge->add_field([
			'abilities_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
			'menu_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'null' => TRUE, 'comment' => 'Refer table menu'],
			'abilities_name' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE, 'comment' => ''],
			'abilities_slug' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE, 'comment' => ''],
			'abilities_desc' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'only_owned' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'created_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'updated_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
		]);

		$this->dbforge->add_key('abilities_id', TRUE);
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
