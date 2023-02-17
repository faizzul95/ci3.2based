<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_menu extends CI_Migration
{
	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->table_name = 'menu';
	}

	public function up()
	{
		$this->dbforge->add_field([
			'menu_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
			'menu_title' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'menu_description' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'menu_url' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'menu_order' => ['type' => 'TINYINT', 'constraint' => '2', 'null' => TRUE, 'comment' => ''],
			'menu_icon' => ['type' => 'VARCHAR', 'constraint' => '150', 'null' => TRUE, 'comment' => ''],
			'is_main_menu' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'null' => TRUE, 'default' => '0', 'comment' => ''],
			'menu_location' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => '1', 'comment' => '0 - side, 1 - main menu'],
			'is_active' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => '1', 'comment' => '1 - active, 0 - inactive'],
			'created_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'updated_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
		]);

		$this->dbforge->add_key('menu_id', TRUE);
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
