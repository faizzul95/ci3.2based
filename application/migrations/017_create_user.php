<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_user extends CI_Migration
{
	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
		$this->table_name = 'users';
	}

	public function up()
	{
		$this->dbforge->add_field([
			'id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
			'name' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'user_preferred_name' => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => TRUE, 'comment' => ''],
			'user_staff_no' => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => TRUE, 'comment' => ''],
			'user_nric_visa' => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => TRUE, 'comment' => ''],
			'email' => ['type' => 'VARCHAR', 'constraint' => '200', 'null' => TRUE, 'comment' => ''],
			'user_contact_no' => ['type' => 'VARCHAR', 'constraint' => '15', 'null' => TRUE, 'comment' => ''],
			'user_gender' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'comment' => '1-Male, 2-Female'],
			'user_dob' => ['type' => 'DATE', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'username' => ['type' => 'VARCHAR', 'constraint' => '15', 'null' => TRUE, 'comment' => ''],
			'password' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'user_marital_status' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => 1, 'comment' => '1-Single, 2-Merried, 3-Divorce, 4-Others'],
			'user_status' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => 4, 'comment' => '0-Inactive, 1-Active, 2-Suspended, 3-Deleted, 4-Unverified'],
			'social_id' => ['type' => 'VARCHAR', 'constraint' => '125', 'null' => TRUE, 'default' => NULL, 'comment' => ''],
			'social_type' => ['type' => 'VARCHAR', 'constraint' => '125', 'null' => TRUE, 'default' => NULL, 'comment' => ''],
			'two_factor_status' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => 0, 'comment' => '0-Disable, 1-Enable'],
			'two_factor_type' => ['type' => 'TINYINT', 'constraint' => '1', 'null' => TRUE, 'default' => 0, 'comment' => '0-Disabled, 1-Google Authenticator, 2-SMS (OTP), 3-Email'],
			'two_factor_secret' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'two_factor_recovery_codes' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'remember_token' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'joined_date' => ['type' => 'DATE', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'resigned_date' => ['type' => 'DATE', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'resigned_reason' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE, 'comment' => ''],
			'email_verified_at' => ['type' => 'DATE', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'company_id' => ['type' => 'BIGINT', 'unsigned' => TRUE, 'null' => TRUE, 'comment' => 'Refer table company'],
			'created_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'updated_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
			'deleted_at' => ['type' => 'TIMESTAMP', 'constraint' => NULL, 'null' => TRUE, 'comment' => ''],
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
		$data = [
			[
				'name'	  			  => 'PMO SUPERADMIN (OWNER)',
				'user_preferred_name' => 'PMO-SU',
				'user_staff_no' 	  => 'SUPERADMIN',
				'user_nric_visa' 	  => NULL,
				'email' 		  	  => 'faizzul14@gmail.com',
				'user_contact_no' 	  => NULL,
				'user_gender' 		  => '1',
				'user_dob' 		  	  => '1995-05-14',
				'username' 	  		  => 'superadmin',
				'password' 	  		  => password_hash('1234qwer', PASSWORD_DEFAULT),
				'user_status' 		  => '1',
				'user_marital_status' => '1',
				'company_id' 		  => '1',
				'joined_date'		  => currentDate(),
				'created_at'		  => timestamp(),
			],
			[
				'name'	  			  => 'MOHD FAHMY IZWAN BIN ZULKHAFRI',
				'user_preferred_name' => 'Fahmy',
				'user_staff_no' 	  => 'CTIE|009',
				'user_nric_visa' 	  => '950514025299',
				'email' 		  	  => 'mfahmyizwan@gmail.com',
				'user_contact_no' 	  => '0189031045',
				'user_gender' 		  => '1',
				'user_dob' 		  	  => '1995-05-14',
				'username' 	  		  => 'fahmy',
				'password' 	  		  => password_hash('1234qwer', PASSWORD_DEFAULT),
				'user_status' 		  => '1',
				'user_marital_status' => '2',
				'company_id' 		  => '2',
				'joined_date'		  => NULL,
				'created_at'		  => timestamp(),
			],
			[
				'name'	  			  => 'MOHAMAD AMIRUL HILMI BIN ZAKARIA',
				'user_preferred_name' => 'Amirul',
				'user_staff_no' 	  => 'CTIE|010',
				'user_nric_visa' 	  => '951116115533',
				'email' 		  	  => 'amirulhilmi16@gmail.com',
				'user_contact_no' 	  => '0145060237',
				'user_gender' 		  => '1',
				'user_dob' 		  	  => '1995-11-16',
				'username' 	  		  => 'amirul',
				'password' 	  		  => password_hash('1234qwer', PASSWORD_DEFAULT),
				'user_status' 		  => '1',
				'user_marital_status' => '1',
				'company_id' 		  => '2',
				'joined_date'		  => NULL,
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
