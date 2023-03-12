<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_entity_files extends CI_Migration
{
	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->table_name = 'entity_files';
	}

	public function up()
	{
		$this->dbforge->add_field([
			'files_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
			'files_name' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'files_original_name' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'files_type' => ['type' => 'VARCHAR', 'constraint' => '150', 'null' => TRUE, 'comment' => ''],
			'files_mime' => ['type' => 'VARCHAR', 'constraint' => '150', 'null' => TRUE, 'comment' => ''],
			'files_extension' => ['type' => 'VARCHAR', 'constraint' => '10', 'null' => TRUE, 'comment' => ''],
			'files_size' => ['type' => 'INT', 'constraint' => '11', 'null' => TRUE, 'default' => 0, 'comment' => ''],
			'files_compression' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => 1, 'comment' => '1 = full size only, 2 = full size & compressed, 3 = full size, compressed & thumbnail'],
			'files_folder' => ['type' => 'VARCHAR', 'constraint' => '150', 'null' => TRUE, 'comment' => ''],
			'files_path' => ['type' => 'VARCHAR', 'constraint' => '250', 'null' => TRUE, 'comment' => ''],
			'files_path_is_url' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => 0, 'comment' => ''],
			'files_description' => ['type' => 'VARCHAR', 'constraint' => '250', 'null' => TRUE, 'comment' => ''],
			'entity_type' => ['type' => 'VARCHAR', 'constraint' => '250', 'null' => TRUE, 'comment' => ''],
			'entity_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'null' => TRUE, 'comment' => ''],
			'entity_file_type' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'user_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'null' => TRUE, 'comment' => 'Refer table users'],
			'created_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'updated_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
		]);

		$this->dbforge->add_key('files_id', TRUE);
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
