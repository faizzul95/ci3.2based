<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_user extends CI_Migration
{
	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->table_name = 'user';
	}

	public function up()
	{
		$this->dbforge->add_field([
			'user_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
			'name' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'user_preferred_name' => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => TRUE, 'comment' => ''],
			'user_nric_visa' => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => TRUE, 'comment' => ''],
			'email' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'user_contact_no' => ['type' => 'VARCHAR', 'constraint' => '15', 'null' => TRUE, 'comment' => ''],
			'user_gender' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'comment' => '1-Male, 2-Female'],
			'user_dob' => ['type' => 'DATE', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'user_username' => ['type' => 'VARCHAR', 'constraint' => '15', 'null' => TRUE, 'comment' => ''],
			'user_password' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'user_status' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => 4, 'comment' => '0-Inactive, 1-Active, 2-Suspended, 3-Deleted, 4-Unverified'],
			'social_id' => ['type' => 'VARCHAR', 'constraint' => '125', 'null' => TRUE, 'default' => '', 'comment' => ''],
			'social_type' => ['type' => 'VARCHAR', 'constraint' => '125', 'null' => TRUE, 'default' => '', 'comment' => ''],
			'two_factor_status' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => 0, 'comment' => '0-Disable, 1-Enable'],
			'two_factor_type' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'comment' => '1-Google Authenticator, 2-SMS (OTP), 3-Email'],
			'two_factor_secret' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'email_verified_at' => ['type' => 'DATE', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'created_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'updated_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'deleted_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
		]);

		$this->dbforge->add_key('user_id', TRUE);
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
				'name'	  			  => 'MOHD FAHMY IZWAN BIN ZULKHAFRI',
				'user_preferred_name' => 'Fahmy Izwan',
				'user_nric_visa' 	  => '9X1X21XXXX',
				'email' 		  	  => 'mfahmyizwan@gmail.com',
				'user_contact_no' 	  => '0189031045',
				'user_gender' 		  => '1',
				'user_dob' 		  	  => '1995-11-24',
				'user_username' 	  => 'superadmin',
				'user_password' 	  => password_hash('1234qwer', PASSWORD_DEFAULT),
				'user_status' 		  => '1',
				'created_at'		  => timestamp(),
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
